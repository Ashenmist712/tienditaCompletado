<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\Payments\OrderPaymentStatusService;
use App\Services\Payments\PayPalPaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function start(
        Request $request,
        string $provider,
        PayPalPaymentService $paypal,
    ): JsonResponse {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone_code' => ['required', 'string', 'regex:/^\+[1-9][0-9]{0,2}$/'],
            'phone_number' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'shipping_street' => ['required', 'string', 'max:150'],
            'shipping_exterior_number' => ['required', 'string', 'max:30'],
            'shipping_interior_number' => ['nullable', 'string', 'max:30'],
            'shipping_neighborhood' => ['required', 'string', 'max:120'],
            'shipping_postal_code' => ['required', 'string', 'regex:/^[0-9]{5}$/'],
            'shipping_municipality' => ['required', 'string', 'max:120'],
            'shipping_state' => ['required', 'string', 'max:100'],
            'shipping_references' => ['nullable', 'string', 'max:1000'],
        ]);
        $validated['telefono'] = $validated['phone_code'].$validated['phone_number'];
        $quantities = $this->cartQuantities($request->session()->get('cart', []));
        $order = DB::transaction(function () use ($request, $validated, $quantities, $provider): Order {
            $products = Product::query()
                ->whereIn('id', array_keys($quantities))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($quantities)) {
                throw ValidationException::withMessages([
                    'cart' => 'Uno o más productos ya no están disponibles.',
                ]);
            }

            $existingOrder = $this->reusablePendingOrder($request, $validated, $quantities, $products);
            if ($existingOrder) {
                return $existingOrder;
            }

            $totalCents = 0;
            foreach ($quantities as $productId => $quantity) {
                $priceCents = $this->amountToCents((string) $products[$productId]->precio);

                if ($priceCents <= 0) {
                    throw ValidationException::withMessages([
                        'cart' => 'Uno o más productos tienen un precio no válido.',
                    ]);
                }

                $totalCents += $priceCents * $quantity;
            }

            $order = Order::create([
                'user_id' => $request->user()?->id,
                'total' => number_format($totalCents / 100, 2, '.', ''),
                'estado' => 'pendiente',
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_provider' => $provider,
                'customer_name' => trim($validated['nombre'].' '.$validated['apellidos']),
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['telefono'],
                'shipping_street' => $validated['shipping_street'],
                'shipping_exterior_number' => $validated['shipping_exterior_number'],
                'shipping_interior_number' => $validated['shipping_interior_number'] ?? null,
                'shipping_neighborhood' => $validated['shipping_neighborhood'],
                'shipping_postal_code' => $validated['shipping_postal_code'],
                'shipping_municipality' => $validated['shipping_municipality'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_references' => $validated['shipping_references'] ?? null,
            ]);

            foreach ($quantities as $productId => $quantity) {
                $order->details()->create([
                    'product_id' => $productId,
                    'cantidad' => $quantity,
                    'precio' => $products[$productId]->precio,
                ]);
            }

            return $order;
        });

        $request->session()->put('pending_payment_order_id', $order->id);

        $successUrl = URL::temporarySignedRoute(
            'payments.success',
            $order->created_at->copy()->addDay(),
            ['order' => $order->id],
        );
        $paypalOrderId = $order->payment_reference;

        if (! $paypalOrderId) {
            $paypalOrder = $paypal->createOrder($order, $successUrl);
            $paypalOrderId = $paypalOrder->getId();
            $order->update(['payment_reference' => $paypalOrderId]);
        }

        return response()->json([
            'order_id' => $paypalOrderId,
            'capture_url' => URL::temporarySignedRoute(
                'payments.paypal.capture',
                now()->addHour(),
                ['order' => $order->id],
            ),
            'success_url' => $successUrl,
        ]);
    }

    public function capturePaypal(
        Request $request,
        Order $order,
        PayPalPaymentService $paypal,
        OrderPaymentStatusService $status,
    ): JsonResponse {
        $validated = $request->validate([
            'paypal_order_id' => ['required', 'string', 'max:36', 'regex:/^[A-Z0-9]+$/'],
        ]);

        abort_unless(
            $order->payment_provider === 'paypal'
                && hash_equals((string) $order->payment_reference, $validated['paypal_order_id']),
            403,
            'The PayPal order does not match this checkout.',
        );

        if ($order->payment_status === Order::PAYMENT_PAID) {
            return response()->json([
                'success_url' => URL::temporarySignedRoute(
                    'payments.success',
                    now()->addDay(),
                    ['order' => $order->id],
                ),
            ]);
        }

        abort_unless($order->payment_status === Order::PAYMENT_PENDING, 409, 'This order is not pending payment.');

        $capturedOrder = $paypal->captureOrder($validated['paypal_order_id'], $order->id);

        abort_unless($paypal->hasValidCapture($capturedOrder, $order), 409, 'PayPal has not confirmed the expected payment.');

        $status->markPaid($order->id, 'paypal', $validated['paypal_order_id']);

        return response()->json([
            'success_url' => URL::temporarySignedRoute(
                'payments.success',
                now()->addDay(),
                ['order' => $order->id],
            ),
        ]);
    }

    public function success(Request $request, string $order): View
    {
        abort_unless($request->hasValidSignatureWhileIgnoring([
            'token',
            'PayerID',
        ]), 403);

        $purchase = Order::query()
            ->with('details.product')
            ->findOrFail($order);

        if ($purchase->payment_status === Order::PAYMENT_PAID
            && (int) $request->session()->get('pending_payment_order_id') === $purchase->id
            && $this->sessionCartMatchesOrder($request->session()->get('cart', []), $purchase)) {
            $request->session()->forget(['cart', 'pending_payment_order_id']);
        }

        return view('shop.success', ['order' => $purchase]);
    }

    /**
     * @return array<int, int>
     */
    private function cartQuantities(mixed $cart): array
    {
        if (! is_array($cart) || $cart === []) {
            throw ValidationException::withMessages([
                'cart' => 'Tu carrito está vacío.',
            ]);
        }

        $quantities = [];

        foreach ($cart as $productId => $item) {
            $quantity = is_array($item)
                ? filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT)
                : false;

            if (! ctype_digit((string) $productId) || $quantity === false || $quantity < 1) {
                throw ValidationException::withMessages([
                    'cart' => 'El carrito contiene productos o cantidades no válidas.',
                ]);
            }

            $quantities[(int) $productId] = $quantity;
        }

        return $quantities;
    }

    private function amountToCents(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '0');

        return ((int) $whole * 100) + (int) str_pad(substr($fraction, 0, 2), 2, '0');
    }

    /**
     * Reuse the current session's pending order when the cart and checkout data have not changed.
     *
     * @param  array<string, mixed>  $validated
     * @param  array<int, int>  $quantities
     * @param  Collection<int, Product>  $products
     */
    private function reusablePendingOrder(
        Request $request,
        array $validated,
        array $quantities,
        Collection $products,
    ): ?Order {
        $orderId = $request->session()->get('pending_payment_order_id');
        if (! is_numeric($orderId)) {
            return null;
        }

        $order = Order::query()->with('details')->find($orderId);

        if (! $order
            || $order->payment_status !== Order::PAYMENT_PENDING
            || $order->created_at->lessThan(now()->subDay())
            || $order->payment_provider !== $request->route('provider')
            || $order->customer_name !== trim($validated['nombre'].' '.$validated['apellidos'])
            || $order->customer_email !== $validated['email']
            || $order->customer_phone !== $validated['telefono']
            || $order->shipping_street !== $validated['shipping_street']
            || $order->shipping_exterior_number !== $validated['shipping_exterior_number']
            || $order->shipping_interior_number !== ($validated['shipping_interior_number'] ?? null)
            || $order->shipping_neighborhood !== $validated['shipping_neighborhood']
            || $order->shipping_postal_code !== $validated['shipping_postal_code']
            || $order->shipping_municipality !== $validated['shipping_municipality']
            || $order->shipping_state !== $validated['shipping_state']
            || $order->shipping_references !== ($validated['shipping_references'] ?? null)) {
            return null;
        }

        $existingQuantities = $order->details
            ->mapWithKeys(fn ($detail): array => [(int) $detail->product_id => (int) $detail->cantidad])
            ->all();
        ksort($existingQuantities);
        ksort($quantities);

        if ($existingQuantities !== $quantities) {
            return null;
        }

        $totalCents = 0;
        foreach ($order->details as $detail) {
            $product = $products[$detail->product_id];
            if ($this->amountToCents((string) $detail->precio) !== $this->amountToCents((string) $product->precio)) {
                return null;
            }

            $totalCents += $this->amountToCents((string) $product->precio) * (int) $detail->cantidad;
        }

        return $totalCents === $this->amountToCents((string) $order->total) ? $order : null;
    }

    private function sessionCartMatchesOrder(mixed $cart, Order $order): bool
    {
        if (! is_array($cart)) {
            return false;
        }

        $cartQuantities = [];
        foreach ($cart as $productId => $item) {
            $quantity = is_array($item)
                ? filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT)
                : false;

            if (! ctype_digit((string) $productId) || $quantity === false || $quantity < 1) {
                return false;
            }

            $cartQuantities[(int) $productId] = $quantity;
        }

        $orderQuantities = $order->details
            ->mapWithKeys(fn ($detail): array => [(int) $detail->product_id => (int) $detail->cantidad])
            ->all();
        ksort($cartQuantities);
        ksort($orderQuantities);

        return $cartQuantities === $orderQuantities;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payments\OrderPaymentStatusService;
use App\Services\Payments\PayPalPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function paypal(
        Request $request,
        PayPalPaymentService $paypal,
        OrderPaymentStatusService $status,
    ) {
        $event = $request->json()->all();
        $headers = [
            'auth_algo' => $request->header('PayPal-Auth-Algo'),
            'cert_url' => $request->header('PayPal-Cert-Url'),
            'transmission_id' => $request->header('PayPal-Transmission-Id'),
            'transmission_sig' => $request->header('PayPal-Transmission-Sig'),
            'transmission_time' => $request->header('PayPal-Transmission-Time'),
        ];

        if (! $paypal->verifyWebhookSignature($headers, $event)) {
            Log::warning('Rejected PayPal webhook with invalid signature.', [
                'transmission_id' => $headers['transmission_id'],
            ]);

            return response()->json(['message' => 'Invalid webhook signature.'], 401);
        }

        $eventType = $event['event_type'] ?? null;
        $resource = $event['resource'] ?? [];
        $paypalOrderId = data_get($resource, 'supplementary_data.related_ids.order_id');

        if (! in_array($eventType, ['PAYMENT.CAPTURE.COMPLETED', 'PAYMENT.CAPTURE.DENIED'], true)
            || ! is_string($paypalOrderId)
            || $paypalOrderId === '') {
            return response()->noContent();
        }

        $order = Order::query()
            ->where('payment_provider', 'paypal')
            ->where('payment_reference', $paypalOrderId)
            ->first();

        if (! $order) {
            return response()->noContent();
        }

        if ($eventType === 'PAYMENT.CAPTURE.DENIED') {
            $status->markFailed($order->id, 'paypal', $paypalOrderId);

            return response()->noContent();
        }

        $verifiedOrder = $paypal->getOrder($paypalOrderId);

        if (! $paypal->hasValidCapture($verifiedOrder, $order)) {
            Log::warning('PayPal capture webhook did not match the local order.', [
                'order_id' => $order->id,
                'event_id' => $event['id'] ?? null,
            ]);

            return response()->json(['message' => 'Payment does not match the order.'], 422);
        }

        $status->markPaid($order->id, 'paypal', $paypalOrderId);

        return response()->noContent();
    }
}

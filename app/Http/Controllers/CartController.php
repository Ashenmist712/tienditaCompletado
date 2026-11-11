<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $recommended = Product::inRandomOrder()->take(4)->get();

        return view('cart.index', compact('cart', 'recommended'));
    }

    public function add(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->nombre,
                'quantity' => 1,
                'price' => $product->precio,
                'image' => $product->imagen,
            ];
        }

        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Producto agregado al carrito exitosamente')->with('open_cart', true);
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = $request->session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                $request->session()->put('cart', $cart);
            }

            return redirect()->back()->with('success', 'Producto eliminado')->with('open_cart', true);
        }
    }

    public function checkout(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (! is_array($cart) || $cart === []) {
            return redirect()->route('shop.index')->with('error', 'Tu carrito está vacío.');
        }

        $productIds = array_filter(array_keys($cart), fn ($id): bool => ctype_digit((string) $id));
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');
        $currentCart = [];

        foreach ($cart as $productId => $item) {
            $product = $products->get($productId);
            $quantity = is_array($item)
                ? filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT)
                : false;

            if (! ctype_digit((string) $productId) || ! $product || $quantity === false || $quantity < 1) {
                $request->session()->put('cart', $currentCart);

                return redirect()->route('cart.index')->with('error', 'El carrito cambió; revisa los productos y sus cantidades.');
            }

            $currentCart[$productId] = [
                'name' => $product->nombre,
                'quantity' => $quantity,
                'price' => $product->precio,
                'image' => $product->imagen,
            ];
        }

        $request->session()->put('cart', $currentCart);

        return view('cart.checkout', ['cart' => $currentCart]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->with(['user', 'details.product'])
            ->latest()
            ->get();
        $summary = [
            'total' => $orders->count(),
            'pending' => $orders->where('estado', 'pendiente')->count(),
            'processing' => $orders->where('estado', 'procesando')->count(),
            'paid' => $orders->where('payment_status', Order::PAYMENT_PAID)->count(),
            'paid_total' => $orders
                ->where('payment_status', Order::PAYMENT_PAID)
                ->sum('total'),
        ];

        return view('admin.orders.index', compact('orders', 'summary'));
    }

    public function show(Order $pedido)
    {
        // En Laravel las rutas de recurso resuelven automáticamente con 'pedido' porque en web.php definimos resource('pedidos')
        $pedido->load('details.product', 'user');
        return view('admin.orders.show', compact('pedido'));
    }

    public function update(Request $request, Order $pedido)
    {
        $validated = $request->validate([
            'estado' => 'required|string|in:pendiente,procesando,completado,cancelado'
        ]);

        $pedido->update($validated);
        return redirect()->back()->with('success', 'Estado del pedido actualizado');
    }

    public function destroy(Order $pedido)
    {
        Order::destroy($pedido->id);
        return redirect()->route('admin.pedidos.index')->with('success', 'Pedido eliminado');
    }
}

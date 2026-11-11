@extends('layouts.app')

@section('content')
    <section class="payment-result">
        @if ($order->payment_status === \App\Models\Order::PAYMENT_PAID)
            <h1>¡Gracias por tu compra!</h1>
            <p>El pago de tu pedido fue confirmado.</p>
        @elseif ($order->payment_status === \App\Models\Order::PAYMENT_FAILED)
            <h1>No se pudo confirmar el pago</h1>
            <p>El pago de este pedido fue rechazado. Puedes volver a la tienda para intentarlo de nuevo.</p>
        @else
            <h1>Estamos confirmando tu pago</h1>
            <p>Tu pedido se creó correctamente y está pendiente de confirmación del proveedor. Actualiza esta página en un momento.</p>
        @endif

        <p class="payment-result-order">Pedido #{{ $order->id }}</p>
        <p>Total: ${{ number_format((float) $order->total, 2) }} {{ config('services.payments.currency') }}</p>

        <div class="payment-result-items">
            @foreach ($order->details as $detail)
                <p>{{ $detail->product?->nombre ?? 'Producto' }} × {{ $detail->cantidad }}</p>
            @endforeach
        </div>

        <a class="btn-primary" href="{{ route('shop.index') }}">Volver a la tienda</a>
    </section>
@endsection

@push('styles')
    <style>
        .payment-result {
            background: var(--surface-color);
            border: 1px solid var(--surface-border);
            border-radius: 20px;
            margin: 4rem auto;
            max-width: 720px;
            padding: 3rem;
            text-align: center;
        }

        .payment-result h1 {
            margin-bottom: 1rem;
        }

        .payment-result-order,
        .payment-result-items {
            margin: 1.5rem 0;
        }

        .payment-result .btn-primary {
            margin-top: 1rem;
        }
    </style>
@endpush

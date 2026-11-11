<?php

namespace App\Services\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderPaymentStatusService
{
    public function markPaid(int $orderId, string $provider, string $reference): Order
    {
        return DB::transaction(function () use ($orderId, $provider, $reference): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);

            if ($order->payment_provider !== $provider) {
                abort(409, 'Payment provider does not match the order.');
            }

            if (in_array($order->payment_status, [Order::PAYMENT_PENDING, Order::PAYMENT_FAILED], true)) {
                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'payment_reference' => $reference,
                ]);
            }

            return $order->refresh();
        });
    }

    public function markFailed(int $orderId, string $provider, string $reference): Order
    {
        return DB::transaction(function () use ($orderId, $provider, $reference): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);

            if ($order->payment_provider !== $provider) {
                abort(409, 'Payment provider does not match the order.');
            }

            if ($order->payment_status === Order::PAYMENT_PENDING) {
                $order->update([
                    'payment_status' => Order::PAYMENT_FAILED,
                    'payment_reference' => $reference,
                ]);
            }

            return $order->refresh();
        });
    }
}

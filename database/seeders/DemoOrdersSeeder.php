<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DemoOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::query()->orderBy('id')->get();

        if ($products->isEmpty()) {
            throw new RuntimeException('Create at least one product before seeding demo orders.');
        }

        $samples = [
            ['pendiente', 'pending'],
            ['procesando', 'paid'],
            ['completado', 'paid'],
            ['pendiente', 'paid'],
            ['cancelado', 'failed'],
            ['procesando', 'paid'],
            ['completado', 'paid'],
            ['pendiente', 'pending'],
        ];
        $customers = [
            ['Ana Torres', 'ana.torres@example.test'],
            ['Luis Mendoza', 'luis.mendoza@example.test'],
            ['María Ríos', 'maria.rios@example.test'],
            ['Diego Cruz', 'diego.cruz@example.test'],
            ['Sofía Vargas', 'sofia.vargas@example.test'],
            ['Pablo Flores', 'pablo.flores@example.test'],
            ['Elena Castillo', 'elena.castillo@example.test'],
            ['Jorge Navarro', 'jorge.navarro@example.test'],
        ];
        $addresses = [
            ['Centro', 'Ciudad de México', 'Ciudad de México', '06000'],
            ['Del Valle', 'Benito Juárez', 'Ciudad de México', '03100'],
            ['Americana', 'Guadalajara', 'Jalisco', '44160'],
            ['Centro', 'Monterrey', 'Nuevo León', '64000'],
            ['Jardines', 'Puebla', 'Puebla', '72530'],
            ['Obrera', 'León', 'Guanajuato', '37340'],
            ['Reforma', 'Oaxaca de Juárez', 'Oaxaca', '68050'],
            ['Centro', 'Mérida', 'Yucatán', '97000'],
        ];

        foreach ($samples as $index => [$orderStatus, $paymentStatus]) {
            DB::transaction(function () use ($index, $orderStatus, $paymentStatus, $customers, $addresses, $products): void {
                $product = $products[$index % $products->count()];
                $quantity = ($index % 3) + 1;
                $total = number_format((float) $product->precio * $quantity, 2, '.', '');
                $orderNumber = $index + 1;
                $createdAt = now()->subDays($index * 2)->subHours($index);

                $order = Order::firstOrCreate(
                    ['payment_reference' => sprintf('DEMO-ORDER-%04d', $orderNumber)],
                    [
                        'user_id' => null,
                        'total' => $total,
                        'estado' => $orderStatus,
                        'payment_status' => $paymentStatus,
                        'payment_provider' => 'paypal',
                        'customer_name' => $customers[$index][0],
                        'customer_email' => $customers[$index][1],
                        'customer_phone' => sprintf('+52 55 0000 %04d', $orderNumber),
                        'shipping_street' => 'Calle de Prueba',
                        'shipping_exterior_number' => (string) (100 + $orderNumber),
                        'shipping_neighborhood' => $addresses[$index][0],
                        'shipping_postal_code' => $addresses[$index][3],
                        'shipping_municipality' => $addresses[$index][1],
                        'shipping_state' => $addresses[$index][2],
                        'shipping_references' => 'Registro ficticio de demostración.',
                    ],
                );

                if ($order->wasRecentlyCreated) {
                    $order->forceFill([
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ])->save();

                    $order->details()->create([
                        'product_id' => $product->id,
                        'cantidad' => $quantity,
                        'precio' => $product->precio,
                    ]);
                }
            });
        }
    }
}

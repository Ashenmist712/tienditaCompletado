<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalProducts = Product::query()->count('id');
        $totalOrders = Order::query()->count('id');
        $totalUsers = User::query()->count('id');
        $recentOrders = Order::query()->latest()->take(5)->get();

        $periodo = $request->get('periodo', 'mes');

        if (! in_array($periodo, ['hoy', 'mes', 'anio'], true)) {
            $periodo = 'mes';
        }

        [$labelsDinero, $dataDinero, $dataVentas] = match ($periodo) {
            'hoy' => [
                ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00'],
                [500, 1200, 800, 2500, 1800, 3100, 1400],
                [2, 5, 3, 9, 6, 11, 4],
            ],
            'anio' => [
                ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                [45000, 52000, 61000, 58000, 72000, 68000, 85000, 79000, 92000, 88000, 105000, 118000],
                [45, 52, 61, 58, 72, 68, 85, 79, 92, 88, 105, 118],
            ],
            default => [
                ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'],
                [15000, 22000, 18500, 31000],
                [18, 26, 22, 37],
            ],
        };

        $labelsProductos = [
            'Extintor PQS 6kg',
            'Manguera Gabinete 15m',
            'Detector de Humo',
            'Válvula Angular',
            'Gabinete Rojo',
        ];
        $dataProductos = [45, 30, 25, 18, 12];

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalUsers',
            'recentOrders',
            'labelsDinero',
            'dataDinero',
            'dataVentas',
            'labelsProductos',
            'dataProductos',
            'periodo'
        ));
    }
}

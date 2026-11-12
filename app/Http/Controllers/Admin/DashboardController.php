<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalCategories = Category::query()->count();
        $totalProducts = Product::query()->count();
        $totalOrders = Order::query()->count();
        $totalUsers = User::query()->count();
        $recentOrders = Order::query()->latest()->take(5)->get();

        $periodo = $request->get('periodo', 'mes');

        if (! in_array($periodo, ['hoy', 'mes', 'anio'], true)) {
            $periodo = 'mes';
        }

        $buckets = match ($periodo) {
            'hoy' => collect(range(0, 5))->map(function (int $index): array {
                $start = now()->startOfDay()->addHours($index * 4);
                $end = $start->copy()->addHours(4);

                return [$start->format('H:i').'–'.$end->copy()->subMinute()->format('H:i'), $start, $end];
            }),
            'anio' => collect(range(0, 11))->map(function (int $index): array {
                $start = now()->startOfYear()->addMonths($index);

                return [$start->translatedFormat('M'), $start, $start->copy()->addMonth()];
            }),
            default => collect(range(0, (int) ceil(now()->daysInMonth / 7) - 1))->map(function (int $index): array {
                $monthStart = now()->startOfMonth();
                $start = $monthStart->copy()->addDays($index * 7);
                $end = $start->copy()->addDays(7);
                $monthEnd = $monthStart->copy()->addMonth();
                $end = $end->greaterThan($monthEnd) ? $monthEnd : $end;

                return ['Semana '.($index + 1), $start, $end];
            }),
        };

        $labelsDinero = [];
        $dataDinero = [];
        $dataVentas = [];

        foreach ($buckets as [$label, $start, $end]) {
            $totals = Order::query()
                ->where('payment_status', Order::PAYMENT_PAID)
                ->where('created_at', '>=', $start)
                ->where('created_at', '<', $end)
                ->selectRaw('COALESCE(SUM(total), 0) as revenue, COUNT(*) as order_count')
                ->first();

            $labelsDinero[] = $label;
            $dataDinero[] = (float) $totals->revenue;
            $dataVentas[] = (int) $totals->order_count;
        }

        $topProducts = OrderDetail::query()
            ->selectRaw('product_id, SUM(cantidad) as total_units')
            ->whereHas('order', fn ($query) => $query->where('payment_status', Order::PAYMENT_PAID))
            ->with('product:id,nombre')
            ->groupBy('product_id')
            ->orderByDesc('total_units')
            ->limit(5)
            ->get();
        $labelsProductos = $topProducts
            ->map(fn (OrderDetail $detail) => $detail->product?->nombre ?? 'Producto eliminado')
            ->all();
        $dataProductos = $topProducts->pluck('total_units')->map(fn ($units) => (int) $units)->all();

        return view('admin.dashboard', compact(
            'totalCategories',
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

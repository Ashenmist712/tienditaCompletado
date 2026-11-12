@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Resumen de la Tienda</h1>
    <p class="text-gray-500">Bienvenido al centro de administración de materiales contra incendios.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    
    <a href="{{ route('admin.categories.index') }}" class="block no-underline">
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <h3 class="text-gray-500 text-sm font-bold uppercase">Categorías</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalCategories }}</p>
        </div>
        <div class="text-blue-500 opacity-70">
            <i class="fas fa-folder-open text-4xl"></i>
        </div>
    </div>
    </a>

    <a href="{{ route('admin.products.index') }}" class="block no-underline">
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500 flex items-center justify-between">
        <div>
            <h3 class="text-gray-500 text-sm font-bold uppercase">Productos</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
        </div>
        <div class="text-green-500 opacity-70">
            <i class="fas fa-box text-4xl"></i>
        </div>
    </div>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="block no-underline">
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500 flex items-center justify-between">
        <div>
            <h3 class="text-gray-500 text-sm font-bold uppercase">Pedidos</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalOrders }}</p>
        </div>
        <div class="text-yellow-500 opacity-70">
            <i class="fas fa-shopping-cart text-4xl"></i>
        </div>
    </div>
    </a>

    <a href="{{ route('admin.users.index') }}" class="block no-underline">
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500 flex items-center justify-between">
        <div>
            <h3 class="text-gray-500 text-sm font-bold uppercase">Usuarios</h3>
            <p class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
        </div>
        </a>
        <div class="text-purple-500 opacity-70">
            <i class="fas fa-users text-4xl"></i>
        </div>
    </div>
    
</div>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    crossorigin="anonymous"
>

<section class="mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Análisis de Ventas</h4>

        <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2">
            <label for="periodo" class="form-label mb-0">Periodo:</label>
            <select id="periodo" name="periodo" class="form-select form-select-sm">
                <option value="hoy" @selected($periodo === 'hoy')>Hoy</option>
                <option value="mes" @selected($periodo === 'mes')>Este Mes</option>
                <option value="anio" @selected($periodo === 'anio')>Este Año</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
        </form>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Ingresos pagados por periodo ($)</div>
                <div class="card-body p-3">
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartDinero" aria-label="Gráfico de flujo de ingresos" role="img"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Unidades vendidas por producto (pagadas)</div>
                <div class="card-body p-3">
                    @if(count($labelsProductos) > 0)
                    <div style="position: relative; height: 320px;">
                        <canvas id="chartVentas" aria-label="Gráfico resumen de pedidos y productos" role="img"></canvas>
                    </div>
                    @else
                    <div class="d-flex flex-column align-items-center justify-content-center text-center text-muted" style="height: 320px;">
                        <i class="fas fa-chart-pie fa-2x mb-3" aria-hidden="true"></i>
                        <p class="mb-0">Todavía no hay productos en pedidos pagados.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const incomeLabels = @json($labelsDinero, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    const incomeData = @json($dataDinero ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    const productLabels = @json($labelsProductos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    const productData = @json($dataProductos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

    new Chart(document.getElementById('chartDinero'), {
        type: 'line',
        data: {
            labels: incomeLabels,
            datasets: [{
                label: 'Ingresos ($)',
                data: incomeData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.12)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => `$${Number(value).toLocaleString('es-MX')}`
                    }
                }
            }
        }
    });

    @if(count($labelsProductos) > 0)
    new Chart(document.getElementById('chartVentas'), {
        type: 'doughnut',
        data: {
            labels: productLabels,
            datasets: [{
                label: 'Unidades vendidas',
                data: productData,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif
</script>
@endsection
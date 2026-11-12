<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - MSI Tienda</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .admin-sidebar .sidebar-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 6px;
            margin: 0 0.75rem;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        .admin-sidebar .sidebar-link i {
            color: inherit;
            margin-right: 0.75rem;
        }

        .admin-sidebar .sidebar-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .admin-sidebar .sidebar-link.active {
            color: #fff;
            font-weight: 700;
            background-color: rgba(255, 255, 255, 0.12);
            box-shadow: inset 3px 0 0 #60a5fa;
        }
    </style>
</head>
<body class="bg-gray-100 flex min-h-screen">

    <aside class="admin-sidebar w-64 bg-slate-900 text-white flex flex-col">
        <div class="p-6 text-center border-b border-slate-800">
            <h2 class="text-2xl font-bold text-blue-400">MSI Tienda</h2>
            <p class="text-xs text-slate-400 mt-1">Panel de Control</p>
        </div>
        
        <nav class="flex-1 mt-4">
            <a href="{{ route('admin.dashboard') }}" @class(['sidebar-link block py-3 transition', 'active' => request()->routeIs('admin.dashboard')])>
                <i class="fas fa-chart-line w-6"></i>Dashboard
            </a>
            <a href="{{ route('admin.categories.index') }}" @class(['sidebar-link block py-3 transition', 'active' => request()->routeIs('admin.categories.*')])>
                <i class="fas fa-folder-open w-6"></i>Categorías
            </a>
            <a href="{{ route('admin.products.index') }}" @class(['sidebar-link block py-3 transition', 'active' => request()->routeIs('admin.products.*')])>
                <i class="fas fa-box w-6"></i>Productos
            </a>
            <a href="{{ route('admin.orders.index') }}" @class(['sidebar-link block py-3 transition', 'active' => request()->routeIs('admin.orders.*')])>
                <i class="fas fa-shopping-cart w-6"></i>Pedidos
            </a>
            <a href="{{ route('admin.users.index') }}" @class(['sidebar-link block py-3 transition', 'active' => request()->routeIs('admin.users.*')])>
                <i class="fas fa-users w-6"></i>Usuarios
            </a>
        </nav>
        
        <!-- Botón de Salir -->
        <div class="p-4 border-t border-slate-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- Contenido Principal Dinámico -->
    <main class="flex-1 p-8">
        @yield('content')
    </main>

</body>
</html>
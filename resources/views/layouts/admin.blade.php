<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - MSI Tienda</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex min-h-screen">

    <aside class="w-64 bg-slate-900 text-white flex flex-col">
        <div class="p-6 text-center border-b border-slate-800">
            <h2 class="text-2xl font-bold text-blue-400">MSI Tienda</h2>
            <p class="text-xs text-slate-400 mt-1">Panel de Control</p>
        </div>
        
        <nav class="flex-1 mt-4">
            <a href="{{ route('admin.dashboard') }}" class="block py-3 px-6 hover:bg-slate-800 hover:text-blue-400 transition">
                <i class="fas fa-chart-line w-6"></i> Dashboard
            </a>
            <a href="{{ route('admin.categories.index') }}" class="block py-3 px-6 hover:bg-slate-800 hover:text-blue-400 transition">
                <i class="fas fa-folder-open w-6"></i> Categorías
            </a>
            <a href="{{ route('admin.products.index') }}" class="block py-3 px-6 hover:bg-slate-800 hover:text-blue-400 transition">
                <i class="fas fa-box w-6"></i> Productos
            </a>
            <a href="{{ route('admin.orders.index') }}" class="block py-3 px-6 hover:bg-slate-800 hover:text-blue-400 transition">
                <i class="fas fa-shopping-cart w-6"></i> Pedidos
            </a>
            <a href="{{ route('admin.users.index') }}" class="block py-3 px-6 hover:bg-slate-800 hover:text-blue-400 transition">
                <i class="fas fa-users w-6"></i> Usuarios
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSI | Materiales y Servicios Industriales</title>
    <meta name="description" content="Suministramos equipos de seguridad industrial, herramientas mecánicas y eléctricas, y materiales para la industria.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<!-- Enviamos la variable de sesión al JS usando un data-attribute -->
<body data-open-cart="{{ session('open_cart') ? 'true' : 'false' }}">

    <nav class="navbar">
        <a href="/" class="navbar-brand">
            MSI <span>Tienda</span>
        </a>
        <ul class="nav-links">
            <li><a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.index') ? 'active' : '' }}">Inicio</a></li>
            <li><a href="{{ route('pages.about') }}" class="{{ request()->routeIs('pages.about') ? 'active' : '' }}">Nosotros</a></li>
            <li><a href="/#categorias">Categorías</a></li>
            <li><a href="/#productos">Productos</a></li>
        </ul>
        
        <div class="nav-actions">
            
            <!-- Botón del carrito limpio -->
            <a href="#" id="openCartBtn" class="cart-btn" title="Ver Carrito">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                
                @php $cartCount = count(session('cart', [])); @endphp
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>

            @auth
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="btn-primary btn-admin">Panel Admin</a>
                @endif
                
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                    @csrf
                    <button type="submit" class="btn-primary btn-logout">Salir</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-primary">Iniciar Sesión</a>
            @endauth
            
        </div>
    </nav>

    @include('partials.cart_sidebar')

    <main class="main-container">
        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Materiales y Servicios Industriales. Todos los derechos reservados.</p>
    </footer>

    <script src="{{ asset('js/main.js') }}"></script>
    @if(session('open_cart'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof openCartSidebar === 'function') {
                openCartSidebar();
            }
        });
    </script>
    @endif
    @stack('scripts')
</body>
</html>
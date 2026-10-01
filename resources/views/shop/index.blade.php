@extends('layouts.app')

@section('content')


<!-- Hero -->
<section class="hero">
    <div class="hero-bg"></div>
    <div>
        <h1>Materiales contra incendios</h1>
        <p>Equipos de seguridad, herramientas mecánicas e incendiarias con la mejor calidad y servicio. Descubre nuestro catálogo completo.</p>
        <a href="#productos" class="btn-primary" style="animation: fadeUp 1.4s ease-out;">Explorar Productos</a>
    </div>
</section>

<!-- Categorias -->
<div id="categorias">
    <h2 class="section-title">Nuestras Categorías</h2>
    <div class="categories-grid">
        @forelse($categories as $category)
            <a href="#" class="category-card">
                <div class="category-icon">
                    <!-- SVG Placeholder for category icon -->
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                </div>
                <h3>{{ $category->nombre }}</h3>
            </a>
        @empty
            <div class="empty-state">
                <p>No hay categorías registradas aún.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Productos -->
<div id="productos">
    <h2 class="section-title">Productos Destacados</h2>
    <div class="products-grid">
        @forelse($products as $product)
            <div style="position: relative;">
                <a href="{{ route('shop.show', $product->id) }}" class="product-card" style="text-decoration: none; color: inherit;">
                    <div class="product-image">
                        @if($product->imagen)
                            <img src="{{ $product->imagen }}" alt="{{ $product->nombre }}">
                        @endif
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">{{ $product->nombre }}</h3>
                        <p class="product-desc">{{ Str::limit($product->descripcion, 80) }}</p>
                        <div class="product-footer">
                            <span class="product-price">${{ number_format($product->precio, 2) }}</span>
                            <div data-form-target="form-{{ $product->id }}" class="btn-icon" title="Agregar al Carrito">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            </div>
                        </div>
                    </div>
                </a>
                <form id="form-{{ $product->id }}" action="{{ route('cart.add') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                </form>
            </div>
        @empty
            <div class="empty-state">
                <p>No hay productos disponibles por el momento.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection

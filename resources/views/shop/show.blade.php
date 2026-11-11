@extends('layouts.app')

@section('content')


<div class="product-detail-container">
    <div class="product-gallery">
        @if($product->imagen)
            <img src="{{ $product->imagen }}" alt="{{ $product->nombre }}">
        @else
            <span style="color: var(--text-secondary)">Imagen no disponible</span>
        @endif
    </div>
    
    <div class="product-info">
        <a href="{{ route('shop.category', $product->category_id) }}" class="category-badge">
            {{ $product->category->nombre ?? 'Sin Categoría' }}
        </a>
        
        <h1 class="product-title">{{ $product->nombre }}</h1>
        <div class="product-price-large">${{ number_format($product->precio, 2) }}</div>
        
        <p class="product-description">{{ $product->descripcion }}</p>
        
        <div class="stock-status">
            <span class="status-dot {{ $product->stock > 0 ? '' : 'out' }}"></span>
            <span style="color: {{ $product->stock > 0 ? '#2ea043' : '#f85149' }}">
                {{ $product->stock > 0 ? $product->stock . ' disponibles en stock' : 'Agotado' }}
            </span>
        </div>

        <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <button type="submit" class="add-to-cart-btn-large" {{ $product->stock > 0 ? '' : 'disabled' }}>
                <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                {{ $product->stock > 0 ? 'Agregar al carrito' : 'Agotado' }}
            </button>
        </form>

        @if(session('success'))
            <div style="margin-top: 1rem; color: #2ea043; background: rgba(46, 160, 67, 0.1); padding: 1rem; border-radius: 10px; border: 1px solid rgba(46, 160, 67, 0.3);">
                ✅ {{ session('success') }} <a href="{{ route('cart.index') }}" style="color: #58a6ff; font-weight: 600; text-decoration: none; margin-left: 10px;">Ver Carrito &rarr;</a>
            </div>
        @endif
    </div>
</div>

@if($related->count() > 0)
<div class="related-section">
    <h2 class="related-title">Productos Relacionados</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem;">
        @foreach($related as $rel)
            <div style="background: var(--surface-color); border: 1px solid var(--surface-border); border-radius: 16px; overflow: hidden; padding: 1rem; text-align: center;">
                <a href="{{ route('shop.show', $rel->id) }}" style="text-decoration: none; color: inherit;">
                    <img src="{{ $rel->imagen }}" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">{{ $rel->nombre }}</h3>
                    <div style="color: var(--accent-primary); font-weight: 600;">${{ number_format($rel->precio, 2) }}</div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection

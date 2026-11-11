@extends('layouts.admin')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Productos</h1>
        <p class="text-gray-500">Administra el catálogo de materiales contra incendios.</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition flex items-center">
        <i class="fas fa-plus mr-2"></i> Nuevo Producto
    </a>
</div>

<!-- Mensaje de Éxito -->
@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
    <p><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</p>
</div>
@endif

<!-- Tabla de Productos -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Imagen</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($products as $product)
            <tr>
                <!-- Miniatura de la Imagen -->
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($product->imagen)
                        <img src="{{ asset('storage/' . $product->imagen) }}" alt="{{ $product->nombre }}" class="h-12 w-12 object-cover rounded shadow-sm">
                    @else
                        <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center text-gray-400 shadow-sm">
                            <i class="fas fa-image text-xl"></i>
                        </div>
                    @endif
                </td>
                
                <!-- Nombre y Descripción corta -->
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $product->nombre }}</div>
                    <div class="text-xs text-gray-500">{{ Str::limit($product->descripcion, 30) }}</div>
                </td>
                
                <!-- Categoría (Usando la relación) -->
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $product->category->nombre ?? 'Sin Categoría' }}
                    </span>
                </td>
                
                <!-- Precio -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                    ${{ number_format($product->precio, 2) }}
                </td>
                
                <!-- Stock -->
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($product->stock > 10)
                        <span class="text-sm text-green-600 font-medium">{{ $product->stock }} unids.</span>
                    @elseif($product->stock > 0)
                        <span class="text-sm text-yellow-600 font-medium">{{ $product->stock }} unids.</span>
                    @else
                        <span class="text-sm text-red-600 font-medium text-xs">Agotado</span>
                    @endif
                </td>
                
                <!-- Acciones -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-900 mr-3" title="Editar"><i class="fas fa-edit"></i></a>
                    
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                    <i class="fas fa-box-open text-4xl mb-3 text-gray-300"></i>
                    <p>No hay productos registrados en el catálogo.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Paginación -->
    @if($products->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
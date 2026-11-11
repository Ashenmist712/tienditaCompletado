@extends('layouts.admin')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Nuevo Producto</h1>
        <p class="text-gray-500">Añade un nuevo material al catálogo.</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700 transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Volver al catálogo
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden max-w-4xl">
    <!-- El enctype es OBLIGATORIO para poder enviar la imagen -->
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Campo Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre del Producto *</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" 
                    placeholder="Ej. Extintor PQS 4.5kg">
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Campo Categoría -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Categoría *</label>
                <select name="category_id" id="category_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border bg-white">
                    <option value="" disabled selected>Selecciona una categoría...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <!-- Campo Precio -->
            <div>
                <label for="precio" class="block text-sm font-medium text-gray-700 mb-2">Precio ($) *</label>
                <input type="number" step="0.01" min="0" name="precio" id="precio" value="{{ old('precio') }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" 
                    placeholder="0.00">
                @error('precio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Campo Stock -->
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Cantidad en Stock *</label>
                <input type="number" min="0" name="stock" id="stock" value="{{ old('stock') ?? 1 }}" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border">
                @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Campo Descripción -->
        <div class="mb-6">
            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción del Producto</label>
            <textarea name="descripcion" id="descripcion" rows="4" 
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" 
                placeholder="Detalla las características técnicas, certificaciones, etc...">{{ old('descripcion') }}</textarea>
            @error('descripcion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Campo de Imagen -->
        <div class="mb-6">
            <label for="imagen" class="block text-sm font-medium text-gray-700 mb-2">Fotografía del Producto</label>
            <input type="file" name="imagen" id="imagen" accept="image/jpeg,image/png,image/jpg,image/webp"
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
            <p class="text-xs text-gray-500 mt-2">Formatos permitidos: JPG, PNG, WEBP. Tamaño máximo: 2MB.</p>
            @error('imagen') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Botón Guardar -->
        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded shadow transition flex items-center text-lg">
                <i class="fas fa-save mr-2"></i> Guardar Producto
            </button>
        </div>
    </form>
</div>
@endsection
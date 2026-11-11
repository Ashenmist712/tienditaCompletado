@extends('layouts.admin')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Nueva Categoría</h1>
        <p class="text-gray-500">Añade una nueva clasificación para tus productos.</p>
    </div>
    <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700 transition flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Volver a la lista
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden max-w-2xl">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="p-8">
        @csrf

        <!-- Campo Nombre -->
        <div class="mb-6">
            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Categoría *</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" 
                placeholder="Ej. Extintores, Mangueras, Señalización...">
            @error('nombre')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Campo Descripción -->
        <div class="mb-6">
            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción (Opcional)</label>
            <textarea name="descripcion" id="descripcion" rows="4" 
                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" 
                placeholder="Breve descripción de los productos que irán aquí...">{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Botón Guardar -->
        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition">
                <i class="fas fa-save mr-2"></i> Guardar Categoría
            </button>
        </div>
    </form>
</div>
@endsection
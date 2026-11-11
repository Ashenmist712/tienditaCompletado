@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Resumen de la Tienda</h1>
    <p class="text-gray-500">Bienvenido al centro de administración de materiales contra incendios.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <h3 class="text-gray-500 text-sm font-bold uppercase">Categorías</h3>
            <p class="text-3xl font-bold text-gray-800">0</p>
        </div>
        <div class="text-blue-500 opacity-70">
            <i class="fas fa-folder-open text-4xl"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500 flex items-center justify-between">
        <div>
            <h3 class="text-gray-500 text-sm font-bold uppercase">Productos</h3>
            <p class="text-3xl font-bold text-gray-800">0</p>
        </div>
        <div class="text-green-500 opacity-70">
            <i class="fas fa-box text-4xl"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-yellow-500 flex items-center justify-between">
        <div>
            <h3 class="text-gray-500 text-sm font-bold uppercase">Pedidos</h3>
            <p class="text-3xl font-bold text-gray-800">0</p>
        </div>
        <div class="text-yellow-500 opacity-70">
            <i class="fas fa-shopping-cart text-4xl"></i>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500 flex items-center justify-between">
        <div>
            <h3 class="text-gray-500 text-sm font-bold uppercase">Usuarios</h3>
            <p class="text-3xl font-bold text-gray-800">1</p>
        </div>
        <div class="text-purple-500 opacity-70">
            <i class="fas fa-users text-4xl"></i>
        </div>
    </div>
    
</div>
@endsection
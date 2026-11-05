@extends('layouts.admin')

@section('content')
    <div class="admin-dashboard">
        <h1>Bienvenido al Panel de Administración</h1>
        <p>Desde aquí puedes gestionar productos, categorías, pedidos y usuarios.</p>

        <div class="admin-actions">
            <a href="{{ route('admin.products.index') }}" class="btn-admin">Gestionar Productos</a>
            <a href="{{ route('admin.categories.index') }}" class="btn-admin">Gestionar Categorías</a>
            <a href="{{ route('admin.orders.index') }}" class="btn-admin">Ver Pedidos</a>
            <a href="{{ route('admin.users.index') }}" class="btn-admin">Gestionar Usuarios</a>
        </div>
    </div>
@endsection
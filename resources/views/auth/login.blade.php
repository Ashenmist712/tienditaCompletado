@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h2 class="auth-title">Iniciar Sesión</h2>
        <p class="auth-subtitle">Ingresa a tu cuenta para continuar</p>

        <!-- Mostrar error si el correo o contraseña son incorrectos -->
        @if ($errors->any())
            <div class="auth-errors">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="auth-form">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="auth-remember">
                <label>
                    <input type="checkbox" name="remember" id="remember"> Recordarme
                </label>
            </div>

            <button type="submit" class="btn-primary auth-submit">Entrar</button>
            <div style="text-align: center; margin-top: 1.5rem; color: var(--text-secondary);">
                ¿No tienes una cuenta? <a href="{{ route('register') }}" style="color: var(--accent-primary); text-decoration: none; font-weight: 600;">Regístrate aquí</a>
            </div>
        </form>
    </div>
</div>
@endsection
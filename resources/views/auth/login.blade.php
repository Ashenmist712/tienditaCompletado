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
                <div class="password-input-wrapper">
                    <input type="password" name="password" id="password" class="form-control" required>
                    <button
                        type="button"
                        class="password-toggle"
                        id="togglePassword"
                        aria-label="Mostrar contraseña"
                        aria-controls="password"
                        aria-pressed="false"
                    >
                        <svg class="password-toggle-show" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="password-toggle-hide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" hidden>
                            <path d="m3 3 18 18"></path>
                            <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                            <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.8"></path>
                            <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10.7 10.7 0 0 0 4.1-.8"></path>
                        </svg>
                    </button>
                </div>
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
@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card" style="text-align: center;">
        <h2 class="auth-title">¡Revisa tu correo!</h2>
        <p class="auth-subtitle">
            Te hemos enviado un enlace de verificación. Por favor haz clic en él para confirmar tu cuenta.
        </p>

        @if (session('message'))
            <div style="background: rgba(46, 160, 67, 0.1); color: #2ea043; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem;">
                {{ session('message') }}
            </div>
        @endif

        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">¿No recibiste el correo?</p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">Reenviar enlace de verificación</button>
        </form>
    </div>
</div>
@endsection
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Muestra la vista del formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesa el formulario cuando el usuario le da a "Entrar"
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $intendedUrl = $request->session()->get('url.intended');
            $verificationPath = route('verification.verify', [
                'id' => Auth::id(),
                'hash' => sha1(Auth::user()->getEmailForVerification()),
            ], false);
            $intendedHost = is_string($intendedUrl)
                ? parse_url($intendedUrl, PHP_URL_HOST)
                : null;

            if (is_string($intendedUrl)
                && $intendedHost === $request->getHost()
                && parse_url($intendedUrl, PHP_URL_PATH) === $verificationPath) {
                return redirect()->intended(
                    Auth::user()->is_admin ? route('admin.dashboard') : route('shop.index')
                );
            }

            // Redirigir según el rol
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('shop.index')); 
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // Cierra la sesión
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
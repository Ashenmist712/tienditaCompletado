<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->withCount('orders')
            ->with(['orders' => fn ($query) => $query->latest()->limit(5)])
            ->latest()
            ->get();
        $summary = [
            'total' => User::query()->count(),
            'admins' => User::query()->where('is_admin', true)->count(),
            'verified' => User::query()->whereNotNull('email_verified_at')->count(),
            'with_orders' => User::query()->has('orders')->count(),
        ];

        return view('admin.users.index', compact('users', 'summary'));
    }

    public function create()
    {
        return redirect()->route('admin.users.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_admin' => 'nullable|boolean', // Para definir si entra al Admin o a la Tienda
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_admin'] = $request->has('is_admin');

        $user = User::create($validated);
        event(new Registered($user));

        return redirect()->route('admin.users.index')->with(
            'success',
            'Usuario creado. Se generó la notificación de verificación para el correo registrado.',
        );
    }

    public function edit(User $usuario)
    {
        return redirect()->route('admin.users.index');
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'is_admin' => 'nullable|boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $validated['is_admin'] = $request->has('is_admin');
        $emailChanged = $usuario->email !== $validated['email'];

        $updated = DB::transaction(function () use ($usuario, $validated, $request, $emailChanged): bool {
            if ($usuario->is_admin && ! $validated['is_admin']) {
                if ((int) $request->user()->id === (int) $usuario->id) {
                    return false;
                }

                $admins = User::query()
                    ->where('is_admin', true)
                    ->lockForUpdate()
                    ->get(['id']);

                if ($admins->count() <= 1) {
                    return false;
                }
            }

            if ($emailChanged) {
                $usuario->forceFill(['email_verified_at' => null])->save();
            }

            $usuario->update($validated);

            return true;
        });

        if (! $updated) {
            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->withErrors([
                    'is_admin' => (int) $request->user()->id === (int) $usuario->id
                        ? 'No puedes quitarte tu propio acceso de administrador.'
                        : 'No se puede quitar el acceso al último administrador.',
                ]);
        }

        if ($emailChanged) {
            event(new Registered($usuario->fresh()));
        }

        return redirect()->route('admin.users.index')->with(
            'success',
            $emailChanged
                ? 'Usuario actualizado. Se solicitó un nuevo correo de verificación para la dirección actualizada.'
                : 'Usuario actualizado exitosamente',
        );
    }

    public function destroy(User $usuario)
    {
        if ((int) request()->user()->id === (int) $usuario->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta mientras tienes una sesión activa.');
        }

        $deleted = DB::transaction(function () use ($usuario): bool {
            if ($usuario->is_admin) {
                $admins = User::query()
                    ->where('is_admin', true)
                    ->lockForUpdate()
                    ->get(['id']);

                if ($admins->count() <= 1) {
                    return false;
                }
            }

            return (bool) $usuario->delete();
        });

        if (! $deleted) {
            return back()->with('error', 'No se puede eliminar al último administrador.');
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado');
    }
}
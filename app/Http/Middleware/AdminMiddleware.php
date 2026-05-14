<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        $usuario = Auth::user();

        if (! $usuario->rol || strtolower($usuario->rol->nombre) !== 'admin') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->withErrors([
                    'correo' => 'No tienes permisos para acceder al panel administrativo.',
                ]);
        }

        return $next($request);
    }
}
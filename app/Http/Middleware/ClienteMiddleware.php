<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('tienda.auth.login');
        }

        $usuario = Auth::user();

        if (! $usuario->rol) {

            Auth::logout();

            return redirect()
                ->route('tienda.auth.login');

        }

        if (! in_array(
            strtolower($usuario->rol->nombre),
            ['cliente', 'admin']
        )) {

            Auth::logout();

            return redirect()
                ->route('tienda.auth.login');

        }

        return $next($request);
    }
}
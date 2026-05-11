<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function showLogin()
    {
        return view('admin.login.index');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt([
            'correo' => $credenciales['correo'],
            'password' => $credenciales['password'],
            'activo' => 1,
        ], $remember)) {
            return back()
                ->withErrors([
                    'correo' => 'Las credenciales no son correctas.',
                ])
                ->onlyInput('correo');
        }
        $request->session()->regenerate();

        $usuario = Auth::user();

        if (! $usuario->rol || strtolower($usuario->rol->nombre) !== 'admin') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'correo' => 'No tienes permisos para acceder al panel administrativo.',
            ]);
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

}

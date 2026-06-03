<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Favorito;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class TiendaAuthController extends Controller
{

    public function showLogin()
    {
        return view('tienda.login.index');
    }

    public function register(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'correo' => ['required', 'email', 'max:150', 'unique:usuarios,correo'],
            'password' => ['required', 'string', 'min:8'],
        ], [

            'nombre.required' => 'Debes ingresar tu nombre.',

            'correo.required' => 'Debes ingresar un correo.',
            'correo.email' => 'El correo no es válido.',
            'correo.unique' => 'Ya existe una cuenta con este correo.',

            'password.required' => 'Debes ingresar una contraseña.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',

        ]);

        $rolCliente = Rol::where('nombre', 'cliente')->first();

        if (! $rolCliente) {
            return back()->withErrors([
                'correo' => 'No existe el rol cliente en el sistema.',
            ])->withInput();
        }

        $usuario = Usuario::create([
            'nombre' => $datos['nombre'],
            'correo' => $datos['correo'],
            'password' => Hash::make($datos['password']),
            'id_rol' => $rolCliente->id_rol, // cliente
            'activo' => 1,
            'provider' => 'manual',
            'correo_verificado_en' => now(),
        ]);

        Auth::login($usuario);

        $request->session()->regenerate();

        return redirect()
            ->route('tienda.home')
            ->with('success', 'Tu cuenta fue creada correctamente. ¡Bienvenido!');
    }

    public function login(Request $request)
    {
        $credenciales = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $sessionIdInvitado = $request->session()->getId();

        $remember = $request->boolean('remember');

        $usuario = Usuario::where('correo', $credenciales['correo'])->first();

        if ($usuario && ! $usuario->activo) {
            return back()
                ->withErrors(['correo' => 'Tu cuenta está inactiva. Contacta con soporte.'])
                ->onlyInput('correo');
        }

        if (! Auth::attempt([
            'correo' => $credenciales['correo'],
            'password' => $credenciales['password'],
            // 'activo' => 1,
        ], $remember)) {
            return back()
                ->withErrors([
                    'correo' => 'El correo o la contraseña no son correctos.',
                ])
                ->onlyInput('correo');
        }

        $request->session()->regenerate();

        $usuario = Auth::user();

        Favorito::where('session_id', $sessionIdInvitado)
        // ->whereNull('id_usuario')
        ->get()
        ->each(function ($favorito) use ($usuario) {

            $existe = Favorito::where('id_usuario', $usuario->id_usuario)
                ->where('id_producto', $favorito->id_producto)
                ->exists();

            if ($existe) {
                $favorito->delete();
                return;
            }

            $favorito->update([
                'id_usuario' => $usuario->id_usuario,
                'session_id' => null,
            ]);

        });

        if (! $usuario->rol || ! in_array(strtolower($usuario->rol->nombre), ['cliente', 'admin'])) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'correo' => 'Tu usuario no tiene acceso a la tienda.',
            ]);
        }

        return redirect()->route('tienda.home');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tienda.home');
    }
}

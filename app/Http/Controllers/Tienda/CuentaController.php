<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Favorito;
use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CuentaController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();

        $pedidosRecientes = Pedido::where('id_usuario', $usuario->id_usuario)
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        $cantidadPedidos = Pedido::where('id_usuario', $usuario->id_usuario)->count();

        $cantidadFavoritos = Favorito::where('id_usuario', $usuario->id_usuario)->count();

        return view('tienda.cuenta.index', compact(
            'usuario',
            'pedidosRecientes',
            'cantidadPedidos',
            'cantidadFavoritos'
        ));
    }

    public function update(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $datos = $request->validate(
            [
                'nombre' => ['required', 'string', 'max:50'],
                'telefono' => ['nullable', 'string', 'max:30'],
            ],
            [
                /*
                |--------------------------------------------------------------------------
                | NOMBRE
                |--------------------------------------------------------------------------
                */

                'nombre.required' =>
                'Debes ingresar tu nombre.',

                'nombre.max' =>
                'El nombre no puede superar los 120 caracteres.',

                /*
                |--------------------------------------------------------------------------
                | TELÉFONO
                |--------------------------------------------------------------------------
                */

                'telefono.digits_between' =>
                'El teléfono debe tener entre 8 y 30 dígitos.',
            ]
        );

        $usuario->update($datos);

        return back()->with(
            'swal_success',
            'Tus datos fueron actualizados correctamente.'
        );
    }

    public function updatePassword(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $request->validate([
            'password_actual' => ['required'],
            'password' => [
                'required',
                'string',
                'confirmed',
                'different:password_actual',
                Password::min(8)->letters()->numbers(),
            ],
        ], [
            'password_actual.required' => 'Debes ingresar tu contraseña actual.',
            'password.required' => 'Debes ingresar una nueva contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.different' => 'La nueva contraseña no puede ser igual a la actual.',
            'password' => 'La contraseña debe tener mínimo 8 caracteres, incluir letras y números.',
        ]);

        if (!Hash::check($request->password_actual, $usuario->password)) {

            return back()
                ->withErrors([
                    'password_actual' => 'La contraseña actual no es correcta.'
                ])
                ->withInput();
        }

        $usuario->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with(
            'swal_success',
            'Tu contraseña fue actualizada correctamente.'
        );
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\PagoPedido;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class PerfilController extends Controller
{

    public function index()
    {
        $usuario = Auth::user();

        $stats = [
            'pagos_verificados' => PagoPedido::where('id_usuario_verificador', $usuario->id_usuario)->count(),
            'movimientos_realizados' => MovimientoInventario::where('id_usuario_realizador', $usuario->id_usuario)->count(),
            'pedidos_gestionados' => Pedido::whereNotNull('updated_at')->count(),
        ];

        $timeline = collect([
            [
                'icono' => 'bx bx-user-check',
                'titulo' => 'Sesión iniciada',
                'descripcion' => 'Accediste al panel administrativo.',
                'fecha' => now()->format('d/m/Y H:i'),
            ],
            [
                'icono' => 'bx bx-shield-quarter',
                'titulo' => 'Rol activo',
                'descripcion' => 'Actualmente tienes el rol de ' . optional($usuario->rol)->nombre . '.',
                'fecha' => optional($usuario->updated_at)->format('d/m/Y H:i'),
            ],
        ]);

        $sesiones = DB::table('sessions')
            ->where('user_id', $usuario->id_usuario)
            ->orderByDesc('last_activity')
            ->get();

        return view('admin.perfil.index', compact('usuario', 'stats', 'timeline', 'sesiones'));
    }

    public function update(Request $request)
    {
        /** @var Usuario $usuario */
        $usuario = Auth::user();

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'correo' => [
                'required',
                'email',
                'max:150',
                'unique:usuarios,correo,' . $usuario->id_usuario . ',id_usuario',
            ],
            'telefono' => ['nullable', 'string', 'max:30'],
        ]);

        $usuario->update($datos);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $usuario = Auth::user();

        $request->validate([
            'password_actual' => ['required'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($request->password_actual, $usuario->password)) {

            return back()->withErrors([
                'password_actual' => 'La contraseña actual no es correcta.',
            ]);
        }

        /** @var Usuario $usuario */
        $usuario->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}

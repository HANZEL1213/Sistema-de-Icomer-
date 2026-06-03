<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PasswordResetController extends Controller
{
    public function showForgot()
    {
        return view('tienda.login.passwords.email');
    }

    public function sendLink(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if ($usuario) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->correo],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            $url = route('tienda.auth.password.reset', [
                'token' => $token,
                'email' => $request->correo,
            ]);

            Mail::send(
                'tienda.login.emails.password-reset',
                ['url' => $url],
                function ($message) use ($request) {
                    $message->to($request->correo)
                        ->subject('Restablece tu contraseña - Cora CR');
                }
            );
        }

        return back()->with(
            'swal_success',
            'Si el correo existe, recibirás un enlace para restablecer tu contraseña.'
        );
    }

    public function showReset(Request $request, $token)
    {
        return view('tienda.login.passwords.reset', [
            'token' => $token,
            'correo' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'correo' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $registro = DB::table('password_reset_tokens')
            ->where('email', $request->correo)
            ->first();

        if (
            ! $registro ||
            ! Hash::check($request->token, $registro->token) ||
            Carbon::parse($registro->created_at)->addMinutes(60)->isPast()
        ) {
            return back()->withErrors([
                'correo' => 'El enlace no es válido o ya expiró.',
            ]);
        }

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (! $usuario) {
            return back()->withErrors([
                'correo' => 'No se encontró una cuenta con ese correo.',
            ]);
        }

        $usuario->update([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->correo)
            ->delete();

        return redirect()
            ->route('tienda.auth.login')
            ->with('swal_success', 'Tu contraseña fue restablecida correctamente.');
    }
}
<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationToken;
use App\Models\Favorito;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

class TiendaAuthController extends Controller
{

    public function showLogin()
    {
        return view('tienda.login.index');
    }

    public function showRegister()
    {
        return view('tienda.login.register');
    }

    public function register(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'correo' => ['required', 'email', 'max:150', 'unique:usuarios,correo'],
            'password' => ['required', 'string', 'min:8'],
            'g-recaptcha-response' => ['required'],
        ], [

            'nombre.required' => 'Debes ingresar tu nombre.',

            'correo.required' => 'Debes ingresar un correo.',
            'correo.email' => 'El correo no es válido.',
            'correo.unique' => 'Ya existe una cuenta con este correo.',

            'password.required' => 'Debes ingresar una contraseña.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'g-recaptcha-response.required' => 'Confirma que no eres un robot.',

        ]);

         $captcha = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if (! $captcha->json('success')) {
            return back()
                ->withInput()
                ->withErrors([
                    'g-recaptcha-response' => 'No se pudo verificar el CAPTCHA. Inténtalo de nuevo.',
                ]);
        }

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
            'correo_verificado_en' => null,
        ]);

        // Auth::login($usuario);

        // $request->session()->regenerate();

        $this->enviarCorreoVerificacion($usuario);

        return redirect()
            ->route('tienda.auth.login')
            ->with('correo_verificacion',  $usuario->correo);
    }

    private function enviarCorreoVerificacion(Usuario $usuario)
    {
        $token = Str::random(64);

        EmailVerificationToken::updateOrCreate(
            ['correo' => $usuario->correo],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $url = route('tienda.auth.email.verify', [
            'token' => $token,
            'correo' => $usuario->correo,
        ]);

        Mail::send(
            'tienda.login.emails.email-verification',
            [
                'url' => $url,
                'usuario' => $usuario,
            ],
            function ($message) use ($usuario) {
                $message->to($usuario->correo)
                    ->subject('Verifica tu correo - Cora CR');
            }
        );
    }

    public function verifyEmail(Request $request, $token)
    {
        $correo = $request->correo;

        $registro = EmailVerificationToken::where(
            'correo', 
            $correo)->first();

        if (
            ! $registro ||
            ! Hash::check($token, $registro->token) ||
            $registro->created_at->addMinutes(60)->isPast()
        ) {
            return redirect()
                ->route('tienda.auth.login')
                ->with('swal_error', 'El enlace de verificación no es válido o ya expiró.');
        }

        $usuario = Usuario::where('correo', $correo)->firstOrFail();

        if ($usuario->correo_verificado_en) {
            return redirect()
                ->route('tienda.auth.login')
                ->with('swal_success', 'Tu correo ya estaba verificado.');
        }

        $usuario->update([
            'correo_verificado_en' => now(),
        ]);

        $registro->delete();

        return redirect()
            ->route('tienda.auth.login')
            ->with('swal_success', 'Tu correo fue verificado correctamente.');
    }

    public function resendVerification(Request $request)
    {
        $request->validate([
            'correo' => ['required', 'email'],
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if (! $usuario) {
            return back()
                ->withErrors([
                    'correo' => 'No encontramos una cuenta con ese correo.',
                ])
                ->onlyInput('correo');
        }

        if ($usuario->correo_verificado_en) {
            return back()->with(
                'swal_success',
                'Tu correo ya se encuentra verificado.'
            );
        }

        $this->enviarCorreoVerificacion($usuario);

        return back()
            ->with('correo_verificacion', $usuario->correo);
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        if (! $request->has('code')) {
        return redirect()
            ->route('tienda.auth.login')
            ->with('swal_error', 'No se pudo iniciar sesión con Google. Inténtalo nuevamente.');
        }

        $googleUser = Socialite::driver('google')->user();

        $esNuevo = false;

        $usuario = Usuario::where('correo', $googleUser->email)->first();

        if (! $usuario) {

            $esNuevo = true;

            $rolCliente = Rol::where('nombre', 'cliente')->first();

            $usuario = Usuario::create([
                'nombre' => $googleUser->name,
                'correo' => $googleUser->email,

                'password' => bcrypt(Str::random(32)),

                'id_rol' => $rolCliente->id_rol,

                'activo' => 1,

                'provider' => 'google',
                'provider_id' => $googleUser->id,

                'avatar' => $googleUser->avatar,

                'correo_verificado_en' => now(),
            ]);

        } else {

            $usuario->update([
                'provider' => 'google',
                'provider_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
            ]);

        }

        Auth::login($usuario, true);

        if ($esNuevo) {
            Mail::send(
                'tienda.login.emails.welcome',
                ['usuario' => $usuario],
                function ($message) use ($usuario) {
                    $message->to($usuario->correo)
                        ->subject('Bienvenido a Cora CR');
                }
            );
        }

        return redirect()->route('tienda.home');
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

        if ($usuario && is_null($usuario->correo_verificado_en)) {
            return back()
                ->withErrors([
                    'correo' => 'Tu cuenta aún no ha sido verificada. Revisa tu correo para activarla.',
                ])
                ->with('reenviar_verificacion', $usuario->correo)
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

<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PreventsDoubleSubmission
{
    /**
     * Ejecuta el callback protegido contra envíos duplicados
     * mientras la sesión del usuario tenga el lock activo.
     */
    protected function conLockDeSesion(Request $request, string $clave, callable $callback)
    {
        if ($request->session()->get($clave)) {
            return back()
                ->withInput()
                ->withErrors([
                    'general' => 'Ya estamos procesando tu solicitud, espera un momento.',
                ]);
        }

        $request->session()->put($clave, true);

        try {
            return $callback();
        } finally {
            $request->session()->forget($clave);
        }
    }
}
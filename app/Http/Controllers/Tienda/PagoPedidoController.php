<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoPedidoController extends Controller
{
    public function index($codigo)
    {
        $pedido = Pedido::with([
                'pagoUltimo',
                'pagos',
            ])
            ->where('numero_pedido', $codigo)
            ->orWhere('codigo_seguimiento_publico', $codigo)
            ->firstOrFail();

        return view('tienda.pedidos.pago', compact('pedido'));
    }

   public function store(Request $request, $codigo)
{
    $request->validate([
        'numero_comprobante' => 'nullable|string|max:100',
        'comprobante' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    if (
        ! $request->numero_comprobante &&
        ! $request->hasFile('comprobante')
    ) {
        return back()
            ->withInput()
            ->with('error', 'Debes ingresar un código SINPE o subir un comprobante.');
    }

    $pedido = Pedido::with([
            'pagos',
            'pagoUltimo',
        ])
        ->where('numero_pedido', $codigo)
        ->orWhere('codigo_seguimiento_publico', $codigo)
        ->firstOrFail();

    /*
    |--------------------------------------------------------------------------
    | BLOQUEAR PEDIDOS CANCELADOS
    |--------------------------------------------------------------------------
    */
    if ($pedido->estado === 'cancelado') {
        return back()->with(
            'error',
            'Este pedido fue cancelado y ya no permite reenviar pagos.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LÍMITE DE INTENTOS
    |--------------------------------------------------------------------------
    */
    $maxIntentos = 3;

    $intentosActuales = (int) $pedido->pagos()->count();

    if ($intentosActuales >= $maxIntentos) {
        return back()->with(
            'error',
            'Este pedido alcanzó el límite máximo de intentos de pago.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR SI PUEDE VOLVER A ENVIAR
    |--------------------------------------------------------------------------
    */

    if ($pedido->pagoUltimo) {

        /*
        | Si ya hay uno enviado o verificado NO permitir
        */
        if (
            in_array($pedido->pagoUltimo->estado, [
                'enviado',
                'verificado',
            ])
        ) {
            return back()->with(
                'error',
                'Ya existe un pago pendiente de revisión.'
            );
        }

        /*
        | Solo permitir reenviar si fue rechazado
        */
        if ($pedido->pagoUltimo->estado !== 'rechazado') {
            return back()->with(
                'error',
                'Este pedido no permite reenviar pagos.'
            );
        }
    }

    DB::transaction(function () use ($request, $pedido) {

        $pedido->pagos()->update([
            'es_ultimo' => 0,
        ]);

        $rutaComprobante = null;

        if ($request->hasFile('comprobante')) {
            $rutaComprobante = $request->file('comprobante')
                ->store('comprobantes/pedidos', 'public');
        }

        $ultimoIntento = (int) $pedido->pagos()->max('intento');

        $pedido->pagos()->create([
            'metodo' => 'sinpe',
            'numero_comprobante' => $request->numero_comprobante,
            'ruta_comprobante' => $rutaComprobante,
            'monto_reportado' => $pedido->total,
            'moneda' => 'CRC',
            'estado' => 'enviado',
            'intento' => $ultimoIntento + 1,
            'es_ultimo' => 1,
            'enviado_en' => now(),
        ]);

        $pedido->update([
            'estado' => 'en_revision',
        ]);
    });

    return redirect()
        ->route('tienda.pedidos.show', $pedido->numero_pedido)
        ->with(
            'success',
            'Pago enviado correctamente. Ahora está en revisión.'
        );
}
}
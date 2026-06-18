<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PagoRechazadoMail;
use App\Mail\PedidoAprobadoMail;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ProductoVariante;
use Illuminate\Support\Facades\Mail;

class PedidosController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = Pedido::with([
                'usuario',
                'cupon',
                'pagoUltimo',
                'venta',
            ])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.pedidos.index', compact('items'));
    }

/* ============================================
   👁️ VER
============================================ */
public function show(string $id)
{
    $item = Pedido::with([
            'usuario',
            'cupon',

            // Detalle del pedido con producto, imagen y variante
            'detalle.producto.imagenPrincipal',
            'detalle.variante.opcion',

            'pagos',
            'pagoUltimo',
            'venta',
            'usoCupon',
        ])
        ->findOrFail($id);

    $this->sincronizarPagoActualEnMemoria($item);

    return view('admin.pedidos.show', compact('item'));
}

/* ============================================
   🧭 PANEL DE GESTIÓN DEL PEDIDO
============================================ */
public function verificar(string $id)
{
    $item = Pedido::with([
            'usuario',
            'cupon',

            // Detalle del pedido con producto, imagen y variante
            'detalle.producto.imagenPrincipal',
            'detalle.variante.opcion',

            'pagos',
            'pagoUltimo',
            'venta',
            'usoCupon',
        ])
        ->findOrFail($id);

    $this->sincronizarPagoActualEnMemoria($item);

    $estados = $this->estadosPedido();
    $transicionesDisponibles = $this->transicionesPermitidas($item);

    return view(
        'admin.pedidos.verificar',
        compact(
            'item',
            'estados',
            'transicionesDisponibles'
        )
    );
}
    /* ============================================
       ✅ APROBAR PAGO
    ============================================ */
    public function aprobarPago(string $id)
    {
        if (! Auth::check()) {
            return redirect()
                ->back()
                ->with('error', 'No hay un usuario autenticado para verificar este pago.');
        }

        try {
            $item = Pedido::with([
                    'pagos',
                    'pagoUltimo',
                ])
                ->findOrFail($id);

            $this->sincronizarPagoActualEnMemoria($item);

            if (! $item->pagoUltimo) {
                return redirect()
                    ->route('admin.pedidos.show', $item->id_pedido)
                    ->with('error', 'El pedido no tiene un pago registrado.');
            }

            if ($item->estado !== 'en_revision') {
                return redirect()
                    ->route('admin.pedidos.verificar', $item->id_pedido)
                    ->with('error', 'Solo puedes aprobar pagos de pedidos que estén en revisión.');
            }

            if ($item->pagoUltimo->estado === 'verificado') {
                return redirect()
                    ->route('admin.pedidos.verificar', $item->id_pedido)
                    ->with('error', 'Este pago ya fue verificado y no se puede procesar nuevamente.');
            }

            if ($item->pagoUltimo->estado === 'rechazado') {
                return redirect()
                    ->route('admin.pedidos.verificar', $item->id_pedido)
                    ->with('error', 'Este pago ya fue rechazado y no se puede aprobar nuevamente.');
            }

            if ($item->pagoUltimo->estado !== 'enviado') {
                return redirect()
                    ->route('admin.pedidos.verificar', $item->id_pedido)
                    ->with('error', 'Solo puedes aprobar un pago cuyo estado actual sea ENVIADO.');
            }

            DB::transaction(function () use ($item) {
                $item->pagoUltimo->update([
                    'estado' => 'verificado',
                    'id_usuario_verificador' => Auth::id(),
                    'verificado_en' => now(),
                    'motivo_rechazo' => null,
                ]);

                $item->update([
                    'estado' => 'pagado_verificado',
                ]);
            });

            $item->refresh();

            if ($item->correo_cliente) {
                Mail::to($item->correo_cliente)
                    ->send(new PedidoAprobadoMail($item));
            }

            return redirect()
                ->route('admin.pedidos.verificar', $item->id_pedido)
                ->with('success', 'Pago verificado correctamente y se notificó al cliente por correo.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', 'Error al verificar el pago.');
        }
    }

    /* ============================================
       ❌ RECHAZAR PAGO
    ============================================ */
    public function rechazarPago(Request $request, string $id)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:255',
        ]);

        if (! Auth::check()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'No hay un usuario autenticado para rechazar este pago.');
        }

        try {
            $item = Pedido::with([
                    'pagos',
                    'pagoUltimo',
                ])
                ->findOrFail($id);

            $this->sincronizarPagoActualEnMemoria($item);

            if (! $item->pagoUltimo) {
                return redirect()
                    ->route('admin.pedidos.show', $item->id_pedido)
                    ->with('error', 'El pedido no tiene un pago registrado.');
            }

            if ($item->estado !== 'en_revision') {
                return redirect()
                    ->route('admin.pedidos.verificar', $item->id_pedido)
                    ->with('error', 'Solo puedes rechazar pagos de pedidos que estén en revisión.');
            }

            if ($item->pagoUltimo->estado === 'rechazado') {
                return redirect()
                    ->route('admin.pedidos.verificar', $item->id_pedido)
                    ->with('error', 'Este pago ya fue rechazado y no se puede procesar nuevamente.');
            }

            if ($item->pagoUltimo->estado === 'verificado') {
                return redirect()
                    ->route('admin.pedidos.verificar', $item->id_pedido)
                    ->with('error', 'Este pago ya fue verificado y no se puede rechazar nuevamente.');
            }

            if ($item->pagoUltimo->estado !== 'enviado') {
                return redirect()
                    ->route('admin.pedidos.verificar', $item->id_pedido)
                    ->with('error', 'Solo puedes rechazar un pago cuyo estado actual sea ENVIADO.');
            }

            DB::transaction(function () use ($item, $request) {
                $item->pagoUltimo->update([
                    'estado' => 'rechazado',
                    'id_usuario_verificador' => Auth::id(),
                    'verificado_en' => now(),
                    'motivo_rechazo' => trim($request->motivo_rechazo),
                ]);

                $item->update([
                    'estado' => 'rechazado',
                ]);
            });

            /*
            |--------------------------------------------------------------------------
            | ENVIAR CORREO
            |--------------------------------------------------------------------------
            */

            $item->refresh();

            if ($item->correo_cliente) {
                Mail::to($item->correo_cliente)
                    ->send(new PagoRechazadoMail($item));
            }

            return redirect()
                ->route('admin.pedidos.verificar', $item->id_pedido)
                ->with('success', 'Pago rechazado correctamente.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al rechazar el pago.');
        }
    }

   /* ============================================
   🔄 ACTUALIZAR ESTADO DEL PEDIDO
============================================ */

public function actualizarEstado(Request $request, string $id)
{
    $pedido = Pedido::with([
            'pagos',
            'pagoUltimo',
            'detalle.producto',
            'detalle.variante',
        ])
        ->findOrFail($id);

    $this->sincronizarPagoActualEnMemoria($pedido);

    $request->validate([
        'estado' => [
            'required',
            'string',
            Rule::in(array_keys($this->estadosPedido())),
        ],
    ]);

    $estadoNuevo = $request->estado;
    $transicionesPermitidas = $this->transicionesPermitidas($pedido);

    if (! in_array($estadoNuevo, $transicionesPermitidas, true)) {
        return redirect()
            ->route('admin.pedidos.verificar', $pedido->id_pedido)
            ->with('error', 'La transición de estado no está permitida desde el estado actual.');
    }

    if ($estadoNuevo === 'pagado_verificado') {
        if (! $pedido->pagoUltimo || $pedido->pagoUltimo->estado !== 'verificado') {
            return redirect()
                ->route('admin.pedidos.verificar', $pedido->id_pedido)
                ->with('error', 'No puedes pasar el pedido a PAGADO VERIFICADO si el pago aún no está verificado.');
        }
    }

    try {
        DB::transaction(function () use ($pedido, $estadoNuevo) {

            if ($estadoNuevo === 'cancelado') {
                $this->devolverInventarioPedido(
                    $pedido,
                    'Devolución por pedido cancelado'
                );
            }

            $pedido->update([
                'estado' => $estadoNuevo,
            ]);
        });

        return redirect()
            ->route('admin.pedidos.verificar', $pedido->id_pedido)
            ->with('success', 'Estado del pedido actualizado correctamente.');
    } catch (\Throwable $e) {
        report($e);

        return redirect()
            ->route('admin.pedidos.verificar', $pedido->id_pedido)
            ->with('error', 'No se pudo actualizar el estado del pedido.');
    }
}
    /* ============================================
       🧠 CATÁLOGO DE ESTADOS
    ============================================ */
    private function estadosPedido(): array
    {
        return [
            'pendiente_pago' => [
                'label' => 'Pendiente de Pago',
                'icon' => 'bx-time-five',
                'class' => 'status-inactive',
                'descripcion' => 'Pedido creado, aún sin validación del pago.',
            ],
            'en_revision' => [
                'label' => 'En Revisión',
                'icon' => 'bx-search-alt',
                'class' => 'status-warning',
                'descripcion' => 'Pago enviado por el cliente y pendiente de revisión.',
            ],
            'pagado_verificado' => [
                'label' => 'Pagado Verificado',
                'icon' => 'bx-check-circle',
                'class' => 'status-active',
                'descripcion' => 'Pago confirmado correctamente.',
            ],
            'preparando' => [
                'label' => 'Preparando',
                'icon' => 'bx-package',
                'class' => 'status-info',
                'descripcion' => 'El pedido está siendo preparado.',
            ],
            'enviado' => [
                'label' => 'Enviado',
                'icon' => 'bx-send',
                'class' => 'status-primary',
                'descripcion' => 'El pedido salió para entrega.',
            ],
            'entregado' => [
                'label' => 'Entregado',
                'icon' => 'bx-check-shield',
                'class' => 'status-dark',
                'descripcion' => 'El cliente ya recibió su pedido.',
            ],
            'rechazado' => [
                'label' => 'Rechazado',
                'icon' => 'bx-x-circle',
                'class' => 'status-danger',
                'descripcion' => 'El pago o el pedido fue rechazado.',
            ],
            'cancelado' => [
                'label' => 'Cancelado',
                'icon' => 'bx-block',
                'class' => 'status-danger',
                'descripcion' => 'Pedido cancelado.',
            ],
        ];
    }

    /* ============================================
       🔐 TRANSICIONES PERMITIDAS
    ============================================ */
    private function transicionesPermitidas(Pedido $pedido): array
    {
        $mapa = [
            'pendiente_pago' => ['en_revision', 'cancelado'],
            'en_revision' => ['pagado_verificado', 'rechazado', 'cancelado'],
            'pagado_verificado' => ['preparando', 'cancelado'],
            'preparando' => ['enviado', 'cancelado'],
            'enviado' => ['entregado'],
            'entregado' => [],
            'rechazado' => ['en_revision', 'cancelado'],
            'cancelado' => [],
        ];

        return $mapa[$pedido->estado] ?? [];
    }

    /* ============================================
       🛡️ RESOLVER PAGO ACTUAL REAL
    ============================================ */
    private function sincronizarPagoActualEnMemoria(Pedido $pedido): void
    {
        if (! $pedido->relationLoaded('pagos') || $pedido->pagos->isEmpty()) {
            return;
        }

        $pagoActual = $pedido->pagos
            ->sortByDesc(function ($pago) {
                return sprintf(
                    '%01d-%010d-%010d-%010d',
                    (int) ($pago->es_ultimo ?? 0),
                    (int) ($pago->intento ?? 0),
                    optional($pago->enviado_en)->timestamp ?? 0,
                    optional($pago->created_at)->timestamp ?? 0
                );
            })
            ->first();

        if ($pagoActual) {
            $pedido->setRelation('pagoUltimo', $pagoActual);
        }
    }




private function devolverInventarioPedido(Pedido $pedido, string $motivo): void
{
    $pedido->loadMissing([
        'detalle.producto',
        'detalle.variante',
    ]);

    foreach ($pedido->detalle as $detalle) {

        if (! $detalle->id_producto) {
            continue;
        }

        $notas = 'Pedido: ' . $pedido->numero_pedido;

        /*
        |--------------------------------------------------------------------------
        | EVITAR DEVOLVER DOS VECES EL MISMO DETALLE
        |--------------------------------------------------------------------------
        | Si el detalle tiene variante, se valida también por id_producto_variante.
        | Así no se confunden dos variantes del mismo producto.
        */
        $yaDevueltoQuery = MovimientoInventario::where('id_pedido', $pedido->id_pedido)
            ->where('id_producto', $detalle->id_producto)
            ->where('tipo', 'entrada')
            ->where('motivo', $motivo)
            ->where('notas', $notas);

        if ($detalle->id_producto_variante) {
            $yaDevueltoQuery->where('id_producto_variante', $detalle->id_producto_variante);
        } else {
            $yaDevueltoQuery->whereNull('id_producto_variante');
        }

        if ($yaDevueltoQuery->exists()) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | DEVOLVER INVENTARIO A LA VARIANTE
        |--------------------------------------------------------------------------
        | registrarEntradaInventario() ya incrementa stock y crea MovimientoInventario.
        | Por eso NO se debe crear MovimientoInventario manualmente aquí.
        */
        if ($detalle->id_producto_variante) {
            $variante = ProductoVariante::where('id_producto_variante', $detalle->id_producto_variante)
                ->where('id_producto', $detalle->id_producto)
                ->lockForUpdate()
                ->first();

            if (! $variante) {
                continue;
            }

            $variante->registrarEntradaInventario(
                $detalle->cantidad,
                $motivo,
                $pedido->id_pedido,
                null,
                Auth::id() ?? 1,
                $notas
            );

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | DEVOLVER INVENTARIO AL PRODUCTO SIMPLE
        |--------------------------------------------------------------------------
        */
        $producto = Producto::where('id_producto', $detalle->id_producto)
            ->lockForUpdate()
            ->first();

        if (! $producto) {
            continue;
        }

        $producto->registrarEntradaInventario(
            $detalle->cantidad,
            $motivo,
            $pedido->id_pedido,
            null,
            Auth::id() ?? 1,
            $notas
        );
    }
}




}
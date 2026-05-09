<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $carrito = collect(session('carrito', []));

        if ($carrito->isEmpty()) {
            return redirect()
                ->route('tienda.carrito.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $subtotal = $carrito->sum(
            fn ($item) => $item['precio'] * $item['cantidad']
        );

        $envio = 0;
        $descuento = 0;

        $subtotalConDescuento = $subtotal - $descuento;
        $total = $subtotalConDescuento + $envio;

        /*
        |--------------------------------------------------------------------------
        | SOLO PROVINCIAS CON ENVÍOS ACTIVOS
        |--------------------------------------------------------------------------
        */
        $provincias = DB::table('provincias')
            ->join(
                'zonas_envio',
                'provincias.id_provincia',
                '=',
                'zonas_envio.id_provincia'
            )
            ->where('zonas_envio.activo', 1)
            ->select(
                'provincias.id_provincia',
                'provincias.nombre'
            )
            ->distinct()
            ->orderBy('provincias.nombre')
            ->get();

        return view('tienda.checkout.index', compact(
            'carrito',
            'subtotal',
            'envio',
            'descuento',
            'subtotalConDescuento',
            'total',
            'provincias'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CANTONES DISPONIBLES
    |--------------------------------------------------------------------------
    */
    public function cantonesDisponibles($id_provincia)
    {
        $cantones = DB::table('cantones')
            ->join(
                'zonas_envio',
                'cantones.id_canton',
                '=',
                'zonas_envio.id_canton'
            )
            ->where(
                'zonas_envio.id_provincia',
                $id_provincia
            )
            ->where('zonas_envio.activo', 1)
            ->select(
                'cantones.id_canton',
                'cantones.nombre'
            )
            ->distinct()
            ->orderBy('cantones.nombre')
            ->get();

        return response()->json($cantones);
    }

    /*
    |--------------------------------------------------------------------------
    | DISTRITOS DISPONIBLES
    |--------------------------------------------------------------------------
    */
    public function distritosDisponibles($id_canton)
    {
        $distritos = DB::table('distritos')
            ->join(
                'zonas_envio',
                'distritos.id_distrito',
                '=',
                'zonas_envio.id_distrito'
            )
            ->where(
                'zonas_envio.id_canton',
                $id_canton
            )
            ->where('zonas_envio.activo', 1)
            ->select(
                'distritos.id_distrito',
                'distritos.nombre'
            )
            ->distinct()
            ->orderBy('distritos.nombre')
            ->get();

        return response()->json($distritos);
    }

    public function confirmar(Request $request)
    {
        $carrito = collect(session('carrito', []));

        if ($carrito->isEmpty()) {
            return redirect()
                ->route('tienda.carrito.index')
                ->with('error', 'Tu carrito está vacío.');
        }

        $request->validate([

            'nombre_cliente' => [
                'required',
                'string',
                'max:120'
            ],

            'telefono_cliente' => [
                'required',
                'string',
                'max:30'
            ],

            'correo_cliente' => [
                'nullable',
                'email',
                'max:190'
            ],

            'tipo_entrega' => [
                'required',
                'in:envio,retiro'
            ],

            'id_provincia' => [
                'required_if:tipo_entrega,envio',
                'nullable',
                'integer',
                'exists:provincias,id_provincia'
            ],

            'id_canton' => [
                'required_if:tipo_entrega,envio',
                'nullable',
                'integer',
                'exists:cantones,id_canton'
            ],

            'id_distrito' => [
                'required_if:tipo_entrega,envio',
                'nullable',
                'integer',
                'exists:distritos,id_distrito'
            ],

            'direccion_envio' => [
                'required_if:tipo_entrega,envio',
                'nullable',
                'string',
                'max:255'
            ],

            'referencia_envio' => [
                'nullable',
                'string',
                'max:255'
            ],

            'link_google_maps' => [
                'nullable',
                'url',
                'max:255'
            ],

            'notas' => [
                'nullable',
                'string',
                'max:255'
            ],

        ]);

        try {

            DB::beginTransaction();

            $subtotal = 0;

            foreach ($carrito as $item) {

                $producto = Producto::where(
                    'id_producto',
                    $item['id_producto']
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    !$producto->activo ||
                    $item['cantidad'] > $producto->stock_actual
                ) {

                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            "Stock insuficiente para {$producto->nombre}."
                        );
                }

                $subtotal += (
                    $producto->precio * $item['cantidad']
                );
            }

            $provinciaNombre = null;
            $cantonNombre = null;
            $distritoNombre = null;

            $envio = 0;

            /*
            |--------------------------------------------------------------------------
            | VALIDAR ZONA ACTIVA
            |--------------------------------------------------------------------------
            */
            if ($request->tipo_entrega === 'envio') {

                $zonaEnvio = DB::table('zonas_envio')
                    ->where(
                        'id_provincia',
                        $request->id_provincia
                    )
                    ->where(
                        'id_canton',
                        $request->id_canton
                    )
                    ->where(
                        'id_distrito',
                        $request->id_distrito
                    )
                    ->where('activo', 1)
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | SOLO ZONAS DISPONIBLES
                |--------------------------------------------------------------------------
                */
                if (!$zonaEnvio) {

                    DB::rollBack();

                    return back()
                        ->withInput()
                        ->with(
                            'error',
                            'La zona seleccionada no está disponible para envío.'
                        );
                }

                $provinciaNombre = DB::table('provincias')
                    ->where(
                        'id_provincia',
                        $request->id_provincia
                    )
                    ->value('nombre');

                $cantonNombre = DB::table('cantones')
                    ->where(
                        'id_canton',
                        $request->id_canton
                    )
                    ->value('nombre');

                $distritoNombre = DB::table('distritos')
                    ->where(
                        'id_distrito',
                        $request->id_distrito
                    )
                    ->value('nombre');

                /*
                |--------------------------------------------------------------------------
                | COSTO ENVÍO
                |--------------------------------------------------------------------------
                */
                $envio = $zonaEnvio->costo;
            }

            $descuento = 0;

            $subtotalConDescuento = (
                $subtotal - $descuento
            );

            $total = (
                $subtotalConDescuento + $envio
            );

            $pedido = Pedido::create([

                'numero_pedido' => (
                    'PED-' . now()->format('YmdHis')
                ),

                'estado' => 'pendiente_pago',

                'id_usuario' => null,

                'nombre_cliente' => $request->nombre_cliente,

                'telefono_cliente' => $request->telefono_cliente,

                'correo_cliente' => $request->correo_cliente,

                'tipo_entrega' => $request->tipo_entrega,

                'provincia_envio' => $provinciaNombre,

                'canton_envio' => $cantonNombre,

                'distrito_envio' => $distritoNombre,

                'direccion_envio' => $request->direccion_envio,

                'referencia_envio' => $request->referencia_envio,

                'link_google_maps' => $request->link_google_maps,

                'costo_envio' => $envio,

                'id_cupon' => null,

                'codigo_cupon' => null,

                'descuento' => $descuento,

                'subtotal' => $subtotal,

                'subtotal_con_descuento' => $subtotalConDescuento,

                'total' => $total,

                'notas' => $request->notas,

                'codigo_seguimiento_publico' => Str::upper(
                    Str::random(16)
                ),

            ]);

            foreach ($carrito as $item) {

                $producto = Producto::findOrFail(
                    $item['id_producto']
                );

                DetallePedido::create([

                    'id_pedido' => $pedido->id_pedido,

                    'id_producto' => $producto->id_producto,

                    'nombre_producto' => $producto->nombre,

                    'sku_snapshot' => $producto->sku,

                    'precio_unitario' => $producto->precio,

                    'cantidad' => $item['cantidad'],

                    'total_linea' => (
                        $producto->precio * $item['cantidad']
                    ),

                ]);

                $producto->decrement(
                    'stock_actual',
                    $item['cantidad']
                );
            }

            DB::commit();

            session()->forget('carrito');

         return redirect()
    ->route(
        'tienda.checkout.confirmacion',
        $pedido->id_pedido
    )
    ->with(
        'success',
        'Pedido creado correctamente.'
    );

        } catch (\Throwable $th) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Ocurrió un error al procesar el pedido.'
                );
        }
    }

    public function confirmacion(Pedido $pedido)
    {
        $pedido->load([
            'detalle',
            'pagoUltimo'
        ]);

        return view(
            'tienda.checkout.confirmacion',
            compact('pedido')
        );
    }
}
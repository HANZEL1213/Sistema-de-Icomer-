<?php

namespace App\Http\Controllers\Tienda;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\DetallePedido;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\PagoPedido;
use App\Models\Cupon;
use App\Models\UsoCupon;
use App\Models\Venta;

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
    $cuponAplicado = session('cupon');

    if ($cuponAplicado) {
        $cupon = Cupon::where('codigo', $cuponAplicado['codigo'])->first();

        if ($cupon) {
            $validacion = $this->validarCupon($cupon, $subtotal);

            if ($validacion === true) {
                $descuento = $this->calcularDescuentoCupon($cupon, $subtotal);
            } else {
                session()->forget('cupon');
                $cuponAplicado = null;
            }
        } else {
            session()->forget('cupon');
            $cuponAplicado = null;
        }
    }

    $subtotalConDescuento = max($subtotal - $descuento, 0);
    $total = $subtotalConDescuento + $envio;

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
        'provincias',
        'cuponAplicado'
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
        'nombre_cliente' => ['required', 'string', 'max:120'],
        'telefono_cliente' => ['required', 'string', 'max:30'],
        'correo_cliente' => ['nullable', 'email', 'max:190'],

        'tipo_entrega' => ['required', 'in:envio,retiro'],

        'id_provincia' => ['required_if:tipo_entrega,envio', 'nullable', 'integer', 'exists:provincias,id_provincia'],
        'id_canton'   => ['required_if:tipo_entrega,envio', 'nullable', 'integer', 'exists:cantones,id_canton'],
        'id_distrito' => ['required_if:tipo_entrega,envio', 'nullable', 'integer', 'exists:distritos,id_distrito'],

        'direccion_envio'  => ['required_if:tipo_entrega,envio', 'nullable', 'string', 'max:255'],
        'referencia_envio' => ['nullable', 'string', 'max:255'],
        'link_google_maps' => ['nullable', 'url', 'max:255'],
        'notas'            => ['nullable', 'string', 'max:255'],

        'metodo_pago' => ['required', 'in:sinpe'],
        'numero_comprobante' => [
            'nullable',
            'string',
            'max:80',
            'required_without:comprobante_pago',
        ],
        'comprobante_pago' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:4096',
            'required_without:numero_comprobante',
        ],
    ]);

    try {
        DB::beginTransaction();

        $subtotal = 0;

        foreach ($carrito as $item) {
            $producto = Producto::where('id_producto', $item['id_producto'])
                ->lockForUpdate()
                ->firstOrFail();

            if (!$producto->activo || $item['cantidad'] > $producto->stock_actual) {
                DB::rollBack();

                return back()
                    ->withInput()
                    ->with('error', "Stock insuficiente para {$producto->nombre}.");
            }

           $subtotal += $producto->precioVenta() * $item['cantidad'];
        }

        // === Lógica de envío ===
        $provinciaNombre = null;
        $cantonNombre = null;
        $distritoNombre = null;
        $envio = 0;

        if ($request->tipo_entrega === 'envio') {
            $zonaEnvio = DB::table('zonas_envio')
                ->where('id_provincia', $request->id_provincia)
                ->where('id_canton', $request->id_canton)
                ->where('id_distrito', $request->id_distrito)
                ->where('activo', 1)
                ->first();

            if (!$zonaEnvio) {
                DB::rollBack();

                return back()
                    ->withInput()
                    ->with('error', 'La zona seleccionada no está disponible para envío.');
            }

            $provinciaNombre = DB::table('provincias')
                ->where('id_provincia', $request->id_provincia)
                ->value('nombre');

            $cantonNombre = DB::table('cantones')
                ->where('id_canton', $request->id_canton)
                ->value('nombre');

            $distritoNombre = DB::table('distritos')
                ->where('id_distrito', $request->id_distrito)
                ->value('nombre');

            $envio = $zonaEnvio->costo;
        }

        /*
        |--------------------------------------------------------------------------
        | CUPÓN APLICADO
        |--------------------------------------------------------------------------
        */
        $descuento = 0;
        $idCupon = null;
        $codigoCupon = null;

        $cuponAplicado = session('cupon');

        if ($cuponAplicado) {
            $cupon = Cupon::where('codigo', $cuponAplicado['codigo'])
                ->lockForUpdate()
                ->first();

            if ($cupon) {
                $validacion = $this->validarCupon($cupon, $subtotal);

                if ($validacion !== true) {
                    DB::rollBack();

                    session()->forget('cupon');

                    return redirect()
                        ->route('tienda.carrito.index')
                        ->with('error', $validacion);
                }

                $descuento = $this->calcularDescuentoCupon($cupon, $subtotal);
                $idCupon = $cupon->id_cupon;
                $codigoCupon = $cupon->codigo;
            } else {
                session()->forget('cupon');
            }
        }

        $subtotalConDescuento = max($subtotal - $descuento, 0);
        $total = $subtotalConDescuento + $envio;

// === Crear Pedido ===
$pedido = Pedido::create([
    'numero_pedido' => 'PED-' . now()->format('YmdHis'),
    'estado' => 'pendiente_pago',

    'id_usuario' => Auth::check() ? Auth::user()->id_usuario : null,
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
    'id_cupon' => $idCupon,
    'codigo_cupon' => $codigoCupon,
    'descuento' => $descuento,
    'subtotal' => $subtotal,
    'subtotal_con_descuento' => $subtotalConDescuento,
    'total' => $total,

    'notas' => $request->notas,
    'codigo_seguimiento_publico' => Str::upper(Str::random(16)),
]);

// === Registrar en reporte de ventas ===
Venta::create([
    'canal' => 'online',
    'id_pedido' => $pedido->id_pedido,
    'id_venta_local' => null,
]);

        // === Registrar uso del cupón ===
        if ($idCupon) {
    UsoCupon::create([
    'id_cupon' => $idCupon,
    'id_pedido' => $pedido->id_pedido,
    'id_usuario' => null,
    'correo_invitado' => $request->correo_cliente,
    'monto_descuento' => $descuento,
]);
        }

        // === Crear detalles del pedido y descontar stock ===
foreach ($carrito as $item) {

    $producto = Producto::findOrFail($item['id_producto']);

  DetallePedido::create([
    'id_pedido' => $pedido->id_pedido,
    'id_producto' => $producto->id_producto,
    'nombre_producto' => $producto->nombre,
    'sku_snapshot' => $producto->sku,

    'precio_unitario' => $producto->precioVenta(),

    'cantidad' => $item['cantidad'],

    'total_linea' => $producto->precioVenta() * $item['cantidad'],
]);
    $producto->registrarSalidaInventario(
        $item['cantidad'],
        'Pedido online',
        $pedido->id_pedido,
        null,
        Auth::id() ?? 1,
        'Pedido: ' . $pedido->numero_pedido
    );
}

        // === Lógica de pago ===
        $rutaComprobante = null;

        if ($request->hasFile('comprobante_pago')) {
            $rutaComprobante = $request->file('comprobante_pago')
                ->store('comprobantes/pedidos', 'public');
        }

        PagoPedido::create([
            'id_pedido' => $pedido->id_pedido,
            'metodo' => $request->metodo_pago,
            'intento' => 1,
            'es_ultimo' => true,
            'ruta_comprobante' => $rutaComprobante,
            'numero_comprobante' => $request->numero_comprobante,
            'monto_reportado' => $pedido->total,
            'moneda' => 'CRC',
            'estado' => 'enviado',
            'enviado_en' => now(),
        ]);

        $pedido->update([
            'estado' => 'en_revision',
        ]);

        DB::commit();

        session()->forget([
            'carrito',
            'cupon',
        ]);

        return redirect()
            ->route('tienda.checkout.confirmacion', $pedido->id_pedido)
            ->with('success', 'Pedido creado correctamente.');

    } catch (\Throwable $th) {
        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', 'Ocurrió un error al procesar el pedido.');
    }
}

public function confirmacion(Pedido $pedido)
{
  $pedido->load([
    'detalle.producto.imagenPrincipal',
    'pagoUltimo',
    'cupon',
    'usoCupon',
]);

    return view(
        'tienda.checkout.confirmacion',
        compact('pedido')
    );
}


public function costoEnvio($id_distrito)
{
    $zona = DB::table('zonas_envio')
        ->where('id_distrito', $id_distrito)
        ->where('activo', 1)
        ->first();

    if (!$zona) {
        return response()->json([
            'success' => false,
            'costo' => 0,
        ]);
    }

    return response()->json([
        'success' => true,
        'costo' => (float) $zona->costo,
    ]);
}


private function validarCupon(Cupon $cupon, float $subtotal)
{
    $ahora = now();

    if (! $cupon->activo) {
        return 'Este cupón no está activo.';
    }

    if ($cupon->inicia_en && $ahora->lt($cupon->inicia_en)) {
        return 'Este cupón aún no está disponible.';
    }

    if ($cupon->termina_en && $ahora->gt($cupon->termina_en)) {
        return 'Este cupón ya venció.';
    }

    if ($subtotal < (float) $cupon->minimo_subtotal) {
        return 'El subtotal mínimo para usar este cupón es ₡' .
            number_format((float) $cupon->minimo_subtotal, 2) .
            '.';
    }

    if ($cupon->max_usos_total && $cupon->usos()->count() >= $cupon->max_usos_total) {
        return 'Este cupón ya alcanzó el máximo de usos.';
    }

    return true;
}
private function calcularDescuentoCupon(Cupon $cupon, float $subtotal): float
{
    if ($cupon->tipo === 'porcentaje') {
        $descuento = $subtotal * ((float) $cupon->valor / 100);
    } else {
        $descuento = (float) $cupon->valor;
    }

    return min($descuento, $subtotal);
}
}


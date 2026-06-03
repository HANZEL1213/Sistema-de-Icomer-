<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{

public function index()
{
    $carritoSession = session('carrito', []);
    $carritoActualizado = [];

    foreach ($carritoSession as $item) {

        $producto = Producto::find($item['id_producto']);

        if (!$producto || !$producto->activo) {
            continue;
        }

        $precioVenta = $producto->precioVenta();
        $precioNormal = round((float) $producto->precio, 2);
        $tienePromocion = $producto->tienePromocionActiva();

        $ahorro = $tienePromocion
            ? max(0, $precioNormal - $precioVenta)
            : 0;

        $porcentajeDescuento = $tienePromocion && $precioNormal > 0
            ? round(($ahorro / $precioNormal) * 100)
            : 0;

        $item['precio'] = $precioVenta;
        $item['precio_normal'] = $precioNormal;
        $item['tiene_promocion'] = $tienePromocion;
        $item['ahorro'] = $ahorro;
        $item['porcentaje_descuento'] = $porcentajeDescuento;
        $item['stock'] = $producto->stock_actual;

        $carritoActualizado[$producto->id_producto] = $item;
    }

    session(['carrito' => $carritoActualizado]);

    $carrito = collect($carritoActualizado);

    $subtotal = $carrito->sum(
        fn ($item) => $item['precio'] * $item['cantidad']
    );

    $envio = 0;
    $descuento = 0;
    $cuponAplicado = session('cupon');

    if ($cuponAplicado) {

        $cupon = Cupon::where(
            'codigo',
            $cuponAplicado['codigo']
        )->first();

        if ($cupon) {

            $validacion = $this->validarCupon(
                $cupon,
                $subtotal
            );

            if ($validacion === true) {

                $descuento = $this->calcularDescuentoCupon(
                    $cupon,
                    $subtotal
                );

                session([
                    'cupon' => [
                        'id_cupon' => $cupon->id_cupon,
                        'codigo' => $cupon->codigo,
                        'tipo' => $cupon->tipo,
                        'valor' => $cupon->valor,
                        'descuento' => $descuento,
                    ],
                ]);

            } else {

                session()->forget('cupon');
                $cuponAplicado = null;
            }

        } else {

            session()->forget('cupon');
            $cuponAplicado = null;
        }
    }

    $total = max(
        ($subtotal + $envio) - $descuento,
        0
    );

    return view('tienda.carrito.index', compact(
        'carrito',
        'subtotal',
        'envio',
        'descuento',
        'total',
        'cuponAplicado'
    ));
}
    public function agregar(Request $request, Producto $producto)
    {
        abort_if(!$producto->activo || $producto->deleted_at, 404);

        $request->validate([
            'cantidad' => ['nullable', 'integer', 'min:1'],
        ]);

        $cantidad = (int) ($request->cantidad ?? 1);

        if ($producto->stock_actual <= 0) {
            return back()->with('error', 'Este producto no tiene stock disponible.');
        }

        $carrito = session('carrito', []);

        $id = $producto->id_producto;

        $cantidadActual = $carrito[$id]['cantidad'] ?? 0;
        $nuevaCantidad = $cantidadActual + $cantidad;

        if ($nuevaCantidad > $producto->stock_actual) {
            return back()->with('error', 'No puedes agregar más unidades que el stock disponible.');
        }

        $imagen = $producto->imagenPrincipal
            ? $producto->imagenPrincipal->ruta
            : null;

$precioVenta = $producto->precioVenta();
$precioNormal = round((float) $producto->precio, 2);
$tienePromocion = $producto->tienePromocionActiva();

$ahorro = $tienePromocion
    ? max(0, $precioNormal - $precioVenta)
    : 0;

$porcentajeDescuento = $tienePromocion && $precioNormal > 0
    ? round(($ahorro / $precioNormal) * 100)
    : 0;

$carrito[$id] = [
    'id_producto' => $producto->id_producto,
    'nombre' => $producto->nombre,
    'slug' => $producto->slug,

    'precio' => $precioVenta,
    'precio_normal' => $precioNormal,
    'tiene_promocion' => $tienePromocion,
    'ahorro' => $ahorro,
    'porcentaje_descuento' => $porcentajeDescuento,

    'cantidad' => $nuevaCantidad,
    'stock' => $producto->stock_actual,
    'imagen' => $imagen,
    'marca' => $producto->marca?->nombre,
    'categoria' => $producto->categoriaPrincipal?->nombre,
];

        session(['carrito' => $carrito]);

      return back()
    ->with('success', 'Producto agregado al carrito.');
    }


public function actualizar(Request $request, Producto $producto)
{
    $request->validate([
        'cantidad' => ['required', 'integer', 'min:1'],
    ]);

    $carrito = session('carrito', []);

    if (!isset($carrito[$producto->id_producto])) {
        return back()->with('error', 'El producto no existe en el carrito.');
    }

    if ($request->cantidad > $producto->stock_actual) {
        return back()->with('error', 'La cantidad supera el stock disponible.');
    }

    $precioVenta = $producto->precioVenta();
    $precioNormal = round((float) $producto->precio, 2);
    $tienePromocion = $producto->tienePromocionActiva();

    $ahorro = $tienePromocion
        ? max(0, $precioNormal - $precioVenta)
        : 0;

    $porcentajeDescuento = $tienePromocion && $precioNormal > 0
        ? round(($ahorro / $precioNormal) * 100)
        : 0;

    $carrito[$producto->id_producto]['cantidad'] = (int) $request->cantidad;
    $carrito[$producto->id_producto]['stock'] = $producto->stock_actual;

    $carrito[$producto->id_producto]['precio'] = $precioVenta;
    $carrito[$producto->id_producto]['precio_normal'] = $precioNormal;
    $carrito[$producto->id_producto]['tiene_promocion'] = $tienePromocion;
    $carrito[$producto->id_producto]['ahorro'] = $ahorro;
    $carrito[$producto->id_producto]['porcentaje_descuento'] = $porcentajeDescuento;

    session(['carrito' => $carrito]);

    return back()->with('success', 'Carrito actualizado.');
}

    public function eliminar(Producto $producto)
    {
        $carrito = session('carrito', []);

        unset($carrito[$producto->id_producto]);

        session(['carrito' => $carrito]);

        if (empty($carrito)) {
            session()->forget('cupon');
        }

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function vaciar()
    {
        session()->forget([
            'carrito',
            'cupon',
        ]);

        return redirect()
            ->route('tienda.carrito.index')
            ->with('success', 'Carrito vaciado correctamente.');
    }

    public function aplicarCupon(Request $request)
    {
        $request->validate([
            'codigo_cupon' => ['required', 'string', 'max:60'],
        ]);

        $codigo = strtoupper(trim($request->codigo_cupon));

        $carrito = collect(session('carrito', []));

        if ($carrito->isEmpty()) {
            return back()->with('error', 'No puedes aplicar un cupón con el carrito vacío.');
        }

        $subtotal = $carrito->sum(fn ($item) => $item['precio'] * $item['cantidad']);

        $cupon = Cupon::where('codigo', $codigo)->first();

        if (!$cupon) {
            return back()->with('error', 'El cupón ingresado no existe.');
        }

        $validacion = $this->validarCupon($cupon, $subtotal);

        if ($validacion !== true) {
            return back()->with('error', $validacion);
        }

        $descuento = $this->calcularDescuentoCupon($cupon, $subtotal);

        session([
            'cupon' => [
                'id_cupon' => $cupon->id_cupon,
                'codigo' => $cupon->codigo,
                'tipo' => $cupon->tipo,
                'valor' => $cupon->valor,
                'descuento' => $descuento,
            ],
        ]);

        return back()->with('success', 'Cupón aplicado correctamente.');
    }

    public function eliminarCupon()
    {
        session()->forget('cupon');

        return back()->with('success', 'Cupón eliminado correctamente.');
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

    if (
        $cupon->max_usos_total &&
        $cupon->usos()->count() >= $cupon->max_usos_total
    ) {
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
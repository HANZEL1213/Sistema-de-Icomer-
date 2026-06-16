<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\ProductoVariante;

class CarritoController extends Controller
{
public function index()
{
    $carritoSession = session('carrito', []);
    $carritoActualizado = [];

    foreach ($carritoSession as $key => $item) {

        $producto = Producto::with([
            'marca',
            'categoriaPrincipal',
            'imagenPrincipal',
        ])->find($item['id_producto']);

        if (!$producto || !$producto->activo) {
            continue;
        }

        $cartKey = $item['cart_key'] ?? $key;

        if (!empty($item['id_producto_variante'])) {

            $variante = ProductoVariante::with('opcion')
                ->where('id_producto_variante', $item['id_producto_variante'])
                ->where('id_producto', $producto->id_producto)
                ->where('activo', 1)
                ->first();

            if (!$variante) {
                continue;
            }

            $nombreVariante = $variante->nombre
                ?: ($variante->opcion?->etiqueta ?? $variante->opcion?->valor ?? 'Variante');

            $precioVenta = $variante->precioVenta();
            $precioNormal = $variante->precioOriginal();
            $tienePromocion = $variante->promocionVigente();

            $ahorro = $tienePromocion
                ? max(0, $precioNormal - $precioVenta)
                : 0;

            $porcentajeDescuento = $tienePromocion && $precioNormal > 0
                ? round(($ahorro / $precioNormal) * 100)
                : 0;

            $item['cart_key'] = $cartKey;
            $item['id_producto'] = $producto->id_producto;
            $item['id_producto_variante'] = $variante->id_producto_variante;

            $item['nombre'] = $producto->nombre;
            $item['variante'] = $nombreVariante;
            $item['slug'] = $producto->slug;

            $item['precio'] = $precioVenta;
            $item['precio_normal'] = $precioNormal;
            $item['tiene_promocion'] = $tienePromocion;
            $item['ahorro'] = $ahorro;
            $item['porcentaje_descuento'] = $porcentajeDescuento;

            $item['stock'] = $variante->stock_actual;
            $item['marca'] = $producto->marca?->nombre;
            $item['categoria'] = $producto->categoriaPrincipal?->nombre;
            $item['imagen'] = $producto->imagenPrincipal?->ruta;

            if ($item['cantidad'] > $variante->stock_actual) {
                $item['cantidad'] = $variante->stock_actual;
            }

            if ($item['cantidad'] <= 0) {
                continue;
            }

            $carritoActualizado[$cartKey] = $item;

        } else {

            if ($producto->stock_actual <= 0) {
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

            $item['cart_key'] = $cartKey;
            $item['id_producto'] = $producto->id_producto;
            $item['id_producto_variante'] = null;

            $item['nombre'] = $producto->nombre;
            $item['variante'] = null;
            $item['slug'] = $producto->slug;

            $item['precio'] = $precioVenta;
            $item['precio_normal'] = $precioNormal;
            $item['tiene_promocion'] = $tienePromocion;
            $item['ahorro'] = $ahorro;
            $item['porcentaje_descuento'] = $porcentajeDescuento;

            $item['stock'] = $producto->stock_actual;
            $item['marca'] = $producto->marca?->nombre;
            $item['categoria'] = $producto->categoriaPrincipal?->nombre;
            $item['imagen'] = $producto->imagenPrincipal?->ruta;

            if ($item['cantidad'] > $producto->stock_actual) {
                $item['cantidad'] = $producto->stock_actual;
            }

            if ($item['cantidad'] <= 0) {
                continue;
            }

            $carritoActualizado[$cartKey] = $item;
        }
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

        $cupon = Cupon::whereRaw('UPPER(TRIM(codigo)) = ?', [
            strtoupper(trim($cuponAplicado['codigo']))
        ])->first();

        if ($cupon) {

            $validacion = $this->validarCupon($cupon, $subtotal);

            if ($validacion === true) {

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

                $cuponAplicado = session('cupon');

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

    $carrito = session('carrito', []);

    $imagen = $producto->imagenPrincipal
        ? $producto->imagenPrincipal->ruta
        : null;

    if ($producto->usa_variantes) {

        $request->validate([
            'id_producto_variante' => [
                'required',
                'integer',
                'exists:producto_variantes,id_producto_variante',
            ],
        ]);

        $variante = ProductoVariante::with('opcion')
            ->where('id_producto_variante', $request->id_producto_variante)
            ->where('id_producto', $producto->id_producto)
            ->where('activo', 1)
            ->firstOrFail();

        if ($variante->stock_actual <= 0) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta variante no tiene stock disponible.',
                ], 422);
            }

            return back()->with('error', 'Esta variante no tiene stock disponible.');
        }

        $id = $producto->id_producto . '_v_' . $variante->id_producto_variante;

        $cantidadActual = $carrito[$id]['cantidad'] ?? 0;
        $nuevaCantidad = $cantidadActual + $cantidad;

        if ($nuevaCantidad > $variante->stock_actual) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes agregar más unidades que el stock disponible.',
                ], 422);
            }

            return back()->with('error', 'No puedes agregar más unidades que el stock disponible.');
        }

        $nombreVariante = $variante->nombre
            ?: ($variante->opcion?->etiqueta ?? $variante->opcion?->valor ?? 'Variante');

        $precioVenta = $variante->precioVenta();
        $precioNormal = $variante->precioOriginal();
        $tienePromocion = $variante->promocionVigente();

        $ahorro = $tienePromocion
            ? max(0, $precioNormal - $precioVenta)
            : 0;

        $porcentajeDescuento = $tienePromocion && $precioNormal > 0
            ? round(($ahorro / $precioNormal) * 100)
            : 0;

        $carrito[$id] = [
            'cart_key' => $id,

            'id_producto' => $producto->id_producto,
            'id_producto_variante' => $variante->id_producto_variante,

            'nombre' => $producto->nombre,
            'variante' => $nombreVariante,
            'slug' => $producto->slug,

            'precio' => $precioVenta,
            'precio_normal' => $precioNormal,
            'tiene_promocion' => $tienePromocion,
            'ahorro' => $ahorro,
            'porcentaje_descuento' => $porcentajeDescuento,

            'cantidad' => $nuevaCantidad,
            'stock' => $variante->stock_actual,
            'imagen' => $imagen,
            'marca' => $producto->marca?->nombre,
            'categoria' => $producto->categoriaPrincipal?->nombre,
        ];

    } else {

        if ($producto->stock_actual <= 0) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este producto no tiene stock disponible.',
                ], 422);
            }

            return back()->with('error', 'Este producto no tiene stock disponible.');
        }

        $id = (string) $producto->id_producto;

        $cantidadActual = $carrito[$id]['cantidad'] ?? 0;
        $nuevaCantidad = $cantidadActual + $cantidad;

        if ($nuevaCantidad > $producto->stock_actual) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes agregar más unidades que el stock disponible.',
                ], 422);
            }

            return back()->with('error', 'No puedes agregar más unidades que el stock disponible.');
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

        $carrito[$id] = [
            'cart_key' => $id,

            'id_producto' => $producto->id_producto,
            'id_producto_variante' => null,

            'nombre' => $producto->nombre,
            'variante' => null,
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
    }

    session(['carrito' => $carrito]);

    $totalItemsCarrito = collect($carrito)->sum('cantidad');

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito.',
            'total_items' => $totalItemsCarrito,
        ]);
    }

    return back()->with('success', 'Producto agregado al carrito.');
}



public function actualizar(Request $request, Producto $producto)
{
    $request->validate([
        'cantidad' => ['required', 'integer', 'min:1'],
        'cart_key' => ['required', 'string'],
    ]);

    $carrito = session('carrito', []);

    $cartKey = $request->cart_key;

    if (!isset($carrito[$cartKey])) {
        return back()->with('error', 'El producto no existe en el carrito.');
    }

    $item = $carrito[$cartKey];

    if (!empty($item['id_producto_variante'])) {

        $variante = ProductoVariante::with('opcion')
            ->where('id_producto_variante', $item['id_producto_variante'])
            ->where('id_producto', $producto->id_producto)
            ->where('activo', 1)
            ->first();

        if (!$variante) {
            unset($carrito[$cartKey]);
            session(['carrito' => $carrito]);

            return back()->with('error', 'La variante ya no está disponible.');
        }

        if ($request->cantidad > $variante->stock_actual) {
            return back()->with('error', 'La cantidad supera el stock disponible.');
        }

        $precioVenta = $variante->precioVenta();
        $precioNormal = $variante->precioOriginal();
        $tienePromocion = $variante->promocionVigente();

        $ahorro = $tienePromocion
            ? max(0, $precioNormal - $precioVenta)
            : 0;

        $porcentajeDescuento = $tienePromocion && $precioNormal > 0
            ? round(($ahorro / $precioNormal) * 100)
            : 0;

        $nombreVariante = $variante->nombre
            ?: ($variante->opcion?->etiqueta ?? $variante->opcion?->valor ?? 'Variante');

        $carrito[$cartKey]['cantidad'] = (int) $request->cantidad;
        $carrito[$cartKey]['stock'] = $variante->stock_actual;

        $carrito[$cartKey]['variante'] = $nombreVariante;
        $carrito[$cartKey]['id_producto_variante'] = $variante->id_producto_variante;

        $carrito[$cartKey]['precio'] = $precioVenta;
        $carrito[$cartKey]['precio_normal'] = $precioNormal;
        $carrito[$cartKey]['tiene_promocion'] = $tienePromocion;
        $carrito[$cartKey]['ahorro'] = $ahorro;
        $carrito[$cartKey]['porcentaje_descuento'] = $porcentajeDescuento;

    } else {

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

        $carrito[$cartKey]['cantidad'] = (int) $request->cantidad;
        $carrito[$cartKey]['stock'] = $producto->stock_actual;

        $carrito[$cartKey]['precio'] = $precioVenta;
        $carrito[$cartKey]['precio_normal'] = $precioNormal;
        $carrito[$cartKey]['tiene_promocion'] = $tienePromocion;
        $carrito[$cartKey]['ahorro'] = $ahorro;
        $carrito[$cartKey]['porcentaje_descuento'] = $porcentajeDescuento;
    }

    session(['carrito' => $carrito]);

    return back()->with('success', 'Carrito actualizado.');
}

    public function eliminar(Request $request, Producto $producto)
{
    $carrito = session('carrito', []);

    $cartKey = $request->cart_key;

    if (!$cartKey) {
        return back()->with('error', 'No se pudo identificar el producto del carrito.');
    }

    unset($carrito[$cartKey]);

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
        return back()
            ->withInput()
            ->with('error', 'No puedes aplicar un cupón con el carrito vacío.');
    }

    $subtotal = $carrito->sum(
        fn ($item) => $item['precio'] * $item['cantidad']
    );

    $cupon = Cupon::whereRaw('UPPER(TRIM(codigo)) = ?', [$codigo])->first();

    if (!$cupon) {
        return back()
            ->withInput()
            ->with('error', 'El cupón ingresado no existe.');
    }

    $validacion = $this->validarCupon($cupon, $subtotal);

    if ($validacion !== true) {
        return back()
            ->withInput()
            ->with('error', $validacion);
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
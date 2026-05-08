<?php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function index()
    {
        $carrito = collect(session('carrito', []));

        $subtotal = $carrito->sum(fn ($item) => $item['precio'] * $item['cantidad']);

        $envio = 0;
        $descuento = 0;
        $total = $subtotal + $envio - $descuento;

        return view('tienda.carrito.index', compact(
            'carrito',
            'subtotal',
            'envio',
            'descuento',
            'total'
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

        $carrito[$id] = [
            'id_producto' => $producto->id_producto,
            'nombre' => $producto->nombre,
            'slug' => $producto->slug,
            'precio' => $producto->precio,
            'cantidad' => $nuevaCantidad,
            'stock' => $producto->stock_actual,
            'imagen' => $imagen,
            'marca' => $producto->marca?->nombre,
            'categoria' => $producto->categoriaPrincipal?->nombre,
        ];

        session(['carrito' => $carrito]);

        return redirect()
            ->route('tienda.carrito.index')
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

        $carrito[$producto->id_producto]['cantidad'] = (int) $request->cantidad;
        $carrito[$producto->id_producto]['stock'] = $producto->stock_actual;

        session(['carrito' => $carrito]);

        return back()->with('success', 'Carrito actualizado.');
    }

    public function eliminar(Producto $producto)
    {
        $carrito = session('carrito', []);

        unset($carrito[$producto->id_producto]);

        session(['carrito' => $carrito]);

        return back()->with('success', 'Producto eliminado del carrito.');
    }

    public function vaciar()
    {
        session()->forget('carrito');

        return redirect()
            ->route('tienda.carrito.index')
            ->with('success', 'Carrito vaciado correctamente.');
    }
}
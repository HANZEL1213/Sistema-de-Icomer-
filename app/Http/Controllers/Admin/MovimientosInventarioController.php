<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Usuario;
use App\Models\VentaLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovimientosInventarioController extends Controller
{
    public function index()
    {
        $items = MovimientoInventario::with([
                'producto',
                'variante.opcion',
                'pedido',
                'ventaLocal',
                'realizador',
            ])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.inventario.index', compact('items'));
    }

    public function create()
    {
        $productos = Producto::with([
                'imagenPrincipal',
                'tipoVariante',
                'variantesActivas.opcion',
            ])
            ->orderBy('nombre')
            ->get();

        $pedidos = Pedido::orderByDesc('id_pedido')->get();
        $ventasLocales = VentaLocal::orderByDesc('id_venta_local')->get();
        $usuarios = Usuario::orderBy('nombre')->get();

        return view('admin.inventario.create', compact(
            'productos',
            'pedidos',
            'ventasLocales',
            'usuarios'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'id_producto_variante' => 'nullable|exists:producto_variantes,id_producto_variante',
            'tipo' => 'required|in:entrada,salida,ajuste',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:120',
            'id_pedido' => 'nullable|exists:pedidos,id_pedido',
            'id_venta_local' => 'nullable|exists:ventas_locales,id_venta_local',
            'id_usuario_realizador' => 'required|exists:usuarios,id_usuario',
            'notas' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request) {

                if ($request->filled('id_pedido') && $request->filled('id_venta_local')) {
                    throw ValidationException::withMessages([
                        'id_pedido' => 'No puedes asociar el movimiento a un pedido y una venta local al mismo tiempo.',
                        'id_venta_local' => 'No puedes asociar el movimiento a un pedido y una venta local al mismo tiempo.',
                    ]);
                }

                $producto = Producto::where('id_producto', $request->id_producto)
                    ->lockForUpdate()
                    ->firstOrFail();

                $cantidad = (int) $request->cantidad;
                $tipo = $request->tipo;

                if ($producto->usa_variantes) {
                    if (! $request->filled('id_producto_variante')) {
                        throw ValidationException::withMessages([
                            'id_producto_variante' => 'Debes seleccionar una variante para este producto.',
                        ]);
                    }

                    $variante = ProductoVariante::where('id_producto_variante', $request->id_producto_variante)
                        ->where('id_producto', $producto->id_producto)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $stockActual = (int) $variante->stock_actual;

                    if ($tipo === 'entrada') {
                        $nuevoStock = $stockActual + $cantidad;
                    } elseif ($tipo === 'salida') {
                        if ($cantidad > $stockActual) {
                            throw ValidationException::withMessages([
                                'cantidad' => 'La cantidad de salida no puede ser mayor al stock actual de la variante.',
                            ]);
                        }

                        $nuevoStock = $stockActual - $cantidad;
                    } else {
                        $nuevoStock = $cantidad;
                    }

                    MovimientoInventario::create([
                        'id_producto' => $producto->id_producto,
                        'id_producto_variante' => $variante->id_producto_variante,
                        'tipo' => $tipo,
                        'cantidad' => $cantidad,
                        'motivo' => trim($request->motivo),
                        'id_pedido' => $request->filled('id_pedido') ? $request->id_pedido : null,
                        'id_venta_local' => $request->filled('id_venta_local') ? $request->id_venta_local : null,
                        'id_usuario_realizador' => $request->id_usuario_realizador,
                        'notas' => $this->nullIfBlank($request->notas),
                    ]);

                    $variante->update([
                        'stock_actual' => $nuevoStock,
                    ]);

                    return;
                }

                if ($request->filled('id_producto_variante')) {
                    throw ValidationException::withMessages([
                        'id_producto_variante' => 'Este producto no usa variantes.',
                    ]);
                }

                $stockActual = (int) $producto->stock_actual;

                if ($tipo === 'entrada') {
                    $nuevoStock = $stockActual + $cantidad;
                } elseif ($tipo === 'salida') {
                    if ($cantidad > $stockActual) {
                        throw ValidationException::withMessages([
                            'cantidad' => 'La cantidad de salida no puede ser mayor al stock actual.',
                        ]);
                    }

                    $nuevoStock = $stockActual - $cantidad;
                } else {
                    $nuevoStock = $cantidad;
                }

                MovimientoInventario::create([
                    'id_producto' => $producto->id_producto,
                    'id_producto_variante' => null,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'motivo' => trim($request->motivo),
                    'id_pedido' => $request->filled('id_pedido') ? $request->id_pedido : null,
                    'id_venta_local' => $request->filled('id_venta_local') ? $request->id_venta_local : null,
                    'id_usuario_realizador' => $request->id_usuario_realizador,
                    'notas' => $this->nullIfBlank($request->notas),
                ]);

                $producto->update([
                    'stock_actual' => $nuevoStock,
                ]);
            });

            return redirect()
                ->route('admin.inventario-movimientos.index')
                ->with('success', 'Movimiento de inventario registrado correctamente.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar el movimiento de inventario.');
        }
    }

    public function show(string $id)
    {
        $item = MovimientoInventario::with([
                'producto',
                'variante.opcion',
                'pedido',
                'ventaLocal',
                'realizador',
            ])
            ->findOrFail($id);

        return view('admin.inventario.show', compact('item'));
    }

    private function nullIfBlank($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
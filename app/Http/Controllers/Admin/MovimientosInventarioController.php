<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\VentaLocal;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MovimientosInventarioController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = MovimientoInventario::with([
                'producto',
                'pedido',
                'ventaLocal',
                'realizador',
            ])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.inventario.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
public function create()
{
    $productos = Producto::with('imagenPrincipal')
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
    /* ============================================
       💾 GUARDAR
    ============================================ */
    public function store(Request $request)
    {
        $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
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

                // ❌ No permitir ambas referencias al mismo tiempo
                if ($request->filled('id_pedido') && $request->filled('id_venta_local')) {
                    throw ValidationException::withMessages([
                        'id_pedido' => 'No puedes asociar el movimiento a un pedido y una venta local al mismo tiempo.',
                        'id_venta_local' => 'No puedes asociar el movimiento a un pedido y una venta local al mismo tiempo.',
                    ]);
                }

                // 🔒 Bloquear producto para evitar inconsistencias
                $producto = Producto::where('id_producto', $request->id_producto)
                    ->lockForUpdate()
                    ->firstOrFail();

                $stockActual = (int) $producto->stock_actual;
                $cantidad = (int) $request->cantidad;
                $tipo = $request->tipo;

                // 📊 Lógica de stock
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
                    // AJUSTE → reemplaza stock
                    $nuevoStock = $cantidad;
                }

                // 💾 Guardar movimiento
                MovimientoInventario::create([
                    'id_producto' => $producto->id_producto,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad,
                    'motivo' => trim($request->motivo),
                    'id_pedido' => $request->filled('id_pedido') ? $request->id_pedido : null,
                    'id_venta_local' => $request->filled('id_venta_local') ? $request->id_venta_local : null,
                    'id_usuario_realizador' => $request->id_usuario_realizador,
                    'notas' => $this->nullIfBlank($request->notas),
                ]);

                // 🔄 Actualizar stock
                $producto->update([
                    'stock_actual' => $nuevoStock,
                ]);
            });

            return redirect()
                ->route('admin.inventario-movimientos.index') // ✅ CORREGIDO
                ->with('success', 'Movimiento de inventario registrado correctamente.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar el movimiento de inventario.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = MovimientoInventario::with([
                'producto',
                'pedido',
                'ventaLocal',
                'realizador',
            ])
            ->findOrFail($id);

        return view('admin.inventario.show', compact('item'));
    }

    /* ============================================
       🔧 APOYO
    ============================================ */
    private function nullIfBlank($value): ?string
    {
        if ($value === null) return null;

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
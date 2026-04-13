<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\VentaLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VentasLocalesController extends Controller
{
    /* ============================================
       📋 LISTADO
    ============================================ */
    public function index()
    {
        $items = VentaLocal::with([
                'cajero',
                'pagos',
                'detalle',
                'venta',
            ])
            ->withSum('detalle as cantidad_items', 'cantidad')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.ventas_locales.index', compact('items'));
    }

    /* ============================================
       ➕ CREAR
    ============================================ */
    public function create()
    {
        $usuarios = Usuario::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        $productos = Producto::where('activo', 1)
            ->orderBy('nombre')
            ->get([
                'id_producto',
                'nombre',
                'sku',
                'codigo',
                'precio',
                'stock_actual',
            ]);

        return view('admin.ventas_locales.create', compact('usuarios', 'productos'));
    }

    /* ============================================
       💾 GUARDAR
    ============================================ */
    public function store(Request $request)
    {
        $data = $request->validate([
            'numero_ticket' => [
                'required',
                'string',
                'max:30',
                'unique:ventas_locales,numero_ticket',
            ],
            'id_usuario_cajero' => [
                'required',
                Rule::exists('usuarios', 'id_usuario')->where(fn ($q) => $q->where('activo', 1)),
            ],
            'nombre_cliente' => 'nullable|string|max:120',
            'telefono_cliente' => 'nullable|string|max:30',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string|max:255',

            'detalle' => 'required|array|min:1',
            'detalle.*.id_producto' => [
                'required',
                'integer',
                'distinct',
                'exists:productos,id_producto',
            ],
            'detalle.*.cantidad' => 'required|integer|min:1',

            'pagos' => 'required|array|min:1',
            'pagos.*.metodo' => [
                'required',
                Rule::in(['efectivo', 'tarjeta', 'sinpe', 'mixto']),
            ],
            'pagos.*.monto' => 'required|numeric|gt:0',
            'pagos.*.referencia' => 'nullable|string|max:80',
        ]);

        try {
            $ventaLocal = DB::transaction(function () use ($data) {
                $productoIds = collect($data['detalle'])
                    ->pluck('id_producto')
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values();

                $productos = Producto::whereIn('id_producto', $productoIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id_producto');

                if ($productos->count() !== $productoIds->count()) {
                    throw ValidationException::withMessages([
                        'detalle' => 'Uno o más productos no existen o no están disponibles.',
                    ]);
                }

                $subtotal = 0.00;
                $detalleRows = [];
                $movimientosRows = [];
                $now = now();

                foreach ($data['detalle'] as $index => $linea) {
                    $idProducto = (int) $linea['id_producto'];
                    $cantidad = (int) $linea['cantidad'];

                    /** @var \App\Models\Producto|null $producto */
                    $producto = $productos->get($idProducto);

                    if (!$producto) {
                        throw ValidationException::withMessages([
                            "detalle.$index.id_producto" => 'El producto seleccionado no es válido.',
                        ]);
                    }

                    if ((int) $producto->activo !== 1) {
                        throw ValidationException::withMessages([
                            "detalle.$index.id_producto" => "El producto {$producto->nombre} no está activo.",
                        ]);
                    }

                    if ((int) $producto->stock_actual < $cantidad) {
                        throw ValidationException::withMessages([
                            "detalle.$index.cantidad" => "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock_actual}.",
                        ]);
                    }

                    $precioUnitario = round((float) $producto->precio, 2);
                    $totalLinea = round($precioUnitario * $cantidad, 2);

                    $subtotal += $totalLinea;

                    $detalleRows[] = [
                        'id_producto' => $producto->id_producto,
                        'nombre_producto' => $producto->nombre,
                        'sku_snapshot' => $producto->sku,
                        'precio_unitario' => $precioUnitario,
                        'cantidad' => $cantidad,
                        'total_linea' => $totalLinea,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $subtotal = round($subtotal, 2);
                $descuento = round((float) ($data['descuento'] ?? 0), 2);

                if ($descuento > $subtotal) {
                    throw ValidationException::withMessages([
                        'descuento' => 'El descuento no puede ser mayor que el subtotal.',
                    ]);
                }

                $total = round($subtotal - $descuento, 2);

                $totalPagos = round(
                    collect($data['pagos'])->sum(fn ($pago) => (float) $pago['monto']),
                    2
                );

                if (abs($totalPagos - $total) > 0.009) {
                    throw ValidationException::withMessages([
                        'pagos' => 'La suma de los pagos debe coincidir exactamente con el total de la venta.',
                    ]);
                }

                $ventaLocal = VentaLocal::create([
                    'numero_ticket' => trim($data['numero_ticket']),
                    'id_usuario_cajero' => (int) $data['id_usuario_cajero'],
                    'nombre_cliente' => $this->nullIfBlank($data['nombre_cliente'] ?? null),
                    'telefono_cliente' => $this->nullIfBlank($data['telefono_cliente'] ?? null),
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'total' => $total,
                    'notas' => $this->nullIfBlank($data['notas'] ?? null),
                ]);

                foreach ($detalleRows as &$row) {
                    $row['id_venta_local'] = $ventaLocal->id_venta_local;
                }
                unset($row);

                DB::table('detalle_ventas_locales')->insert($detalleRows);

                $pagosRows = [];
                foreach ($data['pagos'] as $pago) {
                    $pagosRows[] = [
                        'id_venta_local' => $ventaLocal->id_venta_local,
                        'metodo' => $pago['metodo'],
                        'monto' => round((float) $pago['monto'], 2),
                        'referencia' => $this->nullIfBlank($pago['referencia'] ?? null),
                        'created_at' => $now,
                    ];
                }

                DB::table('pagos_ventas_locales')->insert($pagosRows);

                foreach ($data['detalle'] as $linea) {
                    $idProducto = (int) $linea['id_producto'];
                    $cantidad = (int) $linea['cantidad'];

                    /** @var \App\Models\Producto $producto */
                    $producto = $productos->get($idProducto);

                    $producto->decrement('stock_actual', $cantidad);

                    $movimientosRows[] = [
                        'id_producto' => $producto->id_producto,
                        'tipo' => 'salida',
                        'cantidad' => $cantidad,
                        'motivo' => 'Venta local',
                        'id_pedido' => null,
                        'id_venta_local' => $ventaLocal->id_venta_local,
                        'id_usuario_realizador' => (int) $data['id_usuario_cajero'],
                        'notas' => 'Ticket: ' . $ventaLocal->numero_ticket,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                DB::table('movimientos_inventario')->insert($movimientosRows);

                DB::table('ventas')->insert([
                    'canal' => 'local',
                    'id_pedido' => null,
                    'id_venta_local' => $ventaLocal->id_venta_local,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                return $ventaLocal;
            });

            return redirect()
                ->route('admin.ventas-locales.show', $ventaLocal->id_venta_local)
                ->with('success', 'Venta local registrada correctamente.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al registrar la venta local.');
        }
    }

    /* ============================================
       👁️ VER
    ============================================ */
    public function show(string $id)
    {
        $item = VentaLocal::with([
                'cajero',
                'detalle',
                'pagos',
                'venta',
                'movimientosInventario',
            ])
            ->findOrFail($id);

        return view('admin.ventas_locales.show', compact('item'));
    }

    /* ============================================
       ✏️ EDITAR
    ============================================ */
    public function edit(string $id)
    {
        return redirect()
            ->route('admin.ventas-locales.show', $id)
            ->with('error', 'La edición directa de ventas locales está deshabilitada para proteger inventario, pagos y trazabilidad.');
    }

    /* ============================================
       ♻️ ACTUALIZAR
    ============================================ */
    public function update(Request $request, string $id)
    {
        return redirect()
            ->route('admin.ventas-locales.show', $id)
            ->with('error', 'La actualización directa de ventas locales está deshabilitada para proteger inventario, pagos y trazabilidad.');
    }

    /* ============================================
       🗑️ ELIMINAR
    ============================================ */
    public function destroy(string $id)
    {
        return redirect()
            ->route('admin.ventas-locales.show', $id)
            ->with('error', 'La eliminación de ventas locales está deshabilitada. Lo correcto es implementar una anulación controlada.');
    }

    /* ============================================
       HELPERS
    ============================================ */
    private function nullIfBlank($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
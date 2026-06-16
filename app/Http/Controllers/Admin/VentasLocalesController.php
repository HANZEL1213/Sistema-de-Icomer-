<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\VentaLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class VentasLocalesController extends Controller
{
    public function index()
    {
        $items = VentaLocal::with([
                'cajero',
                'pagos',
                'detalle.variante.opcion',
                'venta',
            ])
            ->withSum('detalle as cantidad_items', 'cantidad')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.ventas_locales.index', compact('items'));
    }

    public function create()
    {
        $productos = Producto::with([
                'imagenPrincipal',
                'tipoVariante',
                'variantePrincipal.opcion',
                'variantesActivas.opcion',
            ])
            ->where('activo', 1)
            ->orderBy('nombre')
            ->get([
                'id_producto',
                'nombre',
                'sku',
                'codigo',
                'precio',
                'descuento_activo',
                'precio_descuento',
                'stock_actual',
                'usa_variantes',
                'id_tipo_variante',
            ]);

        return view('admin.ventas_locales.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_cliente' => 'nullable|string|max:120',
            'telefono_cliente' => 'nullable|string|max:30',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string|max:255',

            'detalle' => 'required|array|min:1',
            'detalle.*.id_producto' => [
                'required',
                'integer',
                'exists:productos,id_producto',
            ],
            'detalle.*.id_producto_variante' => [
                'nullable',
                'integer',
                'exists:producto_variantes,id_producto_variante',
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
                $idCajero = Auth::id() ?? 1;
                $numeroTicket = 'POS-' . now()->format('YmdHisv');
                $now = now();

                $subtotal = 0.00;
                $detalleRows = [];

                foreach ($data['detalle'] as $index => $linea) {
                    $idProducto = (int) $linea['id_producto'];
                    $idVariante = !empty($linea['id_producto_variante'])
                        ? (int) $linea['id_producto_variante']
                        : null;

                    $cantidad = (int) $linea['cantidad'];

                    $producto = Producto::where('id_producto', $idProducto)
                        ->lockForUpdate()
                        ->first();

                    if (!$producto || !$producto->activo) {
                        throw ValidationException::withMessages([
                            "detalle.$index.id_producto" => 'El producto seleccionado no está disponible.',
                        ]);
                    }

                    if ($producto->usa_variantes) {
                        if (!$idVariante) {
                            throw ValidationException::withMessages([
                                "detalle.$index.id_producto_variante" => "Debes seleccionar una variante para {$producto->nombre}.",
                            ]);
                        }

                        $variante = ProductoVariante::with('opcion')
                            ->where('id_producto_variante', $idVariante)
                            ->where('id_producto', $producto->id_producto)
                            ->where('activo', 1)
                            ->lockForUpdate()
                            ->first();

                        if (!$variante) {
                            throw ValidationException::withMessages([
                                "detalle.$index.id_producto_variante" => "La variante seleccionada para {$producto->nombre} no es válida.",
                            ]);
                        }

                        if ($cantidad > $variante->stock_actual) {
                            throw ValidationException::withMessages([
                                "detalle.$index.cantidad" => "Stock insuficiente para {$producto->nombre}. Disponible: {$variante->stock_actual}.",
                            ]);
                        }

                        $nombreVariante = $variante->nombre
                            ?: ($variante->opcion?->etiqueta ?? $variante->opcion?->valor ?? 'Variante');

                        $precioUnitario = $variante->precioVenta();
                        $precioOriginal = $variante->precioOriginal();

                        $promocionAplicada =
                            $variante->promocionVigente()
                            && $precioOriginal > $precioUnitario;

                        $totalLinea = round($precioUnitario * $cantidad, 2);
                        $subtotal += $totalLinea;

                        $detalleRows[] = [
                            'id_producto' => $producto->id_producto,
                            'id_producto_variante' => $variante->id_producto_variante,
                            'nombre_producto' => $producto->nombre . ' - ' . $nombreVariante,
                            'sku_snapshot' => $variante->sku ?? $producto->sku,
                            'precio_original' => $precioOriginal,
                            'precio_unitario' => $precioUnitario,
                            'cantidad' => $cantidad,
                            'total_linea' => $totalLinea,
                            'promocion_aplicada' => $promocionAplicada ? 1 : 0,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        continue;
                    }

                    if ($idVariante) {
                        throw ValidationException::withMessages([
                            "detalle.$index.id_producto_variante" => "{$producto->nombre} no usa variantes.",
                        ]);
                    }

                    if ($cantidad > $producto->stock_actual) {
                        throw ValidationException::withMessages([
                            "detalle.$index.cantidad" => "Stock insuficiente para {$producto->nombre}. Disponible: {$producto->stock_actual}.",
                        ]);
                    }

                    $precioUnitario = $producto->precioVenta();
                    $precioOriginal = round((float) $producto->precio, 2);

                    $promocionAplicada =
                        $producto->tienePromocionActiva()
                        && $precioOriginal > $precioUnitario;

                    $totalLinea = round($precioUnitario * $cantidad, 2);
                    $subtotal += $totalLinea;

                    $detalleRows[] = [
                        'id_producto' => $producto->id_producto,
                        'id_producto_variante' => null,
                        'nombre_producto' => $producto->nombre,
                        'sku_snapshot' => $producto->sku,
                        'precio_original' => $precioOriginal,
                        'precio_unitario' => $precioUnitario,
                        'cantidad' => $cantidad,
                        'total_linea' => $totalLinea,
                        'promocion_aplicada' => $promocionAplicada ? 1 : 0,
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
                    'numero_ticket' => $numeroTicket,
                    'id_usuario_cajero' => $idCajero,
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

                foreach ($data['pagos'] as $pago) {
                    DB::table('pagos_ventas_locales')->insert([
                        'id_venta_local' => $ventaLocal->id_venta_local,
                        'metodo' => $pago['metodo'],
                        'monto' => round((float) $pago['monto'], 2),
                        'referencia' => $this->nullIfBlank($pago['referencia'] ?? null),
                        'created_at' => $now,
                    ]);
                }

                foreach ($detalleRows as $row) {
                    if ($row['id_producto_variante']) {
                        $variante = ProductoVariante::where('id_producto_variante', $row['id_producto_variante'])
                            ->where('id_producto', $row['id_producto'])
                            ->lockForUpdate()
                            ->first();

                        $variante->registrarSalidaInventario(
                            $row['cantidad'],
                            'Venta local',
                            null,
                            $ventaLocal->id_venta_local,
                            $idCajero,
                            'Ticket: ' . $ventaLocal->numero_ticket
                        );

                        continue;
                    }

                    $producto = Producto::where('id_producto', $row['id_producto'])
                        ->lockForUpdate()
                        ->first();

                    $producto->registrarSalidaInventario(
                        $row['cantidad'],
                        'Venta local',
                        null,
                        $ventaLocal->id_venta_local,
                        $idCajero,
                        'Ticket: ' . $ventaLocal->numero_ticket
                    );
                }

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
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al registrar la venta local.');
        }
    }

    public function show(string $id)
    {
        $item = VentaLocal::with([
                'cajero',
                'detalle.producto.imagenPrincipal',
                'detalle.variante.opcion',
                'pagos',
                'venta',
                'movimientosInventario',
            ])
            ->findOrFail($id);

        return view('admin.ventas_locales.show', compact('item'));
    }

    public function edit(string $id)
    {
        return redirect()
            ->route('admin.ventas-locales.show', $id)
            ->with('error', 'La edición directa de ventas locales está deshabilitada para proteger inventario, pagos y trazabilidad.');
    }

    public function update(Request $request, string $id)
    {
        return redirect()
            ->route('admin.ventas-locales.show', $id)
            ->with('error', 'La actualización directa de ventas locales está deshabilitada para proteger inventario, pagos y trazabilidad.');
    }

    public function destroy(string $id)
    {
        return redirect()
            ->route('admin.ventas-locales.show', $id)
            ->with('error', 'La eliminación de ventas locales está deshabilitada. Lo correcto es implementar una anulación controlada.');
    }

    public function ticket(string $id)
    {
        $item = VentaLocal::with([
                'cajero',
                'detalle.producto.imagenPrincipal',
                'detalle.variante.opcion',
                'pagos',
                'venta',
            ])
            ->findOrFail($id);

        return view('admin.ventas_locales.ticket', compact('item'));
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
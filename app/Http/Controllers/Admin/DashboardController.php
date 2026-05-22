<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\VentaLocal;
use App\Models\MovimientoInventario;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        /* ====================== VENTAS ====================== */

        $ventasOnlineHoy = Pedido::whereDate('created_at', $hoy)
            ->whereIn('estado', [
                'pagado_verificado',
                'preparando',
                'enviado',
                'entregado',
            ])
            ->sum('total');

        $ventasLocalesHoy = VentaLocal::whereDate('created_at', $hoy)
            ->sum('total');

        $ventasHoy = $ventasOnlineHoy + $ventasLocalesHoy;

        $ingresosMesOnline = Pedido::whereBetween('created_at', [$inicioMes, now()])
            ->whereIn('estado', [
                'pagado_verificado',
                'preparando',
                'enviado',
                'entregado',
            ])
            ->sum('total');

        $ingresosMesLocal = VentaLocal::whereBetween('created_at', [$inicioMes, now()])
            ->sum('total');

        $ingresosMes = $ingresosMesOnline + $ingresosMesLocal;

        /* ====================== PEDIDOS ====================== */

        $pagosRevision = Pedido::where('estado', 'en_revision')->count();

        $pedidosPendientes = Pedido::whereIn('estado', [
            'pendiente_pago',
            'en_revision',
            'preparando',
        ])->count();

        $pedidosPorEntregar = Pedido::whereIn('estado', [
            'preparando',
            'enviado',
        ])->count();

        /* ====================== INVENTARIO ====================== */

        $limiteBajoStock = 3;

        $productosActivos = Producto::where('activo', 1)->count();
        $productosSinStock = Producto::where('activo', 1)
            ->where('stock_actual', '<=', 0)
            ->count();

        $productosBajoStock = Producto::where('activo', 1)
            ->whereBetween('stock_actual', [1, $limiteBajoStock])
            ->count();

        $movimientosHoy = MovimientoInventario::whereDate('created_at', $hoy)->count();

        /* ====================== MÉTRICAS INVENTARIO ====================== */

        $totalProductos = max($productosActivos, 1);

        $inventarioMetricas = [
            'porcentaje_activos' => 100,
            'porcentaje_sin_stock' => min(($productosSinStock / $totalProductos) * 100, 100),
            'porcentaje_bajo_stock' => min(($productosBajoStock / $totalProductos) * 100, 100),
            'porcentaje_movimientos' => min(($movimientosHoy / 50) * 100, 100),
        ];

        $resumenInventario = [
            'productos_activos' => $productosActivos,
            'sin_stock' => $productosSinStock,
            'bajo_stock' => $productosBajoStock,
            'movimientos_hoy' => $movimientosHoy,
        ];

        /* ====================== DATOS PARA LA VISTA ====================== */

        $movimientosRecientes = MovimientoInventario::with('producto')
            ->latest()
            ->take(3)
            ->get();

        // Pedidos recientes
        $pedidosRecientes = Pedido::with(['pagoUltimo', 'usuario'])
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($pedido) {
                $estadoTexto = $this->nombreEstadoVentaOnline($pedido->estado);
                $estadoColor = $this->colorEstado($pedido->estado);

                $pagoTexto = match ($pedido->estado) {
                    'pagado_verificado', 'preparando', 'enviado', 'entregado' => 'Verificado',
                    'rechazado' => 'Rechazado',
                    default => 'Pendiente',
                };

                $pagoColor = match ($pedido->estado) {
                    'pagado_verificado', 'preparando', 'enviado', 'entregado' => 'success',
                    'rechazado' => 'danger',
                    default => 'warning',
                };

                $clienteNombre = $pedido->nombre_cliente ?? optional($pedido->usuario)->name ?? 'Cliente';
                $clienteTipo = $pedido->id_usuario ? 'Cliente registrado' : 'Invitado';

                return (object) [
                    'id_pedido' => $pedido->id_pedido,
                    'numero_pedido' => $pedido->numero_pedido,
                    'total' => $pedido->total,
                    'created_at' => $pedido->created_at,
                    'estado' => $pedido->estado,
                    'estado_texto' => $estadoTexto,
                    'estado_color' => $estadoColor,
                    'pago_texto' => $pagoTexto,
                    'pago_color' => $pagoColor,
                    'cliente_nombre' => $clienteNombre,
                    'cliente_tipo' => $clienteTipo,
                    'url' => route('admin.pedidos.show', $pedido->id_pedido),
                ];
            });

        // Ventas recientes (online + local)
        $ventasLocalesRecientes = VentaLocal::latest()->take(4)->get();
        $pedidosOnlineRecientes = Pedido::latest()->take(4)->get();

        $ventasRecientes = collect()
            ->merge($pedidosOnlineRecientes->map(fn($pedido) => [
                'referencia' => $pedido->numero_pedido,
                'descripcion' => 'Venta online · ' . $pedido->created_at->diffForHumans(),
                'canal_key' => 'online',
                'canal' => 'Online',
                'total' => $pedido->total,
                'fecha' => $pedido->created_at->timestamp,
                'estado' => $this->nombreEstadoVentaOnline($pedido->estado),
                'badge' => $this->colorEstado($pedido->estado),
                'icono' => 'bx-shopping-bag',
                'url' => route('admin.pedidos.show', $pedido->id_pedido),
            ]))
            ->merge($ventasLocalesRecientes->map(fn($venta) => [
                'referencia' => $venta->numero_ticket,
                'descripcion' => 'Venta local · ' . $venta->created_at->diffForHumans(),
                'canal_key' => 'local',
                'canal' => 'Local',
                'total' => $venta->total,
                'fecha' => $venta->created_at->timestamp,
                'estado' => 'Completada',
                'badge' => 'success',
                'icono' => 'bx-store',
                'url' => route('admin.ventas-locales.show', $venta->id_venta_local),
            ]))
            ->sortByDesc('fecha')
            ->take(4)
            ->values();

        $ventasSemana = $this->ventasUltimosSieteDias();

        /* ====================== FLUJO DE PEDIDOS ====================== */

        $estadosPedidos = [
            ['nombre' => 'Pendiente de pago', 'estado' => 'pendiente_pago', 'color' => 'secondary', 'icono' => 'bx-time'],
            ['nombre' => 'En revisión', 'estado' => 'en_revision', 'color' => 'warning', 'icono' => 'bx-search-alt'],
            ['nombre' => 'Pagado verificado', 'estado' => 'pagado_verificado', 'color' => 'success', 'icono' => 'bx-check-circle'],
            ['nombre' => 'Preparando', 'estado' => 'preparando', 'color' => 'info', 'icono' => 'bx-package'],
            ['nombre' => 'Enviado', 'estado' => 'enviado', 'color' => 'primary', 'icono' => 'bx-truck'],
            ['nombre' => 'Entregado', 'estado' => 'entregado', 'color' => 'dark', 'icono' => 'bx-home-circle'],
        ];

        $totalPedidosSistema = Pedido::count();

        foreach ($estadosPedidos as &$estado) {
            $cantidad = Pedido::where('estado', $estado['estado'])->count();
            $estado['cantidad'] = $cantidad;
            $estado['porcentaje'] = $totalPedidosSistema > 0 ? ($cantidad / $totalPedidosSistema) * 100 : 0;
        }

        /* ====================== KPIS ====================== */

        $kpis = [
            'ventas_hoy' => $ventasHoy,
            'ingresos_mes' => $ingresosMes,
            'pedidos_pendientes' => $pedidosPendientes,
            'pagos_revision' => $pagosRevision,
            'productos_bajo_stock' => $productosBajoStock,
            'pedidos_por_entregar' => $pedidosPorEntregar,
        ];

        /* ====================== ACCIONES RÁPIDAS ====================== */

        $accionesRapidas = [
            ['titulo' => 'Revisar pagos', 'subtitulo' => 'Comprobantes', 'icono' => 'bx-search-alt', 'url' => route('admin.pedidos.index', ['estado' => 'en_revision'])],
            ['titulo' => 'Crear producto', 'subtitulo' => 'Catálogo', 'icono' => 'bx-plus-circle', 'url' => route('admin.productos.create')],
            ['titulo' => 'Venta física', 'subtitulo' => 'Caja local', 'icono' => 'bx-store', 'url' => route('admin.ventas-locales.create')],
            ['titulo' => 'Inventario', 'subtitulo' => 'Movimientos', 'icono' => 'bx-package', 'url' => route('admin.inventario-movimientos.index')],
            ['titulo' => 'Configuración', 'subtitulo' => 'Tienda', 'icono' => 'bx-cog', 'url' => route('admin.configuracion.index')],
            ['titulo' => 'Pendientes', 'subtitulo' => 'Alertas', 'icono' => 'bx-error-circle', 'url' => route('admin.pedidos.index')],
        ];

        /* ====================== HERO PANEL ====================== */

        $heroPanel = [
            'tienda_estado' => ['texto' => 'Online', 'icono' => 'bx-check-circle', 'color' => 'success'],
            'pagos_revision' => ['texto' => $pagosRevision, 'icono' => 'bx-search-alt', 'color' => 'warning'],
            'fecha_operativa' => ['texto' => $hoy->format('d/m/Y'), 'icono' => 'bx-calendar', 'color' => 'info'],
        ];

        return view('admin.dashboard', compact(
            'kpis',
            'pedidosRecientes',
            'ventasRecientes',
            'ventasSemana',
            'resumenInventario',
            'inventarioMetricas',
            'accionesRapidas',
            'pagosRevision',
            'movimientosRecientes',
            'estadosPedidos',
            'totalPedidosSistema',
            'heroPanel'
        ));
    }

    // ====================== MÉTODOS PRIVADOS ======================

    private function ventasUltimosSieteDias()
    {
        $dias = collect();

        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);

            $online = Pedido::whereDate('created_at', $fecha)
                ->whereIn('estado', ['pagado_verificado', 'preparando', 'enviado', 'entregado'])
                ->sum('total');

            $local = VentaLocal::whereDate('created_at', $fecha)->sum('total');

            $total = $online + $local;

            $dias->push([
                'dia' => ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'][$fecha->dayOfWeek],
                'fecha' => $fecha->format('Y-m-d'),
                'total' => $total,
                'altura' => $total > 0 ? min(max(($total / 1500), 40), 220) : 14,
            ]);
        }

        return $dias;
    }

    private function colorEstado($estado)
    {
        return match ($estado) {
            'pendiente_pago', 'en_revision' => 'warning',
            'pagado_verificado', 'entregado' => 'success',
            'preparando' => 'info',
            'enviado' => 'primary',
            'rechazado' => 'danger',
            default => 'primary',
        };
    }

    private function nombreEstadoVentaOnline($estado)
    {
        return match ($estado) {
            'pendiente_pago' => 'Pendiente',
            'en_revision' => 'Revisión',
            'pagado_verificado' => 'Pagada',
            'preparando' => 'Preparando',
            'enviado' => 'Enviada',
            'entregado' => 'Entregada',
            'rechazado' => 'Rechazada',
            default => ucfirst(str_replace('_', ' ', $estado)),
        };
    }
}
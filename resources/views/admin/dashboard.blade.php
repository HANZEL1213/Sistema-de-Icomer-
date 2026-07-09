@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- CSS dashboard.bladee --}}
    <link rel="stylesheet" href="{{ asset('assets/css/modules/dashboard.blade.css') }}">

    <div class="dash-page">

        {{-- HERO --}}

        <div class="dash-hero mb-4">
            <div class="dash-hero-content">

                <div class="row align-items-center g-4">

                    <div class="col-xl-8">

                        <span class="dash-hero-badge">
                            <i class="bx bx-store-alt"></i>
                            Centro de mando
                        </span>

                        <h2 class="dash-hero-title">
                            Dashboard Administrativo
                        </h2>

                        <p class="dash-hero-subtitle">
                            Controla ventas, pedidos, pagos, inventario y alertas críticas desde una sola vista operativa.
                        </p>

                    </div>

                    <div class="col-xl-4">
                        <div class="dash-hero-panel">

                            <div class="dash-health-item">
                                <span>
                                    <i
                                        class="bx {{ $heroPanel['tienda_estado']['icono'] }}
               text-{{ $heroPanel['tienda_estado']['color'] }} me-1"></i>

                                    Tienda activa
                                </span>

                                <strong>
                                    {{ $heroPanel['tienda_estado']['texto'] }}
                                </strong>
                            </div>

                            <div class="dash-health-item">
                                <span>
                                    <i
                                        class="bx {{ $heroPanel['pagos_revision']['icono'] }}
               text-{{ $heroPanel['pagos_revision']['color'] }} me-1"></i>

                                    Pagos por revisar
                                </span>

                                <strong>
                                    {{ $heroPanel['pagos_revision']['texto'] }}
                                </strong>
                            </div>

                            <div class="dash-health-item">
                                <span>
                                    <i
                                        class="bx {{ $heroPanel['fecha_operativa']['icono'] }}
               text-{{ $heroPanel['fecha_operativa']['color'] }} me-1"></i>

                                    Fecha operativa
                                </span>

                                <strong>
                                    {{ $heroPanel['fecha_operativa']['texto'] }}
                                </strong>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        {{-- KPIS --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">

            {{-- VENTAS HOY --}}
            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#2563eb,#1e40af);">
                    <div class="dash-kpi-body">

                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon">
                                <i class="bx bx-cart"></i>
                            </div>

                            <span class="dash-trend">
                                Hoy
                            </span>
                        </div>

                        <div class="dash-kpi-value">
                            ₡{{ number_format($kpis['ventas_hoy'], 2) }}
                        </div>

                        <div class="dash-kpi-label">
                            Ventas de hoy
                        </div>

                        <div class="dash-kpi-footer">
                            <span>Total generado hoy</span>
                            <strong>Online + Local</strong>
                        </div>

                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 72%"></div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- INGRESOS DEL MES --}}
            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#dca117,#b45309);">
                    <div class="dash-kpi-body">

                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon">
                                <i class="bx bx-wallet"></i>
                            </div>

                            <span class="dash-trend">
                                Mes actual
                            </span>
                        </div>

                        <div class="dash-kpi-value">
                            ₡{{ number_format($kpis['ingresos_mes'], 2) }}
                        </div>

                        <div class="dash-kpi-label">
                            Ingresos del mes
                        </div>

                        <div class="dash-kpi-footer">
                            <span>Ventas acumuladas</span>
                            <strong>Mensual</strong>
                        </div>

                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 64%"></div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- PEDIDOS PENDIENTES --}}
            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#16a34a,#166534);">
                    <div class="dash-kpi-body">

                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon">
                                <i class="bx bx-time-five"></i>
                            </div>

                            <span class="dash-trend">
                                Seguimiento
                            </span>
                        </div>

                        <div class="dash-kpi-value">
                            {{ $kpis['pedidos_pendientes'] }}
                        </div>

                        <div class="dash-kpi-label">
                            Pedidos pendientes
                        </div>

                        <div class="dash-kpi-footer">
                            <span>Requieren atención</span>
                            <strong>Operativo</strong>
                        </div>

                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 45%"></div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- PAGOS EN REVISION --}}
            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#dc2626,#991b1b);">
                    <div class="dash-kpi-body">

                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon">
                                <i class="bx bx-search-alt"></i>
                            </div>

                            <span class="dash-trend">
                                Urgente
                            </span>
                        </div>

                        <div class="dash-kpi-value">
                            {{ $kpis['pagos_revision'] }}
                        </div>

                        <div class="dash-kpi-label">
                            Pagos en revisión
                        </div>

                        <div class="dash-kpi-footer">
                            <span>Validar comprobantes</span>
                            <strong>Prioridad</strong>
                        </div>

                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 38%"></div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- BAJO STOCK --}}
            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#475569,#0f172a);">
                    <div class="dash-kpi-body">

                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon">
                                <i class="bx bx-package"></i>
                            </div>

                            <span class="dash-trend">
                                Revisar
                            </span>
                        </div>

                        <div class="dash-kpi-value">
                            {{ $kpis['productos_bajo_stock'] }}
                        </div>

                        <div class="dash-kpi-label">
                            Productos bajo stock
                        </div>

                        <div class="dash-kpi-footer">
                            <span>Inventario crítico</span>
                            <strong>Stock</strong>
                        </div>

                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 52%"></div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- PEDIDOS POR ENTREGAR --}}
            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#7c3aed,#4c1d95);">
                    <div class="dash-kpi-body">

                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon">
                                <i class="bx bx-send"></i>
                            </div>

                            <span class="dash-trend">
                                Activos
                            </span>
                        </div>

                        <div class="dash-kpi-value">
                            {{ $kpis['pedidos_por_entregar'] }}
                        </div>

                        <div class="dash-kpi-label">
                            Pedidos por entregar
                        </div>

                        <div class="dash-kpi-footer">
                            <span>Preparando / enviados</span>
                            <strong>Logística</strong>
                        </div>

                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 58%"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- ACCIONES --}}
        <div class="dash-card mb-4">
            <div class="dash-card-header">
                <div>
                    <h5 class="dash-title">Acciones rápidas</h5>
                    <div class="dash-subtitle">
                        Atajos principales para operar la tienda sin perder tiempo.
                    </div>
                </div>

                <span class="dash-pill">
                    <i class="bx bx-shield-quarter"></i>
                    Modo administrador
                </span>
            </div>

            <div class="card-body p-4">

                <div class="row g-3">

                    @foreach ($accionesRapidas as $accion)
                        <div class="col-6 col-md-4 col-xl-2">

                            <a href="{{ $accion['url'] }}" class="dash-action">

                                <div class="dash-action-icon">
                                    <i class="bx {{ $accion['icono'] }}"></i>
                                </div>

                                <strong>
                                    {{ $accion['titulo'] }}
                                </strong>

                                <small class="text-muted d-block">
                                    {{ $accion['subtitulo'] }}
                                </small>

                            </a>

                        </div>
                    @endforeach

                </div>

            </div>
        </div>

        {{-- GRÁFICO --}}
        <div class="row g-4 mb-4">

            <div class="col-12">

                <div class="dash-card h-100">

                    <div class="dash-card-header">

                        <div>
                            <h5 class="dash-title">
                                Ventas últimos 7 días
                            </h5>

                            <div class="dash-subtitle">
                                Lectura rápida del rendimiento semanal.
                            </div>
                        </div>

                        <span class="dash-pill">
                            ₡{{ number_format(collect($ventasSemana)->sum('total'), 2) }} total
                        </span>

                    </div>

                    <div class="card-body p-4 pt-2">

                        <div class="dash-chart">

                            @php
                                $maxVenta = collect($ventasSemana)->max('total');
                            @endphp

                            @foreach ($ventasSemana as $dia)
                                @php
                                    $altura = $maxVenta > 0 ? max(($dia['total'] / $maxVenta) * 220, 35) : 35;
                                @endphp

                                <div class="dash-chart-col">

                                    <div class="dash-chart-tooltip">
                                        ₡{{ number_format($dia['total'], 2) }}
                                    </div>

                                    <div class="dash-chart-bar" style="height: {{ $altura }}px;"></div>

                                    <span class="dash-chart-label">
                                        {{ $dia['dia'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FLUJO + INVENTARIO --}}
        <div class="row g-4 mb-4">

            {{-- FLUJO DE PEDIDOS --}}
            <div class="col-xl-4">

                <div class="dash-card h-100">

                    <div class="dash-card-header">

                        <div>

                            <h5 class="dash-title">
                                Flujo de pedidos
                            </h5>

                            <div class="dash-subtitle">
                                Estado operativo actual de los pedidos.
                            </div>

                        </div>

                        <span class="dash-pill">
                            {{ $totalPedidosSistema ?? Pedido::count() }}
                            pedidos
                        </span>

                    </div>

                    <div class="card-body p-4 pt-2">

                        {{-- ALERTA --}}
                        <div class="dash-priority mb-4">

                            <div class="d-flex align-items-start gap-3">

                                <div class="dash-alert-icon text-warning">
                                    <i class="bx bx-search-alt"></i>
                                </div>

                                <div>

                                    <strong>
                                        Pedidos pendientes de validación
                                    </strong>

                                    <small class="text-muted d-block">

                                        Actualmente hay
                                        {{ $pagosRevision }}
                                        pedidos esperando revisión administrativa.

                                    </small>

                                </div>

                            </div>

                        </div>

                        {{-- LISTADO --}}
                        <div class="dash-status-row">

                            @foreach ($estadosPedidos as $estado)
                                <div class="dash-status-item">

                                    <div class="dash-status-name">

                                        <span>

                                            <span class="dash-dot bg-{{ $estado['color'] }}"></span>

                                            {{ $estado['nombre'] }}

                                        </span>

                                        <strong>
                                            {{ $estado['cantidad'] }}
                                        </strong>

                                    </div>

                                    <div class="progress" style="height: 9px;">

                                        <div class="progress-bar bg-{{ $estado['color'] }}"
                                            style="width: {{ $estado['porcentaje'] }}%"></div>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">

                            <small class="text-muted">
                                Total pedidos registrados
                            </small>

                            <strong>
                                {{ $totalPedidosSistema }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

            {{-- INVENTARIO --}}
            <div class="col-xl-8">

                <div class="dash-card h-100 overflow-hidden">

                    <div class="dash-card-header">

                        <div>

                            <h5 class="dash-title">
                                Resumen de inventario
                            </h5>

                            <div class="dash-subtitle">
                                Estado operativo y movimientos recientes del stock.
                            </div>

                        </div>

                        <span class="dash-pill">
                            <i class="bx bx-package me-1"></i>
                            Inventario activo
                        </span>

                    </div>

                    <div class="card-body p-4 pt-2">

                        {{-- MINI KPIS --}}
                        <div class="dash-mini-grid mb-4">

                            {{-- PRODUCTOS ACTIVOS --}}
                            <div class="dash-mini dash-mini-hover">

                                <div class="d-flex justify-content-between align-items-start">

                                    <div>

                                        <small class="text-muted">
                                            Productos activos
                                        </small>

                                        <h4>
                                            {{ $resumenInventario['productos_activos'] }}
                                        </h4>

                                    </div>

                                    <div class="dash-mini-icon bg-primary-subtle text-primary">
                                        <i class="bx bx-package"></i>
                                    </div>

                                </div>

                                <div class="progress mt-3" style="height: 7px;">

                                    <div class="progress-bar bg-primary"
                                        style="width: {{ $inventarioMetricas['porcentaje_activos'] }}%"></div>

                                </div>

                            </div>

                            {{-- SIN STOCK --}}
                            <div class="dash-mini dash-mini-hover">

                                <div class="d-flex justify-content-between align-items-start">

                                    <div>

                                        <small class="text-muted">
                                            Sin stock
                                        </small>

                                        <h4 class="text-danger">
                                            {{ $resumenInventario['sin_stock'] }}
                                        </h4>

                                    </div>

                                    <div class="dash-mini-icon bg-danger-subtle text-danger">
                                        <i class="bx bx-x-circle"></i>
                                    </div>

                                </div>

                                <div class="progress mt-3" style="height: 7px;">

                                    <div class="progress-bar bg-danger"
                                        style="width: {{ $inventarioMetricas['porcentaje_sin_stock'] }}%"></div>

                                </div>

                            </div>

                            {{-- BAJO STOCK --}}
                            <div class="dash-mini dash-mini-hover">

                                <div class="d-flex justify-content-between align-items-start">

                                    <div>

                                        <small class="text-muted">
                                            Bajo stock
                                        </small>

                                        <h4 class="text-warning">
                                            {{ $resumenInventario['bajo_stock'] }}
                                        </h4>

                                    </div>

                                    <div class="dash-mini-icon bg-warning-subtle text-warning">
                                        <i class="bx bx-error"></i>
                                    </div>

                                </div>

                                <div class="progress mt-3" style="height: 7px;">

                                    <div class="progress-bar bg-warning"
                                        style="width: {{ $inventarioMetricas['porcentaje_bajo_stock'] }}%"></div>

                                </div>

                            </div>

                            {{-- MOVIMIENTOS --}}
                            <div class="dash-mini dash-mini-hover">

                                <div class="d-flex justify-content-between align-items-start">

                                    <div>

                                        <small class="text-muted">
                                            Movimientos hoy
                                        </small>

                                        <h4 class="text-success">
                                            {{ $resumenInventario['movimientos_hoy'] }}
                                        </h4>

                                    </div>

                                    <div class="dash-mini-icon bg-success-subtle text-success">
                                        <i class="bx bx-transfer-alt"></i>
                                    </div>

                                </div>

                                <div class="progress mt-3" style="height: 7px;">

                                    <div class="progress-bar bg-success"
                                        style="width: {{ $inventarioMetricas['porcentaje_movimientos'] }}%"></div>

                                </div>

                            </div>

                        </div>

                        {{-- ACTIVIDAD --}}
                        <div class="dash-stock-activity">

                            @forelse ($movimientosRecientes as $movimiento)
                                @php

                                    $esEntrada = $movimiento->tipo === 'entrada';

                                    $color = $esEntrada ? 'success' : 'danger';

                                    $icono = $esEntrada ? 'bx-plus' : 'bx-minus';

                                    $signo = $esEntrada ? '+' : '-';

                                @endphp

                                <div class="dash-stock-item">

                                    <div class="dash-stock-line {{ $color }}"></div>

                                    <div class="dash-stock-icon bg-{{ $color }}-subtle text-{{ $color }}">
                                        <i class="bx {{ $icono }}"></i>
                                    </div>

                                    <div class="flex-grow-1">

                                        <div class="dash-stock-title">
                                            {{ ucfirst($movimiento->motivo) }}
                                        </div>

                                        <div class="dash-stock-sub">

                                            {{ $movimiento->producto->nombre ?? 'Producto eliminado' }}
                                            ·
                                            {{ $movimiento->created_at->diffForHumans() }}

                                        </div>

                                    </div>

                                    <strong class="text-{{ $color }}">
                                        {{ $signo }}{{ $movimiento->cantidad }}
                                    </strong>

                                </div>

                            @empty

                                <div class="text-center py-4 text-muted">

                                    <i class="bx bx-package mb-2 d-block fs-2"></i>

                                    No hay movimientos recientes.

                                </div>
                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <br>

        {{-- PEDIDOS RECIENTES --}}
        <div class="col-12">

            <div class="dash-card">

                <div class="dash-card-header">

                    <div>

                        <h5 class="dash-title">
                            Pedidos recientes
                        </h5>

                        <div class="dash-subtitle">
                            Últimos pedidos generados en la tienda.
                        </div>

                    </div>

                    <span class="dash-pill">
                        Últimos {{ $pedidosRecientes->count() }}
                    </span>

                </div>

                <div class="card-body p-4 pt-2">

                    <div class="dash-list">

                        @forelse ($pedidosRecientes as $pedido)
                            <div class="dash-list-item">

                                {{-- PEDIDO --}}
                                <div>

                                    <span class="dash-list-label">
                                        Pedido
                                    </span>

                                    <div class="dash-list-main">

                                        <div class="dash-list-icon">
                                            <i class="bx bx-receipt"></i>
                                        </div>

                                        <div>

                                            <div class="dash-list-title">
                                                {{ $pedido->numero_pedido }}
                                            </div>

                                            <div class="dash-list-sub">
                                                {{ $pedido->created_at->diffForHumans() }}
                                                ·
                                                Pedido online
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                {{-- CLIENTE --}}
                                <div>

                                    <span class="dash-list-label">
                                        Cliente
                                    </span>

                                    <div class="dash-client-name">
                                        {{ $pedido->cliente_nombre }}
                                    </div>

                                    <div class="dash-client-type">
                                        {{ $pedido->cliente_tipo }}
                                    </div>

                                </div>

                                {{-- TOTAL --}}
                                <div>

                                    <span class="dash-list-label">
                                        Total
                                    </span>

                                    <div class="dash-money">
                                        ₡{{ number_format($pedido->total, 2) }}
                                    </div>

                                </div>

                                {{-- ESTADO --}}
                                <div>

                                    <span class="dash-list-label">
                                        Estado
                                    </span>

                                    <span class="dash-status-badge dash-badge-{{ $pedido->estado_color }}">
                                        <i class="bx bx-check-circle"></i>
                                        {{ $pedido->estado_texto }}
                                    </span>

                                </div>

                                {{-- PAGO --}}
                                <div>

                                    <span class="dash-list-label">
                                        Pago
                                    </span>

                                    <span class="dash-status-badge dash-badge-{{ $pedido->pago_color }}">
                                        <i class="bx bx-check-shield"></i>
                                        {{ $pedido->pago_texto }}
                                    </span>

                                </div>

                                {{-- ACCIONES --}}
                                <div class="dash-action-group">

                                    <a href="{{ $pedido->url }}" class="dash-icon-action" title="Ver detalle"
                                        aria-label="Ver detalle del pedido">

                                        <i class="bx bx-show-alt"></i>

                                    </a>

                                    <a href="{{ $pedido->url }}" class="dash-icon-action is-main"
                                        title="Gestionar pedido" aria-label="Gestionar pedido">

                                        <i class="bx bx-right-arrow-alt"></i>

                                    </a>

                                </div>

                            </div>

                        @empty

                            <div class="text-center py-5 text-muted">

                                <i class="bx bx-receipt fs-1 d-block mb-2"></i>

                                No hay pedidos recientes registrados.

                            </div>
                        @endforelse

                    </div>

                </div>

            </div>

        </div>
        <br>
        {{-- VENTAS RECIENTES --}}
        <div class="col-12">

            <div class="dash-card">

                <div class="dash-card-header">

                    <div>

                        <h5 class="dash-title">
                            Ventas recientes
                        </h5>

                        <div class="dash-subtitle">
                            Últimos movimientos comerciales.
                        </div>

                    </div>

                    <span class="dash-pill">
                        Online + local
                    </span>

                </div>

                <div class="card-body p-4 pt-2">

                    <div class="dash-list">

                        @forelse ($ventasRecientes as $venta)
                            <div class="dash-list-item dash-sales-item">

                                {{-- REFERENCIA --}}
                                <div>

                                    <span class="dash-list-label">
                                        Referencia
                                    </span>

                                    <div class="dash-list-main">

                                        <div class="dash-list-icon">
                                            <i class="bx {{ $venta['icono'] }}"></i>
                                        </div>

                                        <div>

                                            <div class="dash-list-title">
                                                {{ $venta['referencia'] }}
                                            </div>

                                            <div class="dash-list-sub">
                                                {{ $venta['descripcion'] }}
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                {{-- CANAL --}}
                                <div>

                                    <span class="dash-list-label">
                                        Canal
                                    </span>

                                    <span class="dash-channel dash-channel-{{ $venta['canal_key'] }}">

                                        <i
                                            class="bx {{ $venta['canal_key'] === 'online' ? 'bx-globe' : 'bx-store-alt' }}"></i>

                                        {{ $venta['canal'] }}

                                    </span>

                                </div>

                                {{-- TOTAL --}}
                                <div>

                                    <span class="dash-list-label">
                                        Total
                                    </span>

                                    <div class="dash-money">
                                        ₡{{ number_format($venta['total'], 2) }}
                                    </div>

                                </div>

                                {{-- FECHA --}}
                                <div>

                                    <span class="dash-list-label">
                                        Fecha
                                    </span>

                                    <div class="dash-date-cell">
                                        {{ \Carbon\Carbon::createFromTimestamp($venta['fecha'])->format('d/m/Y') }}
                                    </div>

                                </div>

                                {{-- ESTADO --}}
                                <div>

                                    <span class="dash-list-label">
                                        Estado
                                    </span>

                                    <span class="dash-status-badge dash-badge-{{ $venta['badge'] }}">

                                        <i class="bx bx-check-circle"></i>

                                        {{ $venta['estado'] }}

                                    </span>

                                </div>

                            </div>

                        @empty

                            <div class="text-center py-5 text-muted">

                                <i class="bx bx-store fs-1 d-block mb-2"></i>

                                No hay ventas recientes registradas.

                            </div>
                        @endforelse

                    </div>

                </div>

            </div>

        </div>
    </div>
    </div>

@endsection

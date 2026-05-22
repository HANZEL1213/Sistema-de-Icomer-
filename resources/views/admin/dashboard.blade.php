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
                            <i class="bx bx-check-circle text-success me-1"></i>
                            Tienda activa
                        </span>

                        <strong>Online</strong>
                    </div>

                    <div class="dash-health-item">
                        <span>
                            <i class="bx bx-search-alt text-warning me-1"></i>
                            Pagos por revisar
                        </span>

                        <strong>7</strong>
                    </div>

                    <div class="dash-health-item">
                        <span>
                            <i class="bx bx-calendar text-info me-1"></i>
                            Fecha operativa
                        </span>

                        <strong>21/05/2026</strong>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

        {{-- KPIS --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3 mb-4">

            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#2563eb,#1e40af);">
                    <div class="dash-kpi-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon"><i class="bx bx-cart"></i></div>
                            <span class="dash-trend">+8%</span>
                        </div>
                        <div class="dash-kpi-value">₡185,000</div>
                        <div class="dash-kpi-label">Ventas de hoy</div>
                        <div class="dash-kpi-footer">
                            <span>12 ventas registradas</span>
                            <strong>72%</strong>
                        </div>
                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 72%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#dca117,#b45309);">
                    <div class="dash-kpi-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon"><i class="bx bx-wallet"></i></div>
                            <span class="dash-trend">64%</span>
                        </div>
                        <div class="dash-kpi-value">₡2.45M</div>
                        <div class="dash-kpi-label">Ingresos del mes</div>
                        <div class="dash-kpi-footer">
                            <span>Meta mensual ₡3.8M</span>
                            <strong>64%</strong>
                        </div>
                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 64%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#16a34a,#166534);">
                    <div class="dash-kpi-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon"><i class="bx bx-time-five"></i></div>
                            <span class="dash-trend">Hoy</span>
                        </div>
                        <div class="dash-kpi-value">18</div>
                        <div class="dash-kpi-label">Pedidos pendientes</div>
                        <div class="dash-kpi-footer">
                            <span>Requieren seguimiento</span>
                            <strong>45%</strong>
                        </div>
                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 45%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#dc2626,#991b1b);">
                    <div class="dash-kpi-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon"><i class="bx bx-search-alt"></i></div>
                            <span class="dash-trend">Urgente</span>
                        </div>
                        <div class="dash-kpi-value">7</div>
                        <div class="dash-kpi-label">Pagos en revisión</div>
                        <div class="dash-kpi-footer">
                            <span>Validar comprobantes</span>
                            <strong>38%</strong>
                        </div>
                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 38%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#475569,#0f172a);">
                    <div class="dash-kpi-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon"><i class="bx bx-package"></i></div>
                            <span class="dash-trend">Revisar</span>
                        </div>
                        <div class="dash-kpi-value">9</div>
                        <div class="dash-kpi-label">Productos bajo stock</div>
                        <div class="dash-kpi-footer">
                            <span>Inventario crítico</span>
                            <strong>52%</strong>
                        </div>
                        <div class="progress mt-2 bg-light-transparent" style="height: 5px;">
                            <div class="progress-bar bg-white" style="width: 52%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="dash-kpi-card" style="background: linear-gradient(135deg,#7c3aed,#4c1d95);">
                    <div class="dash-kpi-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="dash-kpi-icon"><i class="bx bx-send"></i></div>
                            <span class="dash-trend">Activos</span>
                        </div>
                        <div class="dash-kpi-value">11</div>
                        <div class="dash-kpi-label">Pedidos por entregar</div>
                        <div class="dash-kpi-footer">
                            <span>Preparando / enviados</span>
                            <strong>58%</strong>
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
                    <div class="dash-subtitle">Atajos principales para operar la tienda sin perder tiempo.</div>
                </div>
                <span class="dash-pill"><i class="bx bx-shield-quarter"></i> Modo administrador</span>
            </div>

            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6 col-md-4 col-xl-2">
                        <a href="#" class="dash-action">
                            <div class="dash-action-icon"><i class="bx bx-search-alt"></i></div>
                            <strong>Revisar pagos</strong>
                            <small class="text-muted d-block">Comprobantes</small>
                        </a>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <a href="#" class="dash-action">
                            <div class="dash-action-icon"><i class="bx bx-plus-circle"></i></div>
                            <strong>Crear producto</strong>
                            <small class="text-muted d-block">Catálogo</small>
                        </a>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <a href="#" class="dash-action">
                            <div class="dash-action-icon"><i class="bx bx-store"></i></div>
                            <strong>Venta física</strong>
                            <small class="text-muted d-block">Caja local</small>
                        </a>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <a href="#" class="dash-action">
                            <div class="dash-action-icon"><i class="bx bx-package"></i></div>
                            <strong>Inventario</strong>
                            <small class="text-muted d-block">Movimientos</small>
                        </a>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <a href="#" class="dash-action">
                            <div class="dash-action-icon"><i class="bx bx-cog"></i></div>
                            <strong>Configuración</strong>
                            <small class="text-muted d-block">Tienda</small>
                        </a>
                    </div>

                    <div class="col-6 col-md-4 col-xl-2">
                        <a href="#" class="dash-action">
                            <div class="dash-action-icon"><i class="bx bx-error-circle"></i></div>
                            <strong>Pendientes</strong>
                            <small class="text-muted d-block">Alertas</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>




















        {{-- GRÁFICOS + PRIORIDAD --}}
        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="dash-card h-100">
                    <div class="dash-card-header">
                        <div>
                            <h5 class="dash-title">Ventas últimos 7 días</h5>
                            <div class="dash-subtitle">Lectura rápida del rendimiento semanal.</div>
                        </div>
                        <span class="dash-pill">₡485,000 total</span>
                    </div>

                    <div class="card-body p-4 pt-2">
                        <div class="dash-chart">
                            @foreach ([80, 130, 105, 175, 125, 215, 165] as $index => $value)
                                <div class="dash-chart-col">
                                    <div class="dash-chart-bar" style="height: {{ $value }}px;"></div>
                                    <span
                                        class="dash-chart-label">{{ ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'][$index] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="dash-card h-100">
                    <div class="dash-card-header">
                        <div>
                            <h5 class="dash-title">Prioridad operativa</h5>
                            <div class="dash-subtitle">Qué deberías revisar primero.</div>
                        </div>
                    </div>

                    <div class="card-body p-4 pt-2">
                        <div class="dash-priority mb-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="dash-alert-icon text-danger">
                                    <i class="bx bx-search-alt"></i>
                                </div>
                                <div>
                                    <strong>Validar pagos en revisión</strong>
                                    <small class="text-muted d-block">Hay 7 comprobantes esperando aprobación o
                                        rechazo.</small>
                                </div>
                            </div>
                        </div>

                        <div class="dash-status-row">
                            <div class="dash-status-item">
                                <div class="dash-status-name">
                                    <span><span class="dash-dot bg-warning"></span>En revisión</span>
                                    <strong>35%</strong>
                                </div>
                                <div class="progress" style="height: 9px;">
                                    <div class="progress-bar bg-warning" style="width: 35%"></div>
                                </div>
                            </div>

                            <div class="dash-status-item">
                                <div class="dash-status-name">
                                    <span><span class="dash-dot bg-info"></span>Preparando</span>
                                    <strong>25%</strong>
                                </div>
                                <div class="progress" style="height: 9px;">
                                    <div class="progress-bar bg-info" style="width: 25%"></div>
                                </div>
                            </div>

                            <div class="dash-status-item">
                                <div class="dash-status-name">
                                    <span><span class="dash-dot bg-primary"></span>Enviado</span>
                                    <strong>20%</strong>
                                </div>
                                <div class="progress" style="height: 9px;">
                                    <div class="progress-bar bg-primary" style="width: 20%"></div>
                                </div>
                            </div>

                            <div class="dash-status-item mb-0">
                                <div class="dash-status-name">
                                    <span><span class="dash-dot bg-success"></span>Entregado</span>
                                    <strong>20%</strong>
                                </div>
                                <div class="progress" style="height: 9px;">
                                    <div class="progress-bar bg-success" style="width: 20%"></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Total pedidos activos</small>
                            <strong>51</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- INVENTARIO + VENTAS --}}
        <div class="row g-4">

            <div class="col-xl-5">
                <div class="dash-card h-100 overflow-hidden">

                    <div class="dash-card-header">
                        <div>
                            <h5 class="dash-title">Resumen de inventario</h5>
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

                            <div class="dash-mini dash-mini-hover">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Productos activos</small>
                                        <h4>128</h4>
                                    </div>

                                    <div class="dash-mini-icon bg-primary-subtle text-primary">
                                        <i class="bx bx-package"></i>
                                    </div>
                                </div>

                                <div class="progress mt-3" style="height: 7px;">
                                    <div class="progress-bar bg-primary" style="width: 88%"></div>
                                </div>
                            </div>

                            <div class="dash-mini dash-mini-hover">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Sin stock</small>
                                        <h4 class="text-danger">3</h4>
                                    </div>

                                    <div class="dash-mini-icon bg-danger-subtle text-danger">
                                        <i class="bx bx-x-circle"></i>
                                    </div>
                                </div>

                                <div class="progress mt-3" style="height: 7px;">
                                    <div class="progress-bar bg-danger" style="width: 18%"></div>
                                </div>
                            </div>

                            <div class="dash-mini dash-mini-hover">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Bajo stock</small>
                                        <h4 class="text-warning">9</h4>
                                    </div>

                                    <div class="dash-mini-icon bg-warning-subtle text-warning">
                                        <i class="bx bx-error"></i>
                                    </div>
                                </div>

                                <div class="progress mt-3" style="height: 7px;">
                                    <div class="progress-bar bg-warning" style="width: 42%"></div>
                                </div>
                            </div>

                            <div class="dash-mini dash-mini-hover">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <small class="text-muted">Movimientos hoy</small>
                                        <h4 class="text-success">21</h4>
                                    </div>

                                    <div class="dash-mini-icon bg-success-subtle text-success">
                                        <i class="bx bx-transfer-alt"></i>
                                    </div>
                                </div>

                                <div class="progress mt-3" style="height: 7px;">
                                    <div class="progress-bar bg-success" style="width: 74%"></div>
                                </div>
                            </div>

                        </div>

                        {{-- ACTIVIDAD --}}
                        <div class="dash-stock-activity">

                            <div class="dash-stock-item">
                                <div class="dash-stock-line danger"></div>

                                <div class="dash-stock-icon bg-danger-subtle text-danger">
                                    <i class="bx bx-minus"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="dash-stock-title">
                                        Salida por pedido PED-202605210001
                                    </div>

                                    <div class="dash-stock-sub">
                                        Sandalias Urban X · Hace 8 minutos
                                    </div>
                                </div>

                                <strong class="text-danger">-2</strong>
                            </div>

                            <div class="dash-stock-item">
                                <div class="dash-stock-line success"></div>

                                <div class="dash-stock-icon bg-success-subtle text-success">
                                    <i class="bx bx-plus"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="dash-stock-title">
                                        Entrada por ajuste inventario
                                    </div>

                                    <div class="dash-stock-sub">
                                        Reabastecimiento manual · Hace 20 minutos
                                    </div>
                                </div>

                                <strong class="text-success">+10</strong>
                            </div>

                            <div class="dash-stock-item">
                                <div class="dash-stock-line warning"></div>

                                <div class="dash-stock-icon bg-warning-subtle text-warning">
                                    <i class="bx bx-store"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="dash-stock-title">
                                        Salida por venta física
                                    </div>

                                    <div class="dash-stock-sub">
                                        Caja principal · Hace 36 minutos
                                    </div>
                                </div>

                                <strong class="text-danger">-1</strong>
                            </div>

                        </div>

                    </div>
                </div>
            </div>





            <div class="col-xl-7">
                <div class="dash-card h-100 overflow-hidden">

                    <div class="dash-card-header">
                        <div>
                            <h5 class="dash-title">Actividad en tiempo real</h5>
                            <div class="dash-subtitle">
                                Eventos recientes y rendimiento operativo de la tienda.
                            </div>
                        </div>

                        <span class="dash-pill">
                            <i class="bx bx-pulse"></i>
                            En vivo
                        </span>
                    </div>

                    <div class="card-body p-4 pt-2">

                        {{-- MINI STATS --}}
                        <div class="row g-3 mb-4">

                            <div class="col-md-4">
                                <div class="dash-live-metric dash-live-success h-100">
                                    <div class="dash-live-top">
                                        <div class="dash-live-icon">
                                            <i class="bx bx-user-check"></i>
                                        </div>

                                        <span class="dash-live-trend">
                                            <i class="bx bx-up-arrow-alt"></i>
                                            +12%
                                        </span>
                                    </div>

                                    <small>Usuarios activos</small>

                                    <div class="dash-live-value">184</div>

                                    <div class="dash-live-footer">
                                        <span>68% actividad</span>
                                        <strong>En vivo</strong>
                                    </div>

                                    <div class="dash-live-bar">
                                        <span style="width: 68%;"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="dash-live-metric dash-live-primary h-100">
                                    <div class="dash-live-top">
                                        <div class="dash-live-icon">
                                            <i class="bx bx-line-chart"></i>
                                        </div>

                                        <span class="dash-live-trend">
                                            <i class="bx bx-up-arrow-alt"></i>
                                            +0.6%
                                        </span>
                                    </div>

                                    <small>Conversión tienda</small>

                                    <div class="dash-live-value">4.8%</div>

                                    <div class="dash-live-footer">
                                        <span>48% objetivo</span>
                                        <strong>Estable</strong>
                                    </div>

                                    <div class="dash-live-bar">
                                        <span style="width: 48%;"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="dash-live-metric dash-live-warning h-100">
                                    <div class="dash-live-top">
                                        <div class="dash-live-icon">
                                            <i class="bx bx-shopping-bag"></i>
                                        </div>

                                        <span class="dash-live-trend">
                                            <i class="bx bx-up-arrow-alt"></i>
                                            +8%
                                        </span>
                                    </div>

                                    <small>Pedidos hoy</small>

                                    <div class="dash-live-value">27</div>

                                    <div class="dash-live-footer">
                                        <span>74% meta diaria</span>
                                        <strong>Alto</strong>
                                    </div>

                                    <div class="dash-live-bar">
                                        <span style="width: 74%;"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- ACTIVIDAD --}}
                    <div class="d-grid gap-3">

                        <div class="dash-alert">
                            <div class="dash-alert-icon text-success">
                                <i class="bx bx-check-circle"></i>
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between gap-2">
                                    <strong>Pago verificado correctamente</strong>
                                    <small class="text-muted">Hace 2 min</small>
                                </div>

                                <small class="text-muted d-block">
                                    Pedido PED-202605210001 aprobado y movido a preparación.
                                </small>
                            </div>
                        </div>

                        <div class="dash-alert">
                            <div class="dash-alert-icon text-primary">
                                <i class="bx bx-cart"></i>
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between gap-2">
                                    <strong>Nuevo pedido recibido</strong>
                                    <small class="text-muted">Hace 7 min</small>
                                </div>

                                <small class="text-muted d-block">
                                    Cliente realizó una compra por ₡64,000.00.
                                </small>
                            </div>
                        </div>

                        <div class="dash-alert">
                            <div class="dash-alert-icon text-warning">
                                <i class="bx bx-package"></i>
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between gap-2">
                                    <strong>Inventario bajo detectado</strong>
                                    <small class="text-muted">Hace 16 min</small>
                                </div>

                                <small class="text-muted d-block">
                                    “Sandalias Urban X” tiene menos de 3 unidades disponibles.
                                </small>
                            </div>
                        </div>

                        <div class="dash-alert">
                            <div class="dash-alert-icon text-danger">
                                <i class="bx bx-x-circle"></i>
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between gap-2">
                                    <strong>Pago rechazado</strong>
                                    <small class="text-muted">Hace 25 min</small>
                                </div>

                                <small class="text-muted d-block">
                                    SINPE no coincide con el monto reportado por el cliente.
                                </small>
                            </div>
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
                        <h5 class="dash-title">Pedidos recientes</h5>
                        <div class="dash-subtitle">Últimos pedidos generados en la tienda.</div>
                    </div>
                    <span class="dash-pill">Últimos 4</span>
                </div>

                <div class="card-body p-4 pt-2">
                    <div class="dash-list">

                        @foreach ([['PED-202605210001', 'Hace 12 min · Pedido online', 'Hanzel Sanabria', 'Cliente invitado', '₡32,000.00', 'En revisión', 'warning', 'Enviado', 'info'], ['PED-202605210002', 'Hace 28 min · Pedido online', 'Cliente invitado', 'Compra sin cuenta', '₡18,500.00', 'Pagado', 'success', 'Verificado', 'success'], ['PED-202605210003', 'Hace 1 hora · Pedido online', 'María López', 'Usuario registrado', '₡64,000.00', 'Enviado', 'primary', 'Verificado', 'success'], ['PED-202605210004', 'Hace 2 horas · Pedido online', 'Carlos Mora', 'Usuario registrado', '₡12,000.00', 'Rechazado', 'danger', 'Rechazado', 'danger']] as $pedido)
                            <div class="dash-list-item">
                                <div>
                                    <span class="dash-list-label">Pedido</span>
                                    <div class="dash-list-main">
                                        <div class="dash-list-icon"><i class="bx bx-receipt"></i></div>
                                        <div>
                                            <div class="dash-list-title">{{ $pedido[0] }}</div>
                                            <div class="dash-list-sub">{{ $pedido[1] }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <span class="dash-list-label">Cliente</span>
                                    <div class="dash-client-name">{{ $pedido[2] }}</div>
                                    <div class="dash-client-type">{{ $pedido[3] }}</div>
                                </div>

                                <div>
                                    <span class="dash-list-label">Total</span>
                                    <div class="dash-money">{{ $pedido[4] }}</div>
                                </div>

                                <div>
                                    <span class="dash-list-label">Estado</span>
                                    <span class="dash-status-badge dash-badge-{{ $pedido[6] }}">
                                        <i class="bx bx-check-circle"></i> {{ $pedido[5] }}
                                    </span>
                                </div>

                                <div>
                                    <span class="dash-list-label">Pago</span>
                                    <span class="dash-status-badge dash-badge-{{ $pedido[8] }}">
                                        <i class="bx bx-check-shield"></i> {{ $pedido[7] }}
                                    </span>
                                </div>

                                <div class="dash-action-group">
                                    <a href="#" class="dash-icon-action"><i class="bx bx-show"></i></a>
                                    <a href="#" class="dash-icon-action is-main"><i class="bx bx-cog"></i></a>
                                </div>
                            </div>
                        @endforeach

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
                        <h5 class="dash-title">Ventas recientes</h5>
                        <div class="dash-subtitle">Últimos movimientos comerciales.</div>
                    </div>
                    <span class="dash-pill">Online + local</span>
                </div>

                <div class="card-body p-4 pt-2">
                    <div class="dash-list">

                        @foreach ([['PED-202605210001', 'Venta online · Hace 12 min', 'online', 'Online', '₡32,000.00', '2026-05-21', 'Pagada', 'success', 'bx-shopping-bag'], ['TCK-000145', 'Venta local · Caja principal', 'local', 'Local', '₡15,000.00', '2026-05-21', 'Completada', 'success', 'bx-store'], ['PED-202605210002', 'Venta online · Hace 28 min', 'online', 'Online', '₡18,500.00', '2026-05-21', 'Revisión', 'warning', 'bx-shopping-bag'], ['TCK-000146', 'Venta local · Caja principal', 'local', 'Local', '₡8,000.00', '2026-05-21', 'Completada', 'success', 'bx-store']] as $venta)
                            <div class="dash-list-item dash-sales-item">
                                <div>
                                    <span class="dash-list-label">Referencia</span>
                                    <div class="dash-list-main">
                                        <div class="dash-list-icon"><i class="bx {{ $venta[8] }}"></i></div>
                                        <div>
                                            <div class="dash-list-title">{{ $venta[0] }}</div>
                                            <div class="dash-list-sub">{{ $venta[1] }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <span class="dash-list-label">Canal</span>
                                    <span class="dash-channel dash-channel-{{ $venta[2] }}">
                                        <i class="bx {{ $venta[2] === 'online' ? 'bx-globe' : 'bx-store-alt' }}"></i>
                                        {{ $venta[3] }}
                                    </span>
                                </div>

                                <div>
                                    <span class="dash-list-label">Total</span>
                                    <div class="dash-money">{{ $venta[4] }}</div>
                                </div>

                                <div>
                                    <span class="dash-list-label">Fecha</span>
                                    <div class="dash-date-cell">{{ $venta[5] }}</div>
                                </div>

                                <div>
                                    <span class="dash-list-label">Estado</span>
                                    <span class="dash-status-badge dash-badge-{{ $venta[7] }}">
                                        <i class="bx bx-check-circle"></i> {{ $venta[6] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>







    </div>
    </div>

@endsection

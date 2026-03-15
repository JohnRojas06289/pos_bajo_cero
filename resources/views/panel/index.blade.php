@extends('layouts.app')

@section('title','Dashboard — Jacket Store')

@push('css')
<style>
/* ── Theme-aware overrides ───────────────────────────────────────── */
.db-kpi-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
    border: 1px solid var(--border-color, #e2e8f0);
    border-left: 4px solid;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    height: 100%;
}
.db-kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
.db-kpi-label {
    font-size: 0.6rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--text-secondary, #64748b); margin-bottom: 0.3rem;
}
.db-kpi-value {
    font-size: 1.6rem; font-weight: 800; line-height: 1;
    color: var(--text-primary, #111); letter-spacing: -0.02em;
    margin-bottom: 0.2rem; font-family: 'JetBrains Mono', monospace;
}
.db-kpi-sub { font-size: 0.72rem; color: var(--text-secondary, #64748b); font-weight: 600; }
.db-kpi-icon {
    width: 46px; height: 46px; border-radius: 10px; display: flex;
    align-items: center; justify-content: center; font-size: 1.15rem;
    color: #fff; flex-shrink: 0;
}

/* Accent palette */
.kpi-accent  { border-left-color: #E67E22; }
.kpi-petrol  { border-left-color: #2C3E50; }
.kpi-green   { border-left-color: #27AE60; }
.kpi-blue    { border-left-color: #3498DB; }
.kpi-nequi   { border-left-color: #8E44AD; }
.kpi-davi    { border-left-color: #C0392B; }
.kpi-cash    { border-left-color: #27AE60; }
.kpi-digital { border-left-color: #2980B9; }

.icon-accent  { background: linear-gradient(135deg,#E67E22,#D35400); }
.icon-petrol  { background: linear-gradient(135deg,#2C3E50,#34495E); }
.icon-green   { background: linear-gradient(135deg,#27AE60,#2ECC71); }
.icon-blue    { background: linear-gradient(135deg,#2980B9,#3498DB); }
.icon-nequi   { background: linear-gradient(135deg,#6C3483,#8E44AD); }
.icon-davi    { background: linear-gradient(135deg,#C0392B,#E74C3C); }
.icon-cash    { background: linear-gradient(135deg,#1E8449,#27AE60); }
.icon-digital { background: linear-gradient(135deg,#1A5276,#2980B9); }

/* Section titles */
.db-section-title {
    font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.12em; color: var(--text-secondary, #64748b);
    margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;
}
.db-section-title::before {
    content: ''; display: inline-block; width: 3px; height: 14px;
    background: #E67E22; border-radius: 2px;
}

/* Chart cards */
.chart-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 12px; overflow: hidden; height: 100%;
}
.chart-card-header {
    padding: 0.875rem 1.1rem;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
    background: var(--bg-secondary, #f8fafc);
}
.chart-card-header h6 {
    font-weight: 700; color: var(--text-primary, #1e293b);
    margin: 0; font-size: 0.85rem;
}
.chart-card-body { padding: 1rem; }

/* Filter card */
.filter-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 12px; padding: 0.875rem 1.1rem; margin-bottom: 1.25rem;
}
.preset-btn {
    padding: 0.3rem 0.8rem; border-radius: 20px;
    border: 1.5px solid var(--border-color, #e2e8f0);
    background: var(--card-bg, #fff);
    color: var(--text-secondary, #64748b);
    font-weight: 600; font-size: 0.75rem; cursor: pointer;
    transition: all 0.18s ease; white-space: nowrap;
}
.preset-btn:hover, .preset-btn.active {
    border-color: #E67E22; color: #D35400; background: rgba(230,126,34,0.08);
}
.preset-btn.active { background: #E67E22; color: #fff; }

/* Tables */
.db-table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
.db-table th {
    padding: 0.55rem 0.75rem; font-size: 0.6rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-secondary, #64748b);
    border-bottom: 2px solid var(--border-color, #e2e8f0);
    background: var(--bg-secondary, #f8fafc);
}
.db-table td {
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
    color: var(--text-primary, #1e293b); vertical-align: middle;
}
.db-table tr:last-child td { border-bottom: none; }
.db-table tr:hover td { background: rgba(230,126,34,0.04); }

/* Stock badge */
.stock-badge {
    display: inline-block; padding: 0.2rem 0.55rem; border-radius: 6px;
    font-size: 0.7rem; font-weight: 700;
}
.stock-critical { background: rgba(231,76,60,0.15); color: #C0392B; }
.stock-low      { background: rgba(243,156,18,0.15); color: #E67E22; }
.stock-ok       { background: rgba(39,174,96,0.15);  color: #1E8449; }

/* Method badges */
.badge-efectivo     { background: rgba(39,174,96,0.15);  color: #1E8449; }
.badge-nequi        { background: rgba(142,68,173,0.15); color: #6C3483; }
.badge-daviplata    { background: rgba(192,57,43,0.15);  color: #A93226; }
.badge-transferencia{ background: rgba(41,128,185,0.15); color: #1A5276; }

/* KPI row divider */
.kpi-divider {
    height: 1px; background: var(--border-color, #e2e8f0); margin: 0.5rem 0 1rem;
}

/* Periodo KPIs */
.periodo-card {
    background: linear-gradient(135deg, #2C3E50 0%, #1A1A2E 100%);
    border-radius: 12px; padding: 1rem 1.25rem; color: #fff; margin-bottom: 1.25rem;
}
[data-theme="light"] .periodo-card {
    background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%);
}
.periodo-card .label { font-size: 0.65rem; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.1em; }
.periodo-card .value { font-size: 1.4rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; }

/* Btn ver detalle */
.btn-detail {
    padding: 0.25rem 0.6rem; border-radius: 6px; border: 1.5px solid var(--border-color, #e2e8f0);
    background: transparent; color: var(--text-secondary, #64748b); font-size: 0.72rem; cursor: pointer;
    transition: all 0.15s ease;
}
.btn-detail:hover { border-color: #E67E22; color: #E67E22; background: rgba(230,126,34,0.08); }

/* Modal */
.modal-content {
    background: var(--card-bg, #fff) !important;
    border: 1px solid var(--border-color, #e2e8f0) !important;
    color: var(--text-primary, #1e293b) !important;
}
.modal-header { border-bottom: 1px solid var(--border-color, #e2e8f0) !important; }
.modal-footer { border-top:  1px solid var(--border-color, #e2e8f0) !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 py-2">

    {{-- ════════════════════════════════════════════════════
         KPIs DEL DÍA
    ════════════════════════════════════════════════════ --}}
    <div class="db-section-title">Resumen del día — {{ \Carbon\Carbon::today()->format('d \d\e F, Y') }}</div>

    <div class="row g-3 mb-2">
        {{-- Ventas hoy --}}
        <div class="col-6 col-sm-3 col-xl">
            <div class="db-kpi-card kpi-accent">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Ventas hoy</div>
                        <div class="db-kpi-value">${{ number_format($ventasHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">COP</div>
                    </div>
                    <div class="db-kpi-icon icon-accent"><i class="fas fa-calendar-day"></i></div>
                </div>
            </div>
        </div>

        {{-- Transacciones hoy --}}
        <div class="col-6 col-sm-3 col-xl">
            <div class="db-kpi-card kpi-petrol">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Transacciones</div>
                        <div class="db-kpi-value">{{ number_format($transaccionesHoy, 0) }}</div>
                        <div class="db-kpi-sub">Ventas registradas</div>
                    </div>
                    <div class="db-kpi-icon icon-petrol"><i class="fas fa-receipt"></i></div>
                </div>
            </div>
        </div>

        {{-- Ticket promedio --}}
        <div class="col-6 col-sm-3 col-xl">
            <div class="db-kpi-card kpi-blue">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Ticket Promedio</div>
                        <div class="db-kpi-value">${{ number_format($ticketPromedioHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">Por venta</div>
                    </div>
                    <div class="db-kpi-icon icon-blue"><i class="fas fa-tags"></i></div>
                </div>
            </div>
        </div>

        {{-- Efectivo hoy --}}
        <div class="col-6 col-sm-3 col-xl">
            <div class="db-kpi-card kpi-cash">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Efectivo</div>
                        <div class="db-kpi-value">${{ number_format($efectivoHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">En caja hoy</div>
                    </div>
                    <div class="db-kpi-icon icon-cash"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
        </div>

        {{-- Nequi hoy --}}
        <div class="col-6 col-sm-3 col-xl">
            <div class="db-kpi-card kpi-nequi">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Nequi</div>
                        <div class="db-kpi-value">${{ number_format($nequiHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">Digital hoy</div>
                    </div>
                    <div class="db-kpi-icon icon-nequi"><i class="fas fa-mobile-alt"></i></div>
                </div>
            </div>
        </div>

        {{-- Daviplata hoy --}}
        <div class="col-6 col-sm-3 col-xl">
            <div class="db-kpi-card kpi-davi">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Daviplata</div>
                        <div class="db-kpi-value">${{ number_format($daviplataHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">Digital hoy</div>
                    </div>
                    <div class="db-kpi-icon icon-davi"><i class="fas fa-university"></i></div>
                </div>
            </div>
        </div>

        {{-- Transferencia hoy --}}
        <div class="col-6 col-sm-3 col-xl">
            <div class="db-kpi-card kpi-digital">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Transferencia</div>
                        <div class="db-kpi-value">${{ number_format($transferenciaHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">Digital hoy</div>
                    </div>
                    <div class="db-kpi-icon icon-digital"><i class="fas fa-exchange-alt"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════
         VENTAS POR CLIENTE HOY
    ════════════════════════════════════════════════════ --}}
    <div class="db-kpi-divider" style="height:1px;background:var(--border-color,#e2e8f0);margin:0.5rem 0 1rem;"></div>
    <div class="db-section-title">Ventas por cliente — hoy</div>

    <div class="chart-card mb-3">
        <div class="chart-card-body p-0">
            <div class="table-responsive">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Método</th>
                            <th>Total</th>
                            <th>Vendedor</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventasPorClienteHoy as $i => $v)
                        <tr>
                            <td class="text-secondary" style="font-size:0.7rem;">{{ $i+1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($v->created_at)->format('H:i') }}</td>
                            <td class="fw-600">{{ $v->cliente?->persona?->razon_social ?? 'Cliente General' }}</td>
                            <td>
                                <span class="stock-badge badge-{{ strtolower($v->metodo_pago) }}">
                                    {{ $v->metodo_pago }}
                                </span>
                            </td>
                            <td class="fw-700" style="font-family:'JetBrains Mono',monospace;">
                                ${{ number_format($v->total, 0, ',', '.') }}
                            </td>
                            <td>{{ $v->user?->name ?? 'N/A' }}</td>
                            <td>
                                <button class="btn-detail"
                                    onclick="abrirDetalle({{ $v->id }}, {{ json_encode([
                                        'numero'   => $v->numero_venta ?? ('V-'.($i+1)),
                                        'cliente'  => $v->cliente?->persona?->razon_social ?? 'Cliente General',
                                        'metodo'   => $v->metodo_pago,
                                        'total'    => $v->total,
                                        'fecha'    => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                                        'vendedor' => $v->user?->name ?? 'N/A',
                                        'productos'=> $v->productos->map(fn($p)=>[
                                            'nombre'   => $p->nombre,
                                            'cantidad' => $p->pivot->cantidad,
                                            'precio'   => $p->pivot->precio_venta,
                                            'subtotal' => $p->pivot->cantidad * $p->pivot->precio_venta,
                                        ])
                                    ]) }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary">
                                <i class="fas fa-store-slash fa-lg mb-2 d-block opacity-40"></i>
                                Sin ventas registradas hoy
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════
         FILTRO DE FECHAS (solo admin)
    ════════════════════════════════════════════════════ --}}
    @can('ver-estadisticas')
    <div class="db-kpi-divider" style="height:1px;background:var(--border-color,#e2e8f0);margin:0.5rem 0 1rem;"></div>
    <div class="db-section-title">Estadísticas del periodo</div>

    <div class="filter-card">
        <form action="{{ route('panel') }}" method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-sm-3 col-6">
                    <label class="form-label small fw-600 mb-1" style="color:var(--text-secondary);">Desde</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_inicio"
                           id="fecha_inicio" value="{{ $fechaInicio }}">
                </div>
                <div class="col-sm-3 col-6">
                    <label class="form-label small fw-600 mb-1" style="color:var(--text-secondary);">Hasta</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_fin"
                           id="fecha_fin" value="{{ $fechaFin }}">
                </div>
                <div class="col-sm-6">
                    <div class="d-flex gap-1 flex-wrap align-items-center">
                        <button type="button" class="preset-btn" onclick="setPreset('today')">Hoy</button>
                        <button type="button" class="preset-btn" onclick="setPreset('yesterday')">Ayer</button>
                        <button type="button" class="preset-btn" onclick="setPreset('week')">Semana</button>
                        <button type="button" class="preset-btn" onclick="setPreset('month')">Mes</button>
                        <button type="button" class="preset-btn" onclick="setPreset('30d')">30 días</button>
                        <button type="button" class="preset-btn" onclick="setPreset('year')">Año</button>
                        <button type="submit" class="btn btn-sm ms-auto" style="background:#E67E22;color:#fff;border-radius:8px;font-weight:600;">
                            <i class="fas fa-filter me-1"></i>Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- KPIs del periodo --}}
    <div class="periodo-card mb-3">
        <div class="row g-3 text-center">
            <div class="col-4">
                <div class="label">Ventas Periodo</div>
                <div class="value">${{ number_format($ventasPeriodo, 0, ',', '.') }}</div>
            </div>
            <div class="col-4">
                <div class="label">Transacciones</div>
                <div class="value">{{ number_format($transaccionesPeriodo, 0) }}</div>
            </div>
            <div class="col-4">
                <div class="label">Ticket Promedio</div>
                <div class="value">${{ number_format($ticketPromedioPeriodo, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="text-center mt-2" style="font-size:0.65rem;opacity:0.6;">
            {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </div>
    </div>

    {{-- Gráficas --}}
    <div class="row g-3 mb-3">
        {{-- Gráfica 1: Ventas por día --}}
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-chart-line me-2" style="color:#E67E22;"></i>Ventas por Día</h6>
                </div>
                <div class="chart-card-body">
                    <div style="height:280px;"><canvas id="ventasLineChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Gráfica 2: Distribución por método de pago --}}
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-chart-pie me-2" style="color:#E67E22;"></i>Métodos de Pago</h6>
                </div>
                <div class="chart-card-body d-flex flex-column align-items-center">
                    <div style="height:220px;width:100%;max-width:260px;"><canvas id="pagosDonutChart"></canvas></div>
                    <div id="donutLegend" class="mt-2" style="font-size:0.72rem;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        {{-- Gráfica 3: Top 5 más vendidos --}}
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-arrow-up me-2" style="color:#27AE60;"></i>Top 5 Más Vendidos</h6>
                </div>
                <div class="chart-card-body">
                    <div style="height:220px;"><canvas id="top5MasChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Gráfica 4: Top 5 menos vendidos --}}
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-arrow-down me-2" style="color:#E74C3C;"></i>Top 5 Menos Vendidos</h6>
                </div>
                <div class="chart-card-body">
                    <div style="height:220px;"><canvas id="top5MenosChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tablas --}}
    <div class="row g-3 mb-3">
        {{-- Últimas transacciones --}}
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-list me-2" style="color:#E67E22;"></i>Últimas Transacciones</h6>
                </div>
                <div class="chart-card-body p-0">
                    <div class="table-responsive">
                        <table class="db-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Método</th>
                                    <th>Total</th>
                                    <th>Vendedor</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ultimasVentas as $i => $v)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($v->created_at)->format('d/m H:i') }}</td>
                                    <td>{{ $v->cliente?->persona?->razon_social ?? 'Cliente General' }}</td>
                                    <td>
                                        <span class="stock-badge badge-{{ strtolower($v->metodo_pago) }}">
                                            {{ $v->metodo_pago }}
                                        </span>
                                    </td>
                                    <td style="font-family:'JetBrains Mono',monospace;font-weight:700;">
                                        ${{ number_format($v->total, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $v->user?->name ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn-detail"
                                            onclick="abrirDetalle({{ $v->id }}, {{ json_encode([
                                                'numero'   => $v->numero_venta ?? 'V-'.($i+1),
                                                'cliente'  => $v->cliente?->persona?->razon_social ?? 'Cliente General',
                                                'metodo'   => $v->metodo_pago,
                                                'total'    => $v->total,
                                                'fecha'    => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                                                'vendedor' => $v->user?->name ?? 'N/A',
                                                'productos'=> $v->productos->map(fn($p)=>[
                                                    'nombre'   => $p->nombre,
                                                    'cantidad' => $p->pivot->cantidad,
                                                    'precio'   => $p->pivot->precio_venta,
                                                    'subtotal' => $p->pivot->cantidad * $p->pivot->precio_venta,
                                                ])
                                            ]) }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stock bajo --}}
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header d-flex align-items-center justify-content-between">
                    <h6><i class="fas fa-exclamation-triangle me-2" style="color:#E74C3C;"></i>Stock Bajo (&lt;10)</h6>
                    <span class="badge" style="background:rgba(231,76,60,0.15);color:#C0392B;font-size:0.65rem;">
                        {{ $productosStockBajo->count() }} productos
                    </span>
                </div>
                <div class="chart-card-body p-0">
                    @if($productosStockBajo->isEmpty())
                        <div class="text-center py-4 text-secondary">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block" style="color:#27AE60;opacity:0.6;"></i>
                            <small>Todos los productos con stock suficiente</small>
                        </div>
                    @else
                    <table class="db-table">
                        <thead>
                            <tr><th>Producto</th><th class="text-end">Stock</th></tr>
                        </thead>
                        <tbody>
                            @foreach($productosStockBajo as $p)
                            <tr>
                                <td>{{ $p->nombre }}</td>
                                <td class="text-end">
                                    <span class="stock-badge {{ $p->cantidad == 0 ? 'stock-critical' : ($p->cantidad < 5 ? 'stock-low' : 'stock-ok') }}">
                                        {{ $p->cantidad }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endcan

    {{-- ════════════════════════════════════════════════════
         KPIs ACUMULADOS (totales históricos)
    ════════════════════════════════════════════════════ --}}
    <div class="db-kpi-divider" style="height:1px;background:var(--border-color,#e2e8f0);margin:0.5rem 0 1rem;"></div>
    <div class="db-section-title">Acumulados históricos</div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="db-kpi-card kpi-accent">
                <div class="db-kpi-label">Ventas (Semana)</div>
                <div class="db-kpi-value" style="font-size:1.3rem;">${{ number_format($ventasSemana, 0, ',', '.') }}</div>
                <div class="db-kpi-sub">Semana actual</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card kpi-green">
                <div class="db-kpi-label">Ventas (Mes)</div>
                <div class="db-kpi-value" style="font-size:1.3rem;">${{ number_format($ventasMes, 0, ',', '.') }}</div>
                <div class="db-kpi-sub">Mes actual</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card kpi-petrol">
                <div class="db-kpi-label">Ventas (Año)</div>
                <div class="db-kpi-value" style="font-size:1.3rem;">${{ number_format($ventasYear, 0, ',', '.') }}</div>
                <div class="db-kpi-sub">Año {{ date('Y') }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card kpi-blue">
                <div class="db-kpi-label">Prendas Vendidas</div>
                <div class="db-kpi-value" style="font-size:1.3rem;">{{ number_format($unidadesVendidas, 0) }}</div>
                <div class="db-kpi-sub">Unidades totales</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#E67E22;">
                <div class="db-kpi-label">Clientes</div>
                <div class="db-kpi-value" style="font-size:1.3rem;">{{ number_format($totalClientes) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#2C3E50;">
                <div class="db-kpi-label">Productos</div>
                <div class="db-kpi-value" style="font-size:1.3rem;">{{ number_format($totalProductos) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#27AE60;">
                <div class="db-kpi-label">Compras</div>
                <div class="db-kpi-value" style="font-size:1.3rem;">{{ number_format($totalCompras) }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#3498DB;">
                <div class="db-kpi-label">Usuarios</div>
                <div class="db-kpi-value" style="font-size:1.3rem;">{{ number_format($totalUsuarios) }}</div>
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════
     MODAL DETALLE DE VENTA
═══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="ventaDetalleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title fw-700" id="modalVentaTitle" style="color:var(--text-primary);">
                    <i class="fas fa-receipt me-2" style="color:#E67E22;"></i>Detalle de Venta
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row g-2 mb-3" id="modalMeta" style="font-size:0.78rem;"></div>
                <table class="db-table" id="modalProductosTable">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="modalProductosBody"></tbody>
                    <tfoot>
                        <tr style="background:var(--bg-secondary,#f8fafc);">
                            <td colspan="3" class="fw-700 text-end" style="padding:0.6rem 0.75rem;font-size:0.8rem;">TOTAL</td>
                            <td class="fw-800 text-end" id="modalTotal"
                                style="padding:0.6rem 0.75rem;font-family:'JetBrains Mono',monospace;color:#E67E22;font-size:0.9rem;"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer py-2 px-3">
                <button type="button" class="btn btn-sm" data-bs-dismiss="modal"
                        style="background:var(--border-color,#e2e8f0);color:var(--text-primary);border-radius:8px;">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script>
// ── Datos del servidor ────────────────────────────────────────────
const datosVentasDia  = @json($totalVentasPorDia);
const datosPagos      = @json($pagosPorMetodo);
const datosTop5Mas    = @json($top5MasVendidos);
const datosTop5Menos  = @json($top5MenosVendidos);

// ── Paleta Jacket Store ───────────────────────────────────────────
const JS_ACCENT  = '#E67E22';
const JS_PRIMARY = '#2C3E50';
const JS_GREEN   = '#27AE60';
const JS_BLUE    = '#3498DB';
const JS_RED     = '#E74C3C';
const JS_PURPLE  = '#8E44AD';
const JS_ORANGE2 = '#D35400';

const PAGO_COLORS = {
    EFECTIVO:      '#27AE60',
    NEQUI:         '#8E44AD',
    DAVIPLATA:     '#E74C3C',
    TRANSFERENCIA: '#3498DB',
};

// ── Detectar tema ─────────────────────────────────────────────────
function isDark() {
    return document.documentElement.getAttribute('data-theme') === 'dark';
}
function themeColor(light, dark) { return isDark() ? dark : light; }
function gridColor()  { return isDark() ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)'; }
function textColor()  { return isDark() ? '#B0B3C1' : '#64748B'; }
function tickColor()  { return isDark() ? '#B0B3C1' : '#64748B'; }

// ── Format COP ────────────────────────────────────────────────────
function cop(v) {
    return '$' + Number(v).toLocaleString('es-CO', {maximumFractionDigits:0});
}

// ── Chart.js: solo si el usuario ve las gráficas ──────────────────
(function initCharts() {
    const _vcEl = document.getElementById('ventasLineChart');
    if (!_vcEl) return; // sin permiso ver-estadisticas

Chart.defaults.global.defaultFontFamily = "Inter, system-ui, sans-serif";
Chart.defaults.global.defaultFontSize   = 11;

// ── Gráfica 1: Ventas por día (línea) ────────────────────────────
const fechas = datosVentasDia.map(d => {
    const s = String(d.fecha).split(' ')[0].split('-');
    return s.length === 3 ? `${s[2]}/${s[1]}` : d.fecha;
});
const montos    = datosVentasDia.map(d => parseFloat(d.total)   || 0);
const cantidades = datosVentasDia.map(d => parseInt(d.cantidad) || 0);

const ventasLineChart = new Chart(_vcEl, {
    type: 'line',
    data: {
        labels: fechas,
        datasets: [
            {
                label: 'Ventas COP',
                data: montos,
                lineTension: 0.3,
                backgroundColor: 'rgba(230,126,34,0.08)',
                borderColor: JS_ACCENT,
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: JS_ACCENT,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6,
                yAxisID: 'y-left',
            },
            {
                label: 'Transacciones',
                data: cantidades,
                lineTension: 0.3,
                backgroundColor: 'rgba(44,62,80,0.06)',
                borderColor: JS_PRIMARY,
                borderWidth: 2,
                borderDash: [4, 3],
                pointRadius: 3,
                pointBackgroundColor: JS_PRIMARY,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                yAxisID: 'y-right',
            }
        ],
    },
    options: {
        maintainAspectRatio: false,
        layout: { padding: { top: 10 } },
        scales: {
            xAxes: [{
                gridLines: { display: false },
                ticks: { fontColor: tickColor(), maxTicksLimit: 10 },
            }],
            yAxes: [
                {
                    id: 'y-left',
                    position: 'left',
                    ticks: {
                        fontColor: tickColor(), maxTicksLimit: 5, padding: 8,
                        callback: v => cop(v),
                    },
                    gridLines: { color: gridColor(), drawBorder: false, borderDash: [3] },
                },
                {
                    id: 'y-right',
                    position: 'right',
                    ticks: { fontColor: tickColor(), maxTicksLimit: 5, padding: 8 },
                    gridLines: { display: false },
                }
            ],
        },
        legend: {
            display: true,
            labels: { fontColor: textColor(), boxWidth: 12, padding: 16, usePointStyle: true }
        },
        tooltips: {
            mode: 'index', intersect: false,
            backgroundColor: themeColor('#fff','#1A1A2C'),
            titleFontColor: themeColor('#1e293b','#ecedf0'),
            bodyFontColor:  themeColor('#64748b','#B0B3C1'),
            borderColor: themeColor('#e2e8f0','#2A2A3E'), borderWidth: 1,
            callbacks: {
                label: function(item, data) {
                    const ds = data.datasets[item.datasetIndex];
                    if (ds.yAxisID === 'y-left') return ' ' + cop(item.yLabel);
                    return ' ' + item.yLabel + ' ventas';
                }
            }
        },
    }
});

// ── Gráfica 2: Donuts de métodos de pago ─────────────────────────
const pagoLabels = datosPagos.map(p => p.metodo_pago);
const pagoTotales = datosPagos.map(p => parseFloat(p.total) || 0);
const pagoCantidades = datosPagos.map(p => parseInt(p.cantidad) || 0);
const pagoColors = pagoLabels.map(l => PAGO_COLORS[l] || '#95A5A6');

const _pdEl = document.getElementById('pagosDonutChart');
const pagosDonutChart = _pdEl ? new Chart(_pdEl, {
    type: 'doughnut',
    data: {
        labels: pagoLabels,
        datasets: [{
            data: pagoTotales,
            backgroundColor: pagoColors.map(c => c + 'CC'),
            borderColor: pagoColors,
            borderWidth: 2,
            hoverBorderWidth: 3,
        }]
    },
    options: {
        maintainAspectRatio: false,
        cutoutPercentage: 72,
        legend: { display: false },
        tooltips: {
            backgroundColor: themeColor('#fff','#1A1A2C'),
            titleFontColor: themeColor('#1e293b','#ecedf0'),
            bodyFontColor:  themeColor('#64748b','#B0B3C1'),
            borderColor: themeColor('#e2e8f0','#2A2A3E'), borderWidth: 1,
            callbacks: {
                label: function(item, data) {
                    const lbl = data.labels[item.index];
                    const val = data.datasets[0].data[item.index];
                    return ` ${lbl}: ${cop(val)}`;
                }
            }
        }
    }
}) : null;

// Leyenda manual del donut
(function buildDonutLegend() {
    const el = document.getElementById('donutLegend');
    if (!el || !pagoLabels.length) return;
    el.innerHTML = pagoLabels.map((l, i) =>
        `<div style="display:inline-flex;align-items:center;gap:4px;margin:2px 6px;">
            <span style="width:10px;height:10px;border-radius:50%;background:${pagoColors[i]};display:inline-block;"></span>
            <span style="color:var(--text-secondary);">${l}: ${cop(pagoTotales[i])}</span>
        </div>`
    ).join('');
})();

// ── Gráfica 3 & 4: Top 5 productos ───────────────────────────────
function makeHBarChart(canvasId, labels, data, color) {
    const el = document.getElementById(canvasId);
    if (!el) return null;
    return new Chart(el, {
        type: 'horizontalBar',
        data: {
            labels,
            datasets: [{
                label: 'Unidades',
                data,
                backgroundColor: color + 'BB',
                borderColor: color,
                borderWidth: 1.5,
                hoverBackgroundColor: color,
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    ticks: { beginAtZero: true, fontColor: tickColor(), maxTicksLimit: 5 },
                    gridLines: { color: gridColor(), drawBorder: false, borderDash: [3] },
                }],
                yAxes: [{
                    ticks: { fontColor: tickColor() },
                    gridLines: { display: false },
                }],
            },
            legend: { display: false },
            tooltips: {
                backgroundColor: themeColor('#fff','#1A1A2C'),
                titleFontColor: themeColor('#1e293b','#ecedf0'),
                bodyFontColor:  themeColor('#64748b','#B0B3C1'),
                borderColor: themeColor('#e2e8f0','#2A2A3E'), borderWidth: 1,
                callbacks: { label: (i) => ` ${i.xLabel} unidades` }
            }
        }
    });
}

if (datosTop5Mas.length) {
    makeHBarChart(
        'top5MasChart',
        datosTop5Mas.map(p => p.nombre),
        datosTop5Mas.map(p => parseInt(p.total_vendido) || 0),
        JS_GREEN
    );
}
if (datosTop5Menos.length) {
    makeHBarChart(
        'top5MenosChart',
        datosTop5Menos.map(p => p.nombre),
        datosTop5Menos.map(p => parseInt(p.total_vendido) || 0),
        JS_RED
    );
}

})(); // end initCharts

// ── Presets de fecha ──────────────────────────────────────────────
function fmtDate(d) {
    return d.getFullYear() + '-'
        + String(d.getMonth()+1).padStart(2,'0') + '-'
        + String(d.getDate()).padStart(2,'0');
}
function setPreset(p) {
    const t = new Date();
    let s, e = t;
    switch(p) {
        case 'today':
            s = e = t; break;
        case 'yesterday':
            s = e = new Date(t.getFullYear(), t.getMonth(), t.getDate()-1); break;
        case 'week':
            s = new Date(t); s.setDate(t.getDate() - t.getDay() + 1); break;
        case 'month':
            s = new Date(t.getFullYear(), t.getMonth(), 1); break;
        case '30d':
            s = new Date(t); s.setDate(t.getDate()-29); break;
        case 'year':
            s = new Date(t.getFullYear(), 0, 1); break;
        default: s = t;
    }
    document.getElementById('fecha_inicio').value = fmtDate(s);
    document.getElementById('fecha_fin').value   = fmtDate(e);
    document.getElementById('filterForm').submit();
}

// ── Modal detalle de venta ────────────────────────────────────────
function abrirDetalle(id, data) {
    document.getElementById('modalVentaTitle').innerHTML =
        '<i class="fas fa-receipt me-2" style="color:#E67E22;"></i>' +
        (data.numero || 'Detalle de Venta');

    document.getElementById('modalMeta').innerHTML = `
        <div class="col-6"><span style="color:var(--text-secondary);font-size:0.7rem;">Cliente</span><div class="fw-600">${data.cliente}</div></div>
        <div class="col-6"><span style="color:var(--text-secondary);font-size:0.7rem;">Fecha</span><div class="fw-600">${data.fecha}</div></div>
        <div class="col-6"><span style="color:var(--text-secondary);font-size:0.7rem;">Método</span>
            <div><span class="stock-badge badge-${data.metodo.toLowerCase()}">${data.metodo}</span></div></div>
        <div class="col-6"><span style="color:var(--text-secondary);font-size:0.7rem;">Vendedor</span><div class="fw-600">${data.vendedor}</div></div>
    `;

    const tbody = document.getElementById('modalProductosBody');
    tbody.innerHTML = '';
    (data.productos || []).forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${p.nombre}</td>
            <td class="text-center">${p.cantidad}</td>
            <td class="text-end" style="font-family:'JetBrains Mono',monospace;">$${Number(p.precio).toLocaleString('es-CO',{maximumFractionDigits:0})}</td>
            <td class="text-end fw-700" style="font-family:'JetBrains Mono',monospace;">$${Number(p.subtotal).toLocaleString('es-CO',{maximumFractionDigits:0})}</td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('modalTotal').textContent =
        '$' + Number(data.total).toLocaleString('es-CO', {maximumFractionDigits:0});

    new bootstrap.Modal(document.getElementById('ventaDetalleModal')).show();
}
</script>
@endpush

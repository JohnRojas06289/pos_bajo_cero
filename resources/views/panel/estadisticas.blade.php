@extends('layouts.app')

@section('title','Estadísticas — Bajo Cero')

@push('css')
<style>
/* ── Base cards ─────────────────────────────────────────────────────── */
.db-kpi-card {
    background: var(--card-bg, #fff);
    border-radius: 12px; padding: 1rem 1.2rem;
    border: 1px solid var(--border-color, #e2e8f0);
    border-left: 4px solid; height: 100%;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.db-kpi-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.08); }
.db-kpi-label {
    font-size: 0.58rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.1em; color: var(--text-secondary, #64748b); margin-bottom: 0.25rem;
}
.db-kpi-value {
    font-size: 1.45rem; font-weight: 800; line-height: 1;
    color: var(--text-primary, #111); font-family: 'JetBrains Mono', monospace;
    letter-spacing: -0.02em; margin-bottom: 0.15rem;
}
.db-kpi-sub { font-size: 0.68rem; color: var(--text-secondary, #64748b); font-weight: 600; }
.db-kpi-icon {
    width: 42px; height: 42px; border-radius: 9px; display: flex;
    align-items: center; justify-content: center; font-size: 1.05rem;
    color: #fff; flex-shrink: 0;
}

.icon-accent  { background: linear-gradient(135deg,#E67E22,#D35400); }
.icon-petrol  { background: linear-gradient(135deg,#2C3E50,#34495E); }
.icon-blue    { background: linear-gradient(135deg,#2980B9,#3498DB); }
.icon-green   { background: linear-gradient(135deg,#1E8449,#27AE60); }
.icon-nequi   { background: linear-gradient(135deg,#6C3483,#8E44AD); }
.icon-davi    { background: linear-gradient(135deg,#C0392B,#E74C3C); }
.icon-digital { background: linear-gradient(135deg,#1A5276,#2980B9); }
.icon-cash    { background: linear-gradient(135deg,#1E8449,#27AE60); }
.icon-purple  { background: linear-gradient(135deg,#6C3483,#9B59B6); }

/* ── Section headers ────────────────────────────────────────────────── */
.db-section-title {
    font-size: 0.62rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.12em; color: var(--text-secondary, #64748b);
    margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;
}
.db-section-title::before {
    content: ''; display: inline-block; width: 3px; height: 14px;
    background: #E67E22; border-radius: 2px;
}

/* ── Filtro ─────────────────────────────────────────────────────────── */
.filter-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 12px; padding: 0.875rem 1.1rem; margin-bottom: 1.25rem;
}
.preset-btn {
    padding: 0.28rem 0.75rem; border-radius: 20px;
    border: 1.5px solid var(--border-color, #e2e8f0);
    background: var(--card-bg, #fff); color: var(--text-secondary, #64748b);
    font-weight: 600; font-size: 0.72rem; cursor: pointer;
    transition: all 0.15s ease; white-space: nowrap;
}
.preset-btn:hover, .preset-btn.active { border-color: #E67E22; color: #D35400; background: rgba(230,126,34,0.08); }
.preset-btn.active { background: #E67E22; color: #fff; }

/* ── Periodo banner ─────────────────────────────────────────────────── */
.periodo-banner {
    background: linear-gradient(135deg, #1B4F72 0%, #2C3E50 100%);
    border-radius: 12px; padding: 1rem 1.25rem; color: #fff; margin-bottom: 1.25rem;
}
.periodo-banner .label { font-size: 0.62rem; opacity: 0.65; text-transform: uppercase; letter-spacing: 0.1em; }
.periodo-banner .value { font-size: 1.35rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; }
.periodo-banner .sub   { font-size: 0.65rem; opacity: 0.55; margin-top: 0.15rem; }

/* ── Chart cards ────────────────────────────────────────────────────── */
.chart-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 12px; overflow: hidden; height: 100%;
}
.chart-card-header {
    padding: 0.8rem 1.1rem;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
    background: var(--bg-secondary, #f8fafc);
}
.chart-card-header h6 { font-weight: 700; color: var(--text-primary, #1e293b); margin: 0; font-size: 0.82rem; }
.chart-card-body { padding: 1rem; }

/* ── Tables ─────────────────────────────────────────────────────────── */
.db-table { width: 100%; border-collapse: collapse; font-size: 0.78rem; }
.db-table th {
    padding: 0.5rem 0.75rem; font-size: 0.58rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-secondary, #64748b);
    border-bottom: 2px solid var(--border-color, #e2e8f0);
    background: var(--bg-secondary, #f8fafc);
}
.db-table td {
    padding: 0.55rem 0.75rem; border-bottom: 1px solid var(--border-color, #e2e8f0);
    color: var(--text-primary, #1e293b); vertical-align: middle;
}
.db-table tr:last-child td { border-bottom: none; }
.db-table tr:hover td { background: rgba(230,126,34,0.04); }

/* ── Badges ─────────────────────────────────────────────────────────── */
.stock-badge { display: inline-block; padding: 0.18rem 0.5rem; border-radius: 5px; font-size: 0.68rem; font-weight: 700; }
.badge-efectivo      { background: rgba(39,174,96,0.15);  color: #1E8449; }
.badge-nequi         { background: rgba(142,68,173,0.15); color: #6C3483; }
.badge-daviplata     { background: rgba(192,57,43,0.15);  color: #A93226; }
.badge-transferencia { background: rgba(41,128,185,0.15); color: #1A5276; }
.badge-fiado         { background: rgba(230,126,34,0.15); color: #D35400; }
.stock-critical { background: rgba(231,76,60,0.15); color: #C0392B; }
.stock-low      { background: rgba(243,156,18,0.15); color: #E67E22; }
.stock-ok       { background: rgba(39,174,96,0.15);  color: #1E8449; }

.btn-detail {
    padding: 0.22rem 0.55rem; border-radius: 5px; border: 1.5px solid var(--border-color, #e2e8f0);
    background: transparent; color: var(--text-secondary, #64748b); font-size: 0.7rem; cursor: pointer;
    transition: all 0.15s ease;
}
.btn-detail:hover { border-color: #E67E22; color: #E67E22; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 py-2">

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="db-section-title mb-0">Estadísticas</div>
            <div style="font-size:0.75rem;color:var(--text-secondary);">Análisis histórico · filtros · KPIs del período</div>
        </div>
        <a href="{{ route('panel') }}" class="btn btn-sm" style="border:1.5px solid var(--border-color);color:var(--text-secondary);border-radius:8px;font-size:0.75rem;background:var(--card-bg);">
            <i class="fas fa-arrow-left me-1"></i>Volver al panel
        </a>
    </div>

    {{-- ── Filtro de fechas ─────────────────────────────────────────────── --}}
    <div class="filter-card">
        <form action="{{ route('estadisticas.index') }}" method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-sm-3 col-6">
                    <label class="form-label" style="font-size:0.68rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px;">Desde</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_inicio" id="fecha_inicio" value="{{ $fechaInicio }}">
                </div>
                <div class="col-sm-3 col-6">
                    <label class="form-label" style="font-size:0.68rem;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px;">Hasta</label>
                    <input type="date" class="form-control form-control-sm" name="fecha_fin" id="fecha_fin" value="{{ $fechaFin }}">
                </div>
                <div class="col-sm-6">
                    <div class="d-flex gap-1 flex-wrap align-items-center">
                        <button type="button" class="preset-btn {{ $fechaInicio == \Carbon\Carbon::today()->format('Y-m-d') && $fechaFin == \Carbon\Carbon::today()->format('Y-m-d') ? 'active' : '' }}" onclick="setPreset('today')">Hoy</button>
                        <button type="button" class="preset-btn" onclick="setPreset('yesterday')">Ayer</button>
                        <button type="button" class="preset-btn" onclick="setPreset('week')">Semana</button>
                        <button type="button" class="preset-btn" onclick="setPreset('month')">Mes</button>
                        <button type="button" class="preset-btn" onclick="setPreset('30d')">30 días</button>
                        <button type="button" class="preset-btn" onclick="setPreset('year')">Año</button>
                        <button type="submit" class="btn btn-sm ms-auto" style="background:#E67E22;color:#fff;border-radius:8px;font-weight:600;font-size:0.75rem;">
                            <i class="fas fa-filter me-1"></i>Filtrar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ── KPIs del período ─────────────────────────────────────────────── --}}
    <div class="periodo-banner">
        <div class="row g-3 text-center">
            <div class="col-4">
                <div class="label">Ventas del período</div>
                <div class="value">${{ number_format($ventasPeriodo, 0, ',', '.') }}</div>
                <div class="sub">COP</div>
            </div>
            <div class="col-4">
                <div class="label">Transacciones</div>
                <div class="value">{{ number_format($transaccionesPeriodo) }}</div>
                <div class="sub">ventas</div>
            </div>
            <div class="col-4">
                <div class="label">Ticket Promedio</div>
                <div class="value">${{ number_format($ticketPromedioPeriodo, 0, ',', '.') }}</div>
                <div class="sub">por venta</div>
            </div>
        </div>
        <div class="text-center mt-2" style="font-size:0.62rem;opacity:0.5;">
            {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
        </div>
    </div>

    {{-- ── Gráficas ─────────────────────────────────────────────────────── --}}
    <div class="db-section-title">Análisis del período</div>
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-chart-line me-2" style="color:#E67E22;"></i>Ventas por Día</h6>
                </div>
                <div class="chart-card-body">
                    <div style="height:260px;"><canvas id="ventasLineChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-chart-pie me-2" style="color:#E67E22;"></i>Métodos de Pago</h6>
                </div>
                <div class="chart-card-body d-flex flex-column align-items-center">
                    <div style="height:200px;width:100%;max-width:240px;"><canvas id="pagosDonutChart"></canvas></div>
                    <div id="donutLegend" class="mt-2" style="font-size:0.7rem;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-arrow-up me-2" style="color:#27AE60;"></i>Top 5 Más Vendidos</h6>
                </div>
                <div class="chart-card-body">
                    <div style="height:200px;"><canvas id="top5MasChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-arrow-down me-2" style="color:#E74C3C;"></i>Top 5 Menos Vendidos</h6>
                </div>
                <div class="chart-card-body">
                    <div style="height:200px;"><canvas id="top5MenosChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── KPIs acumulados ──────────────────────────────────────────────── --}}
    <div class="db-section-title">Acumulados históricos</div>
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#E67E22;">
                <div class="db-kpi-label">Ventas (Semana)</div>
                <div class="db-kpi-value">${{ number_format($ventasSemana, 0, ',', '.') }}</div>
                <div class="db-kpi-sub">Semana actual</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#27AE60;">
                <div class="db-kpi-label">Ventas (Mes)</div>
                <div class="db-kpi-value">${{ number_format($ventasMes, 0, ',', '.') }}</div>
                <div class="db-kpi-sub">{{ \Carbon\Carbon::now()->isoFormat('MMMM Y') }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#2C3E50;">
                <div class="db-kpi-label">Ventas (Año)</div>
                <div class="db-kpi-value">${{ number_format($ventasYear, 0, ',', '.') }}</div>
                <div class="db-kpi-sub">Año {{ date('Y') }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#3498DB;">
                <div class="db-kpi-label">Prendas Vendidas</div>
                <div class="db-kpi-value">{{ number_format($unidadesVendidas) }}</div>
                <div class="db-kpi-sub">Unidades totales</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#27AE60;">
                <div class="d-flex align-items-center gap-3">
                    <div class="db-kpi-icon icon-cash"><i class="fas fa-money-bill-wave"></i></div>
                    <div>
                        <div class="db-kpi-label">Efectivo total</div>
                        <div class="db-kpi-value" style="font-size:1.1rem;">${{ number_format($ventasEfectivo, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#8E44AD;">
                <div class="d-flex align-items-center gap-3">
                    <div class="db-kpi-icon icon-nequi"><i class="fas fa-mobile-alt"></i></div>
                    <div>
                        <div class="db-kpi-label">Digital total</div>
                        <div class="db-kpi-value" style="font-size:1.1rem;">${{ number_format($ventasTransferencia, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#E67E22;">
                <div class="d-flex align-items-center gap-3">
                    <div class="db-kpi-icon icon-accent"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="db-kpi-label">Clientes</div>
                        <div class="db-kpi-value" style="font-size:1.1rem;">{{ number_format($totalClientes) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="db-kpi-card" style="border-left-color:#2C3E50;">
                <div class="d-flex align-items-center gap-3">
                    <div class="db-kpi-icon icon-petrol"><i class="fas fa-vest"></i></div>
                    <div>
                        <div class="db-kpi-label">Productos</div>
                        <div class="db-kpi-value" style="font-size:1.1rem;">{{ number_format($totalProductos) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Últimas transacciones + stock bajo ──────────────────────────── --}}
    <div class="db-section-title">Detalle del período</div>
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h6><i class="fas fa-list me-2" style="color:#E67E22;"></i>Últimas transacciones del período</h6>
                </div>
                <div class="p-0">
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
                                @forelse($ultimasVentas as $i => $v)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($v->created_at)->format('d/m H:i') }}</td>
                                    <td>{{ $v->cliente?->persona?->razon_social ?? 'Cliente General' }}</td>
                                    <td><span class="stock-badge badge-{{ strtolower($v->metodo_pago) }}">{{ $v->metodo_pago }}</span></td>
                                    <td style="font-family:'JetBrains Mono',monospace;font-weight:700;">
                                        ${{ number_format($v->total, 0, ',', '.') }}
                                    </td>
                                    <td>{{ $v->user?->name ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn-detail" onclick="abrirDetalle({{ json_encode([
                                            'numero'    => $v->numero_venta ?? 'V-'.($i+1),
                                            'cliente'   => $v->cliente?->persona?->razon_social ?? 'Cliente General',
                                            'metodo'    => $v->metodo_pago,
                                            'total'     => $v->total,
                                            'fecha'     => \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i'),
                                            'vendedor'  => $v->user?->name ?? 'N/A',
                                            'productos' => $v->productos->map(fn($p) => [
                                                'nombre'   => $p->nombre,
                                                'cantidad' => $p->pivot->cantidad,
                                                'precio'   => $p->pivot->precio_venta,
                                                'subtotal' => $p->pivot->cantidad * $p->pivot->precio_venta,
                                            ]),
                                        ]) }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">
                                        <i class="fas fa-inbox fa-lg mb-2 d-block" style="opacity:0.3;"></i>
                                        Sin transacciones en este período
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header d-flex align-items-center justify-content-between">
                    <h6><i class="fas fa-exclamation-triangle me-2" style="color:#E74C3C;"></i>Stock Bajo (&lt;10)</h6>
                    <span class="badge" style="background:rgba(231,76,60,0.12);color:#C0392B;font-size:0.62rem;">
                        {{ $productosStockBajo->count() }} productos
                    </span>
                </div>
                <div class="p-0">
                    @if($productosStockBajo->isEmpty())
                        <div class="text-center py-4 text-secondary">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block" style="color:#27AE60;opacity:0.5;"></i>
                            <small>Stock suficiente en todos los productos</small>
                        </div>
                    @else
                    <table class="db-table">
                        <thead><tr><th>Producto</th><th class="text-end">Stock</th></tr></thead>
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

</div>

{{-- ── Modal detalle ───────────────────────────────────────────────────── --}}
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
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>Producto</th><th class="text-center">Cant.</th>
                            <th class="text-end">Precio Unit.</th><th class="text-end">Subtotal</th>
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
const datosVentasDia  = @json($totalVentasPorDia);
const datosPagos      = @json($pagosPorMetodo);
const datosTop5Mas    = @json($top5MasVendidos);
const datosTop5Menos  = @json($top5MenosVendidos);

const JS_ACCENT  = '#E67E22';
const JS_PRIMARY = '#2C3E50';
const JS_GREEN   = '#27AE60';
const JS_RED     = '#E74C3C';

const PAGO_COLORS = {
    EFECTIVO:      '#27AE60',
    NEQUI:         '#8E44AD',
    DAVIPLATA:     '#E74C3C',
    TRANSFERENCIA: '#3498DB',
    FIADO:         '#E67E22',
};

function isDark()  { return document.documentElement.getAttribute('data-theme') === 'dark'; }
function themeColor(l, d) { return isDark() ? d : l; }
function gridColor()  { return isDark() ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)'; }
function tickColor()  { return isDark() ? '#B0B3C1' : '#64748B'; }
function cop(v) { return '$' + Number(v).toLocaleString('es-CO', {maximumFractionDigits:0}); }

(function initCharts() {
    try {
        Chart.defaults.global.defaultFontFamily = "Inter, system-ui, sans-serif";
        Chart.defaults.global.defaultFontSize   = 11;

        // Ventas por día
        const _vc = document.getElementById('ventasLineChart');
        if (_vc) {
            const fechas    = datosVentasDia.map(d => { const s = String(d.fecha).split(' ')[0].split('-'); return s.length===3 ? `${s[2]}/${s[1]}` : d.fecha; });
            const montos    = datosVentasDia.map(d => parseFloat(d.total) || 0);
            const cantidades = datosVentasDia.map(d => parseInt(d.cantidad) || 0);
            new Chart(_vc, {
                type: 'line',
                data: {
                    labels: fechas,
                    datasets: [
                        { label: 'Ventas COP', data: montos, lineTension: 0.3, backgroundColor: 'rgba(230,126,34,0.08)', borderColor: JS_ACCENT, borderWidth: 2.5, pointRadius: 3, pointBackgroundColor: JS_ACCENT, pointBorderColor:'#fff', pointBorderWidth:2, yAxisID:'y-left' },
                        { label: 'Transacciones', data: cantidades, lineTension: 0.3, backgroundColor: 'rgba(44,62,80,0.06)', borderColor: JS_PRIMARY, borderWidth: 2, borderDash:[4,3], pointRadius:2, pointBackgroundColor:JS_PRIMARY, pointBorderColor:'#fff', pointBorderWidth:2, yAxisID:'y-right' }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{ gridLines:{display:false}, ticks:{fontColor:tickColor(),maxTicksLimit:10} }],
                        yAxes: [
                            { id:'y-left', position:'left', ticks:{fontColor:tickColor(),maxTicksLimit:5,padding:8,callback:v=>cop(v)}, gridLines:{color:gridColor(),drawBorder:false,borderDash:[3]} },
                            { id:'y-right', position:'right', ticks:{fontColor:tickColor(),maxTicksLimit:5,padding:8}, gridLines:{display:false} }
                        ]
                    },
                    legend: { display:true, labels:{fontColor:tickColor(),boxWidth:12,padding:14,usePointStyle:true} },
                    tooltips: { mode:'index', intersect:false, backgroundColor:themeColor('#fff','#1A1A2C'), titleFontColor:themeColor('#1e293b','#ecedf0'), bodyFontColor:themeColor('#64748b','#B0B3C1'), borderColor:themeColor('#e2e8f0','#2A2A3E'), borderWidth:1, callbacks:{ label:(item,data)=>{ const ds=data.datasets[item.datasetIndex]; return ds.yAxisID==='y-left' ? ' '+cop(item.yLabel) : ' '+item.yLabel+' ventas'; } } }
                }
            });
        }

        // Donut métodos de pago
        const _pd = document.getElementById('pagosDonutChart');
        if (_pd && datosPagos.length) {
            const labels   = datosPagos.map(p => p.metodo_pago);
            const totales  = datosPagos.map(p => parseFloat(p.total) || 0);
            const colors   = labels.map(l => PAGO_COLORS[l] || '#95A5A6');
            new Chart(_pd, {
                type: 'doughnut',
                data: { labels, datasets: [{ data:totales, backgroundColor:colors.map(c=>c+'CC'), borderColor:colors, borderWidth:2, hoverBorderWidth:3 }] },
                options: { maintainAspectRatio:false, cutoutPercentage:72, legend:{display:false}, tooltips:{ backgroundColor:themeColor('#fff','#1A1A2C'), titleFontColor:themeColor('#1e293b','#ecedf0'), bodyFontColor:themeColor('#64748b','#B0B3C1'), borderColor:themeColor('#e2e8f0','#2A2A3E'), borderWidth:1, callbacks:{label:(item,data)=>` ${data.labels[item.index]}: ${cop(data.datasets[0].data[item.index])}`} } }
            });
            const legendEl = document.getElementById('donutLegend');
            if (legendEl) legendEl.innerHTML = labels.map((l,i) =>
                `<div style="display:inline-flex;align-items:center;gap:4px;margin:2px 5px;">
                    <span style="width:9px;height:9px;border-radius:50%;background:${colors[i]};display:inline-block;"></span>
                    <span style="color:var(--text-secondary);">${l}: ${cop(totales[i])}</span>
                </div>`
            ).join('');
        }

        // Barras horizontales top 5
        function makeHBar(id, labels, data, color) {
            const el = document.getElementById(id);
            if (!el || !labels.length) return;
            new Chart(el, {
                type: 'horizontalBar',
                data: { labels, datasets: [{ label:'Unidades', data, backgroundColor:color+'BB', borderColor:color, borderWidth:1.5, hoverBackgroundColor:color }] },
                options: { maintainAspectRatio:false, scales:{ xAxes:[{ ticks:{beginAtZero:true,fontColor:tickColor(),maxTicksLimit:5}, gridLines:{color:gridColor(),drawBorder:false,borderDash:[3]} }], yAxes:[{ ticks:{fontColor:tickColor()}, gridLines:{display:false} }] }, legend:{display:false}, tooltips:{ backgroundColor:themeColor('#fff','#1A1A2C'), titleFontColor:themeColor('#1e293b','#ecedf0'), bodyFontColor:themeColor('#64748b','#B0B3C1'), borderColor:themeColor('#e2e8f0','#2A2A3E'), borderWidth:1, callbacks:{label:(i)=>` ${i.xLabel} unidades`} } }
            });
        }
        makeHBar('top5MasChart',   datosTop5Mas.map(p=>p.nombre),   datosTop5Mas.map(p=>parseInt(p.total_vendido)||0),   JS_GREEN);
        makeHBar('top5MenosChart', datosTop5Menos.map(p=>p.nombre), datosTop5Menos.map(p=>parseInt(p.total_vendido)||0), JS_RED);

    } catch(e) { console.warn('[Estadísticas] Chart error:', e.message); }
})();

// Presets de fecha
function fmtDate(d) { return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0'); }
function setPreset(p) {
    const t = new Date(); let s, e = t;
    switch(p) {
        case 'today':     s = e = t; break;
        case 'yesterday': s = e = new Date(t.getFullYear(),t.getMonth(),t.getDate()-1); break;
        case 'week':      s = new Date(t); s.setDate(t.getDate()-t.getDay()+1); break;
        case 'month':     s = new Date(t.getFullYear(),t.getMonth(),1); break;
        case '30d':       s = new Date(t); s.setDate(t.getDate()-29); break;
        case 'year':      s = new Date(t.getFullYear(),0,1); break;
        default: s = t;
    }
    document.getElementById('fecha_inicio').value = fmtDate(s);
    document.getElementById('fecha_fin').value    = fmtDate(e);
    document.getElementById('filterForm').submit();
}

// Modal detalle
function abrirDetalle(data) {
    document.getElementById('modalVentaTitle').innerHTML = '<i class="fas fa-receipt me-2" style="color:#E67E22;"></i>' + (data.numero || 'Detalle');
    document.getElementById('modalMeta').innerHTML = `
        <div class="col-6"><span style="color:var(--text-secondary);font-size:0.7rem;">Cliente</span><div class="fw-600">${data.cliente}</div></div>
        <div class="col-6"><span style="color:var(--text-secondary);font-size:0.7rem;">Fecha</span><div class="fw-600">${data.fecha}</div></div>
        <div class="col-6"><span style="color:var(--text-secondary);font-size:0.7rem;">Método</span><div><span class="stock-badge badge-${data.metodo.toLowerCase()}">${data.metodo}</span></div></div>
        <div class="col-6"><span style="color:var(--text-secondary);font-size:0.7rem;">Vendedor</span><div class="fw-600">${data.vendedor}</div></div>
    `;
    const tbody = document.getElementById('modalProductosBody');
    tbody.innerHTML = '';
    (data.productos||[]).forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${p.nombre}</td><td class="text-center">${p.cantidad}</td><td class="text-end" style="font-family:'JetBrains Mono',monospace;">$${Number(p.precio).toLocaleString('es-CO',{maximumFractionDigits:0})}</td><td class="text-end fw-700" style="font-family:'JetBrains Mono',monospace;">$${Number(p.subtotal).toLocaleString('es-CO',{maximumFractionDigits:0})}</td>`;
        tbody.appendChild(tr);
    });
    document.getElementById('modalTotal').textContent = '$'+Number(data.total).toLocaleString('es-CO',{maximumFractionDigits:0});
    new bootstrap.Modal(document.getElementById('ventaDetalleModal')).show();
}
</script>
@endpush

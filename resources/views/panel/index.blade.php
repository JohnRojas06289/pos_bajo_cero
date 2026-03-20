@extends('layouts.app')

@section('title','Panel — Bajo Cero')

@push('css')
<style>
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

.icon-accent  { background: linear-gradient(135deg,#E67E22,#D35400); }
.icon-petrol  { background: linear-gradient(135deg,#2C3E50,#34495E); }
.icon-blue    { background: linear-gradient(135deg,#2980B9,#3498DB); }
.icon-nequi   { background: linear-gradient(135deg,#6C3483,#8E44AD); }
.icon-davi    { background: linear-gradient(135deg,#C0392B,#E74C3C); }
.icon-cash    { background: linear-gradient(135deg,#1E8449,#27AE60); }
.icon-digital { background: linear-gradient(135deg,#1A5276,#2980B9); }

.db-section-title {
    font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.12em; color: var(--text-secondary, #64748b);
    margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;
}
.db-section-title::before {
    content: ''; display: inline-block; width: 3px; height: 14px;
    background: #E67E22; border-radius: 2px;
}

.chart-card {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 12px; overflow: hidden;
}
.chart-card-header {
    padding: 0.875rem 1.1rem;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
    background: var(--bg-secondary, #f8fafc);
}
.chart-card-header h6 { font-weight: 700; color: var(--text-primary, #1e293b); margin: 0; font-size: 0.85rem; }

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

.stock-badge { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700; }
.badge-efectivo      { background: rgba(39,174,96,0.15);  color: #1E8449; }
.badge-nequi         { background: rgba(142,68,173,0.15); color: #6C3483; }
.badge-daviplata     { background: rgba(192,57,43,0.15);  color: #A93226; }
.badge-transferencia { background: rgba(41,128,185,0.15); color: #1A5276; }
.badge-fiado         { background: rgba(230,126,34,0.15); color: #D35400; }

.btn-detail {
    padding: 0.25rem 0.6rem; border-radius: 6px; border: 1.5px solid var(--border-color, #e2e8f0);
    background: transparent; color: var(--text-secondary, #64748b); font-size: 0.72rem; cursor: pointer;
    transition: all 0.15s ease;
}
.btn-detail:hover { border-color: #E67E22; color: #E67E22; background: rgba(230,126,34,0.08); }

/* Link a estadísticas */
.stats-cta {
    display: flex; align-items: center; justify-content: space-between;
    background: linear-gradient(135deg, #1B4F72 0%, #2C3E50 100%);
    border-radius: 12px; padding: 1rem 1.25rem; color: #fff; text-decoration: none;
    transition: opacity 0.18s ease;
}
.stats-cta:hover { opacity: 0.9; color: #fff; text-decoration: none; }
.stats-cta .label { font-size: 0.65rem; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.1em; }
.stats-cta .title { font-size: 1rem; font-weight: 700; }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 py-2">

    {{-- ── Header del día ─────────────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <div class="db-section-title mb-0">Resumen del día</div>
            <div style="font-size:0.78rem;color:var(--text-secondary);">{{ \Carbon\Carbon::today()->isoFormat('dddd, D [de] MMMM [de] Y') }}</div>
        </div>
        @can('ver-estadisticas')
        <a href="{{ route('estadisticas.index') }}" class="btn btn-sm" style="background:#E67E22;color:#fff;border-radius:8px;font-weight:600;font-size:0.78rem;">
            <i class="fas fa-chart-bar me-1"></i>Ver estadísticas
        </a>
        @endcan
    </div>

    {{-- ── KPIs del día ────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-sm-4 col-xl">
            <div class="db-kpi-card" style="border-left-color:#E67E22;">
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
        <div class="col-6 col-sm-4 col-xl">
            <div class="db-kpi-card" style="border-left-color:#2C3E50;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Transacciones</div>
                        <div class="db-kpi-value">{{ number_format($transaccionesHoy) }}</div>
                        <div class="db-kpi-sub">Ventas registradas</div>
                    </div>
                    <div class="db-kpi-icon icon-petrol"><i class="fas fa-receipt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-xl">
            <div class="db-kpi-card" style="border-left-color:#3498DB;">
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
        <div class="col-6 col-sm-4 col-xl">
            <div class="db-kpi-card" style="border-left-color:#27AE60;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Efectivo</div>
                        <div class="db-kpi-value">${{ number_format($efectivoHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">En caja</div>
                    </div>
                    <div class="db-kpi-icon icon-cash"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-xl">
            <div class="db-kpi-card" style="border-left-color:#8E44AD;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Nequi</div>
                        <div class="db-kpi-value">${{ number_format($nequiHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">Digital</div>
                    </div>
                    <div class="db-kpi-icon icon-nequi"><i class="fas fa-mobile-alt"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-xl">
            <div class="db-kpi-card" style="border-left-color:#C0392B;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Daviplata</div>
                        <div class="db-kpi-value">${{ number_format($daviplataHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">Digital</div>
                    </div>
                    <div class="db-kpi-icon icon-davi"><i class="fas fa-university"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-4 col-xl">
            <div class="db-kpi-card" style="border-left-color:#2980B9;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="db-kpi-label">Transferencia</div>
                        <div class="db-kpi-value">${{ number_format($transferenciaHoy, 0, ',', '.') }}</div>
                        <div class="db-kpi-sub">Digital</div>
                    </div>
                    <div class="db-kpi-icon icon-digital"><i class="fas fa-exchange-alt"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Ventas del día ──────────────────────────────────────────────── --}}
    <div class="db-section-title">Ventas registradas hoy</div>
    <div class="chart-card mb-3">
        <div class="p-0">
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
                            <td class="text-secondary" style="font-size:0.7rem;">{{ $i + 1 }}</td>
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
                                <button class="btn-detail" onclick="abrirDetalle({{ json_encode([
                                    'numero'    => $v->numero_venta ?? ('V-'.($i+1)),
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
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="fas fa-store-slash fa-2x mb-2 d-block" style="opacity:0.3;"></i>
                                Sin ventas registradas hoy
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── CTA estadísticas ────────────────────────────────────────────── --}}
    @can('ver-estadisticas')
    <a href="{{ route('estadisticas.index') }}" class="stats-cta mb-4 d-flex">
        <div>
            <div class="label">Módulo de análisis</div>
            <div class="title"><i class="fas fa-chart-bar me-2" style="color:#E67E22;"></i>Ver estadísticas históricas, gráficas y KPIs del período</div>
        </div>
        <i class="fas fa-arrow-right fa-lg" style="opacity:0.6;align-self:center;"></i>
    </a>
    @endcan

</div>

{{-- ── Modal detalle de venta ──────────────────────────────────────────── --}}
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
<script>
function abrirDetalle(data) {
    document.getElementById('modalVentaTitle').innerHTML =
        '<i class="fas fa-receipt me-2" style="color:#E67E22;"></i>' + (data.numero || 'Detalle');

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

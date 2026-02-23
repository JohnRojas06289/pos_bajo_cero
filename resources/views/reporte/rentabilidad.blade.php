@extends('layouts.app')

@section('title', 'Reporte de Rentabilidad')

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.template>
        <x-breadcrumb.item active>Reporte de Rentabilidad</x-breadcrumb.item>
    </x-breadcrumb.template>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fa-solid fa-chart-line me-2 text-success"></i>Rentabilidad por Producto</h2>
    </div>

    <!-- Filtro de fechas -->
    <form method="GET" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Desde</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Hasta</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i>Filtrar</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Tarjetas de resumen -->
    <div class="row g-3 mb-4">
        @foreach($resumenOrigen as $origen => $data)
        <div class="col-md-6">
            <div class="card shadow-sm border-0 {{ $origen === 'Importado' ? 'border-start border-primary border-4' : 'border-start border-success border-4' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-1">{{ $origen }}</h6>
                            <h4 class="fw-bold mb-0">${{ number_format($data['ingresos'], 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ number_format($data['unidades'], 0) }} unidades vendidas</small>
                        </div>
                        <div class="text-end">
                            <div class="text-success fw-bold fs-5">${{ number_format($data['margen'], 0, ',', '.') }}</div>
                            <small class="text-muted">Margen bruto</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="col-md-4">
            <div class="card shadow-sm text-center border-0 bg-light">
                <div class="card-body">
                    <div class="text-muted small">Productos sin ventas</div>
                    <div class="fs-3 fw-bold text-danger">{{ $sinVentas }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 más rentables -->
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold text-success"><i class="fa-solid fa-trophy me-2"></i>Top 5 Más Rentables</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>#</th><th>Producto</th><th>Origen</th><th>Ingresos</th><th>Margen</th><th>%</th></tr></thead>
                <tbody>
                    @foreach($topRentables as $i => $p)
                    <tr>
                        <td><span class="badge bg-warning text-dark">{{ $i+1 }}</span></td>
                        <td class="fw-semibold">{{ $p->nombre }}</td>
                        <td><span class="badge {{ $p->origen === 'Importado' ? 'bg-primary' : 'bg-success' }}">{{ $p->origen }}</span></td>
                        <td>${{ number_format($p->ingreso_total, 0, ',', '.') }}</td>
                        <td class="text-success fw-bold">${{ number_format($p->margen_bruto, 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:8px">
                                    <div class="progress-bar bg-success" style="width:{{ min($p->margen_pct, 100) }}%"></div>
                                </div>
                                <small>{{ $p->margen_pct }}%</small>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabla completa -->
    <div class="card shadow-sm">
        <div class="card-header fw-semibold"><i class="fa-solid fa-table me-2"></i>Todos los Productos</div>
        <div class="card-body p-0">
            <table class="table table-hover table-sm mb-0 align-middle" id="tablaProductos">
                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Origen</th>
                        <th>Precio Base</th>
                        <th>Unid. Vendidas</th>
                        <th>Ingresos</th>
                        <th>Costo Total</th>
                        <th>Margen Bruto</th>
                        <th>Margen %</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $p)
                    @php $costoTotal = $p->unidades_vendidas * $p->ultimo_costo; @endphp
                    <tr class="{{ $p->margen_pct < 10 && $p->unidades_vendidas > 0 ? 'table-danger' : '' }}">
                        <td class="fw-semibold">{{ $p->nombre }}</td>
                        <td><span class="badge {{ $p->origen === 'Importado' ? 'bg-primary' : 'bg-success' }}">{{ $p->origen }}</span></td>
                        <td>${{ number_format($p->precio, 0, ',', '.') }}</td>
                        <td>{{ number_format($p->unidades_vendidas, 0) }}</td>
                        <td>${{ number_format($p->ingreso_total, 0, ',', '.') }}</td>
                        <td>${{ number_format($costoTotal, 0, ',', '.') }}</td>
                        <td class="fw-bold {{ $p->margen_bruto > 0 ? 'text-success' : 'text-danger' }}">
                            ${{ number_format($p->margen_bruto, 0, ',', '.') }}
                        </td>
                        <td>
                            <span class="badge {{ $p->margen_pct >= 30 ? 'bg-success' : ($p->margen_pct >= 10 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $p->margen_pct }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

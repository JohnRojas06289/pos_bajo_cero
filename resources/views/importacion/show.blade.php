@extends('layouts.app')

@section('title', 'Importación ' . $importacion->numero)

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.template>
        <x-breadcrumb.item href="{{ route('importaciones.index') }}">Importaciones</x-breadcrumb.item>
        <x-breadcrumb.item active>{{ $importacion->numero }}</x-breadcrumb.item>
    </x-breadcrumb.template>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold"><i class="fa-solid fa-info-circle me-2"></i>Detalle de Importación</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><th>Número</th><td><code>{{ $importacion->numero }}</code></td></tr>
                        <tr><th>Proveedor</th><td>{{ $importacion->proveedor?->persona?->nombre ?? '—' }}</td></tr>
                        <tr><th>País</th><td>{{ $importacion->pais_origen }}</td></tr>
                        <tr><th>Llegada</th><td>{{ $importacion->fecha_llegada?->format('d/m/Y') }}</td></tr>
                        <tr><th>Moneda</th><td>{{ $importacion->moneda_costo }}</td></tr>
                        <tr><th>TRM</th><td>{{ number_format($importacion->tasa_cambio, 0, ',', '.') }}</td></tr>
                        <tr><th>Flete</th><td>${{ number_format($importacion->flete, 0, ',', '.') }}</td></tr>
                        <tr><th>Arancel</th><td>${{ number_format($importacion->arancel, 0, ',', '.') }}</td></tr>
                        <tr><th>Seguro</th><td>${{ number_format($importacion->seguro, 0, ',', '.') }}</td></tr>
                        <tr><th>Otros</th><td>${{ number_format($importacion->otros_gastos, 0, ',', '.') }}</td></tr>
                        <tr class="table-primary"><th>Total Gastos</th><td class="fw-bold">${{ number_format($importacion->total_gastos, 0, ',', '.') }}</td></tr>
                    </table>
                    @if($importacion->notas)
                    <p class="mt-3 text-muted small">{{ $importacion->notas }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold"><i class="fa-solid fa-box me-2"></i>Productos Importados</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Costo ({{ $importacion->moneda_costo }})</th>
                                <th>Costo COP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($importacion->productos as $p)
                            <tr>
                                <td>{{ $p->nombre }}</td>
                                <td>{{ $p->pivot->cantidad }}</td>
                                <td>{{ number_format($p->pivot->costo_unitario_moneda, 2) }}</td>
                                <td class="fw-bold">${{ number_format($p->pivot->costo_unitario_cop, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

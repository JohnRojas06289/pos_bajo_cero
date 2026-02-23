@extends('layouts.app')

@section('title', 'Importaciones')

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.template>
        <x-breadcrumb.item active>Importaciones</x-breadcrumb.item>
    </x-breadcrumb.template>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fa-solid fa-plane-arrival me-2 text-primary"></i>Importaciones</h2>
        @can('crear-importacion')
        <a href="{{ route('importaciones.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Nueva Importación
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Número</th>
                        <th>Proveedor</th>
                        <th>País Origen</th>
                        <th>Fecha Llegada</th>
                        <th>Moneda</th>
                        <th>TRM</th>
                        <th>Gastos Total</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($importaciones as $imp)
                    <tr>
                        <td><code>{{ $imp->numero }}</code></td>
                        <td>{{ $imp->proveedor?->persona?->nombre ?? '—' }}</td>
                        <td><i class="fa-solid fa-globe me-1 text-muted"></i>{{ $imp->pais_origen }}</td>
                        <td>{{ $imp->fecha_llegada?->format('d/m/Y') }}</td>
                        <td><span class="badge bg-info text-dark">{{ $imp->moneda_costo }}</span></td>
                        <td>{{ number_format($imp->tasa_cambio, 0, ',', '.') }}</td>
                        <td class="fw-bold">${{ number_format($imp->total_gastos, 0, ',', '.') }}</td>
                        <td>
                            @php $colores = ['Pendiente'=>'warning','En Tránsito'=>'info','Recibida'=>'success','Cancelada'=>'danger']; @endphp
                            <span class="badge bg-{{ $colores[$imp->estado] ?? 'secondary' }}">{{ $imp->estado }}</span>
                        </td>
                        <td>
                            <a href="{{ route('importaciones.show', $imp) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-plane fa-3x mb-2 d-block opacity-25"></i>
                            Sin importaciones registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($importaciones->hasPages())
        <div class="card-footer">{{ $importaciones->links() }}</div>
        @endif
    </div>
</div>
@endsection

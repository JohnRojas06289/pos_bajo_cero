@extends('layouts.app')

@section('title', 'Devoluciones')

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.template>
        <x-breadcrumb.item active>Devoluciones</x-breadcrumb.item>
    </x-breadcrumb.template>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fa-solid fa-rotate-left me-2 text-warning"></i>Devoluciones y Cambios</h2>
        @can('crear-devolucion')
        <a href="{{ route('devoluciones.create') }}" class="btn btn-warning text-dark">
            <i class="fa-solid fa-plus me-1"></i> Nueva Devolución
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
                        <th>Venta</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Motivo</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devoluciones as $dev)
                    <tr>
                        <td><code>{{ $dev->numero }}</code></td>
                        <td>{{ $dev->venta?->numero ?? '—' }}</td>
                        <td>{{ $dev->venta?->cliente?->persona?->nombre ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $dev->tipo === 'Cambio' ? 'bg-info text-dark' : 'bg-warning text-dark' }}">
                                <i class="fa-solid {{ $dev->tipo === 'Cambio' ? 'fa-arrows-rotate' : 'fa-rotate-left' }} me-1"></i>
                                {{ $dev->tipo }}
                            </span>
                        </td>
                        <td><small>{{ Str::limit($dev->motivo, 40) }}</small></td>
                        <td class="fw-bold">${{ number_format($dev->total, 0, ',', '.') }}</td>
                        <td>
                            @php $colores = ['Pendiente'=>'warning','Aprobada'=>'success','Rechazada'=>'danger']; @endphp
                            <span class="badge bg-{{ $colores[$dev->estado] }}">{{ $dev->estado }}</span>
                        </td>
                        <td><small>{{ $dev->created_at->format('d/m/Y') }}</small></td>
                        <td>
                            <a href="{{ route('devoluciones.show', $dev) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-rotate-left fa-3x mb-2 d-block opacity-25"></i>
                            Sin devoluciones registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($devoluciones->hasPages())
        <div class="card-footer">{{ $devoluciones->links() }}</div>
        @endif
    </div>
</div>
@endsection

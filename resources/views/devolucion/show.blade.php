@extends('layouts.app')

@section('title', 'Devolución ' . $devolucion->numero)

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.breadcrumb>
        <x-breadcrumb.item href="{{ route('devoluciones.index') }}">Devoluciones</x-breadcrumb.item>
        <x-breadcrumb.item active>{{ $devolucion->numero }}</x-breadcrumb.item>
    </x-breadcrumb.breadcrumb>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Devolución {{ $devolucion->numero }}</h2>
        @if($devolucion->estado === 'Pendiente')
        @can('editar-devolucion')
        <div class="d-flex gap-2">
            <form action="{{ route('devoluciones.aprobar', $devolucion) }}" method="POST" onsubmit="return confirm('¿Aprobar esta devolución? Esto actualizará el inventario.')">
                @csrf @method('PATCH')
                <button class="btn btn-success"><i class="fa-solid fa-check me-1"></i>Aprobar</button>
            </form>
            <form action="{{ route('devoluciones.rechazar', $devolucion) }}" method="POST" onsubmit="return confirm('¿Rechazar esta devolución?')">
                @csrf @method('PATCH')
                <button class="btn btn-danger"><i class="fa-solid fa-xmark me-1"></i>Rechazar</button>
            </form>
        </div>
        @endcan
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Información</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr><th>Número</th><td><code>{{ $devolucion->numero }}</code></td></tr>
                        <tr><th>Venta</th><td>{{ $devolucion->venta?->numero ?? '—' }}</td></tr>
                        <tr><th>Cliente</th><td>{{ $devolucion->venta?->cliente?->persona?->nombre ?? '—' }}</td></tr>
                        <tr><th>Tipo</th><td><span class="badge {{ $devolucion->tipo === 'Cambio' ? 'bg-info text-dark' : 'bg-warning text-dark' }}">{{ $devolucion->tipo }}</span></td></tr>
                        <tr><th>Estado</th><td>
                            @php $colores = ['Pendiente'=>'warning','Aprobada'=>'success','Rechazada'=>'danger']; @endphp
                            <span class="badge bg-{{ $colores[$devolucion->estado] }}">{{ $devolucion->estado }}</span>
                        </td></tr>
                        <tr><th>Total</th><td class="fw-bold">${{ number_format($devolucion->total, 0, ',', '.') }}</td></tr>
                        <tr><th>Registrado por</th><td>{{ $devolucion->user?->name ?? '—' }}</td></tr>
                        <tr><th>Fecha</th><td>{{ $devolucion->created_at->format('d/m/Y H:i') }}</td></tr>
                    </table>
                    @if($devolucion->motivo)
                    <div class="mt-3 p-2 bg-light rounded">
                        <strong class="small">Motivo:</strong><br>
                        <p class="mb-0 small">{{ $devolucion->motivo }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold">Productos Devueltos</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Venta</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devolucion->productos as $p)
                            <tr>
                                <td>{{ $p->nombre }}</td>
                                <td>{{ $p->pivot->cantidad }}</td>
                                <td>${{ number_format($p->pivot->precio_venta, 0, ',', '.') }}</td>
                                <td class="fw-bold">${{ number_format($p->pivot->cantidad * $p->pivot->precio_venta, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total:</td>
                                <td class="fw-bold text-success">${{ number_format($devolucion->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Variantes de ' . $producto->nombre)

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.template>
        <x-breadcrumb.item href="{{ route('productos.index') }}">Productos</x-breadcrumb.item>
        <x-breadcrumb.item active>Variantes: {{ $producto->nombre }}</x-breadcrumb.item>
    </x-breadcrumb.template>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Tallas / Colores</h2>
            <small class="text-muted">{{ $producto->nombre }} — Stock total: <strong>{{ $variantes->sum('stock') }}</strong></small>
        </div>
        @can('crear-producto')
        <a href="{{ route('productos.variantes.create', $producto) }}" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i> Nueva Variante
        </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Talla</th>
                        <th>Color</th>
                        <th>SKU</th>
                        <th>Stock</th>
                        <th>Stock Mínimo</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variantes as $variante)
                    <tr class="{{ $variante->stock <= $variante->stock_minimo ? 'table-warning' : '' }}">
                        <td><span class="badge bg-secondary fs-6">{{ $variante->talla ?? '—' }}</span></td>
                        <td>
                            @if($variante->color)
                                <span class="badge" style="background:#6c757d; padding:6px 12px;">{{ $variante->color }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><code>{{ $variante->sku ?? '—' }}</code></td>
                        <td>
                            <span class="fw-bold {{ $variante->stock <= $variante->stock_minimo ? 'text-danger' : 'text-success' }}">
                                {{ $variante->stock }}
                                @if($variante->stock <= $variante->stock_minimo)
                                    <i class="fa-solid fa-triangle-exclamation ms-1" title="Stock bajo"></i>
                                @endif
                            </span>
                        </td>
                        <td>{{ $variante->stock_minimo }}</td>
                        <td>{{ $variante->precio ? number_format($variante->precio, 0, ',', '.') : '<span class="text-muted small">Usa precio base</span>' }}</td>
                        <td>
                            @if($variante->estado)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @can('editar-producto')
                            <a href="{{ route('productos.variantes.edit', [$producto, $variante]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @endcan
                            @can('eliminar-producto')
                            <form action="{{ route('productos.variantes.destroy', [$producto, $variante]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar esta variante?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-ban"></i></button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-tags fa-2x mb-2 d-block opacity-50"></i>
                            Sin variantes registradas. <a href="{{ route('productos.variantes.create', $producto) }}">Agregar primera variante</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

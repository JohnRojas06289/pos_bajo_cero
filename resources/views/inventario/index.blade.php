@extends('layouts.app')

@section('title','Inventario')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" type="text/css">
@endpush

@section('content')

<div class="container-fluid px-2 py-3">

    <!-- Page Header -->
    <div class="section-header">
        <h1><i class="fas fa-warehouse"></i> Inventario</h1>
    </div>

    <!-- Breadcrumb -->
    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Inventario" />
    </x-breadcrumb.template>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-end g-3">
                <div class="col-md-4">
                    <form action="{{ route('inventario.index') }}" method="GET" class="d-flex gap-2">
                        <div class="flex-grow-1">
                            <label for="categoria_id" class="form-label">Filtrar por Categoría</label>
                            <select name="categoria_id" id="categoria_id" class="form-select">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->caracteristica->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="align-self-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 offset-md-4">
                    <label for="searchInput" class="form-label">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Buscar producto...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-boxes text-primary"></i>
            <span>Stock de productos</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover mb-0 fs-6">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Stock</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $item)
                        <tr>
                            <td>
                                <span class="badge" style="background:var(--hover-bg);color:var(--text-secondary);font-size:0.72rem;font-weight:600;border-radius:5px;">
                                    {{ $item->codigo }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size:0.85rem;">{{ $item->nombre }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    <i class="fas fa-ruler-combined me-1"></i>Talla: {{ $item->presentacione?->sigla ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @php $qty = $item->inventario->cantidad ?? 0; @endphp
                                @if($qty <= 0)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Agotado
                                    </span>
                                @elseif($qty <= 3)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $qty }} uds
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>{{ $qty }} uds
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions justify-content-end">
                                    @if($item->inventario)
                                        <a href="{{ route('inventario.edit', $item->inventario->id) }}"
                                           class="btn-icon-sm btn-edit" title="Editar inventario">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('inventario.destroy', $item->inventario->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar este registro de inventario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon-sm btn-delete" title="Eliminar inventario">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge" style="background:var(--hover-bg);color:var(--text-muted);font-size:0.72rem;">
                                            Sin inventario
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
<script src="{{ asset('js/simple-datatables.min.js') }}" type="text/javascript"></script>
<script>
    window.addEventListener('DOMContentLoaded', event => {
        const datatablesSimple = document.getElementById('datatablesSimple');
        if (datatablesSimple) {
            const dataTable = new simpleDatatables.DataTable(datatablesSimple, {
                paging: false,
                searchable: true,
                labels: {
                    placeholder: "Buscar...",
                    perPage: "Registros por página:",
                    noRows: "No se encontraron registros",
                    info: "Mostrando {start} a {end} de {rows} registros",
                    noResults: "No se encontraron resultados para tu búsqueda",
                }
            });

            setTimeout(() => {
                const searchWrapper = datatablesSimple.closest('.datatable-wrapper');
                if(searchWrapper) {
                    const defaultSearch = searchWrapper.querySelector('.datatable-search');
                    if(defaultSearch) defaultSearch.style.display = 'none';
                }
            }, 100);

            const searchInput = document.getElementById('searchInput');
            if(searchInput){
                searchInput.addEventListener('keyup', function(e) {
                    dataTable.search(e.target.value);
                });
            }
        }
    });
</script>
@endpush

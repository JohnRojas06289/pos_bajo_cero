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
        <div class="card-header d-flex align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-boxes text-primary"></i>
                <span>Stock de productos</span>
            </div>
            <button id="btnVistaExtendida" class="btn btn-primary" onclick="toggleVistaExtendida()">
                <i class="fas fa-expand-alt me-2"></i>Vista extendida
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover mb-0 fs-6">
                    <thead>
                        <tr>
                            <th class="col-img" style="display:none;">Imagen</th>
                            <th>Código</th>
                            <th>Producto</th>
                            <th class="col-ext" style="display:none;">Categoría / Marca</th>
                            <th class="col-ext" style="display:none;">Precios</th>
                            <th>Stock</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $item)
                        @php $qty = $item->variantes->sum('stock'); @endphp
                        <tr>
                            <td class="col-img align-middle text-center" style="display:none; width:90px;">
                                @if($item->image_url)
                                    <img src="{{ $item->image_url }}"
                                         alt="{{ $item->nombre }}"
                                         style="width:70px;height:70px;object-fit:contain;border-radius:6px;border:1px solid #dee2e6;background:#f8f9fa;padding:3px;">
                                @else
                                    <div style="width:70px;height:70px;display:flex;align-items:center;justify-content:center;border-radius:6px;border:1px solid #dee2e6;background:#f8f9fa;color:#adb5bd;margin:auto;">
                                        <i class="fas fa-image fa-lg"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="badge" style="background:var(--hover-bg);color:var(--text-secondary);font-size:0.72rem;font-weight:600;border-radius:5px;">
                                    {{ $item->codigo }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="fw-semibold text-dark" style="font-size:0.85rem;">{{ $item->nombre }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    <i class="fas fa-ruler-combined me-1"></i>Talla: {{ $item->presentacione?->sigla ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="col-ext align-middle" style="display:none; font-size:0.8rem;">
                                <div><i class="fas fa-tag me-1 text-muted"></i>{{ $item->categoria?->caracteristica?->nombre ?? 'N/A' }}</div>
                                <div class="text-muted"><i class="fas fa-copyright me-1"></i>{{ $item->marca?->nombre ?? 'N/A' }}</div>
                            </td>
                            <td class="col-ext align-middle" style="display:none; font-size:0.8rem;">
                                <div><i class="fas fa-dollar-sign me-1 text-muted"></i>Venta: <strong>$ {{ number_format($item->precio ?? 0, 0, ',', '.') }}</strong></div>
                                @if($item->precio_al_por_mayor)
                                <div class="text-muted"><i class="fas fa-dollar-sign me-1"></i>Mayor: $ {{ number_format($item->precio_al_por_mayor, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="align-middle">
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
                                    {{-- Detalle extendido --}}
                                    <button type="button"
                                        class="btn-icon-sm btn-view"
                                        title="Ver detalle"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detalleModal"
                                        data-nombre="{{ $item->nombre }}"
                                        data-codigo="{{ $item->codigo }}"
                                        data-categoria="{{ $item->categoria?->caracteristica?->nombre ?? 'N/A' }}"
                                        data-marca="{{ $item->marca?->nombre ?? 'N/A' }}"
                                        data-talla="{{ $item->presentacione?->sigla ?? 'N/A' }}"
                                        data-precio="{{ number_format($item->precio ?? 0, 0, ',', '.') }}"
                                        data-precio-mayor="{{ number_format($item->precio_al_por_mayor ?? 0, 0, ',', '.') }}"
                                        data-stock="{{ $qty }}"
                                        data-imagen="{{ $item->image_url }}">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if($item->inventario)
                                        {{-- Reinicializar (editar inventario) --}}
                                        <a href="{{ route('inventario.edit', $item->inventario->id) }}"
                                           class="btn-icon-sm"
                                           style="color: var(--color-primary);"
                                           title="Reinicializar inventario">
                                            <i class="fas fa-sync-alt"></i>
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

<!-- Modal Detalle Extendido -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detalleModalLabel">
                    <i class="fas fa-box-open me-2 text-primary"></i>
                    <span id="modal-nombre"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="modal-imagen"
                         src=""
                         alt="Imagen del producto"
                         class="rounded"
                         style="max-height:220px; max-width:100%; object-fit:contain; background:#f8f9fa; border:1px solid #dee2e6; padding:8px;">
                    <div id="modal-sin-imagen" class="text-muted py-4" style="display:none;">
                        <i class="fas fa-image fa-3x mb-2"></i>
                        <p class="mb-0">Sin imagen</p>
                    </div>
                </div>
                <table class="table table-sm table-borderless mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-semibold" style="width:40%"><i class="fas fa-barcode me-1"></i>Código</td>
                            <td id="modal-codigo"></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold"><i class="fas fa-tag me-1"></i>Categoría</td>
                            <td id="modal-categoria"></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold"><i class="fas fa-copyright me-1"></i>Marca</td>
                            <td id="modal-marca"></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold"><i class="fas fa-ruler-combined me-1"></i>Talla</td>
                            <td id="modal-talla"></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold"><i class="fas fa-cubes me-1"></i>Stock</td>
                            <td id="modal-stock"></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold"><i class="fas fa-dollar-sign me-1"></i>Precio venta</td>
                            <td id="modal-precio"></td>
                        </tr>
                        <tr id="modal-row-precio-mayor">
                            <td class="text-muted fw-semibold"><i class="fas fa-dollar-sign me-1"></i>Precio mayor</td>
                            <td id="modal-precio-mayor"></td>
                        </tr>
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

        // Vista extendida toggle
        window.toggleVistaExtendida = function() {
            const btn = document.getElementById('btnVistaExtendida');
            const cols = document.querySelectorAll('.col-img, .col-ext');
            const activa = btn.classList.toggle('btn-secondary');
            btn.classList.toggle('btn-primary', !activa);
            const mostrar = cols.length > 0 && cols[0].style.display === 'none';
            cols.forEach(el => el.style.display = mostrar ? '' : 'none');
            btn.innerHTML = mostrar
                ? '<i class="fas fa-compress-alt me-2"></i>Vista compacta'
                : '<i class="fas fa-expand-alt me-2"></i>Vista extendida';
        };

        // Poblar modal con datos del producto
        const detalleModal = document.getElementById('detalleModal');
        if (detalleModal) {
            detalleModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                document.getElementById('modal-nombre').textContent  = btn.dataset.nombre;
                document.getElementById('modal-codigo').textContent  = btn.dataset.codigo;
                document.getElementById('modal-categoria').textContent = btn.dataset.categoria;
                document.getElementById('modal-marca').textContent   = btn.dataset.marca;
                document.getElementById('modal-talla').textContent   = btn.dataset.talla;
                document.getElementById('modal-stock').textContent   = btn.dataset.stock + ' uds';
                document.getElementById('modal-precio').textContent  = '$ ' + btn.dataset.precio;

                const precioMayor = btn.dataset.precioMayor;
                const rowMayor = document.getElementById('modal-row-precio-mayor');
                if (precioMayor && precioMayor !== '0') {
                    document.getElementById('modal-precio-mayor').textContent = '$ ' + precioMayor;
                    rowMayor.style.display = '';
                } else {
                    rowMayor.style.display = 'none';
                }

                const imagen = btn.dataset.imagen;
                const imgEl = document.getElementById('modal-imagen');
                const sinImagen = document.getElementById('modal-sin-imagen');
                if (imagen) {
                    imgEl.src = imagen;
                    imgEl.style.display = '';
                    sinImagen.style.display = 'none';
                } else {
                    imgEl.src = '';
                    imgEl.style.display = 'none';
                    sinImagen.style.display = '';
                }
            });
        }
    });
</script>
@endpush

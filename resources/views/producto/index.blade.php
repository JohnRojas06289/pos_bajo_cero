@extends('layouts.app')

@section('title','Productos')

@push('css')
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
@endpush

@section('content')

<div class="container-fluid px-2">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-box-open me-2"></i> Productos</h1>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            @can('ver-producto')
            <a href="{{route('productos.export')}}" class="btn btn-sm btn-ghost" style="border-color:rgba(255,255,255,0.3);color:white;">
                <i class="fas fa-file-excel"></i>
                <span class="d-none d-sm-inline">Exportar</span>
            </a>
            @endcan
            @can('crear-producto')
            <button type="button" class="btn btn-sm btn-ghost" style="border-color:rgba(255,255,255,0.3);color:white;" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="fas fa-file-upload"></i>
                <span class="d-none d-sm-inline">Importar</span>
            </button>
            @endcan
            @can('crear-producto')
            <a href="{{route('productos.create')}}" class="btn btn-sm" style="background:white;color:#2563eb;font-weight:700;border:none;">
                <i class="fas fa-plus me-1"></i> Nuevo Producto
            </a>
            @endcan
        </div>
    </div>

    <!-- Filters and Controls -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <!-- Search Bar -->
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchProducts" class="form-control border-start-0" 
                               placeholder="Buscar producto..." onkeyup="filterProducts()">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="col-md-2">
                    <select id="filterCategory" class="form-select" onchange="filterProducts()">
                        <option value="">Todas las categorías</option>
                        @foreach($productos->unique('categoria_id')->sortBy('categoria.caracteristica.nombre') as $item)
                            @if($item->categoria)
                            <option value="{{ $item->categoria->caracteristica->nombre }}">
                                {{ $item->categoria->caracteristica->nombre }}
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Brand Filter -->
                <div class="col-md-2">
                    <select id="filterMarca" class="form-select" onchange="filterProducts()">
                        <option value="">Todas las marcas</option>
                        @foreach($productos->unique('marca_id')->sortBy('marca.caracteristica.nombre') as $item)
                            @if($item->marca)
                            <option value="{{ $item->marca->caracteristica->nombre }}">
                                {{ $item->marca->caracteristica->nombre }}
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2">
                    <select id="filterStatus" class="form-select" onchange="filterProducts()">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>

                <!-- View Toggle + Clear -->
                <div class="col-md-2 d-flex gap-2 align-items-center justify-content-end">
                    <button type="button" class="btn-ghost btn-sm" onclick="clearFilters()" title="Limpiar filtros" style="font-size:0.78rem;padding:0.3rem 0.65rem;">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </button>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active" id="gridViewBtn" onclick="setView('grid')" title="Grid">
                            <i class="fas fa-th"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="listViewBtn" onclick="setView('list')" title="Lista">
                            <i class="fas fa-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-warning" id="familyViewBtn" onclick="setView('familia')" title="Familias">
                            <i class="fas fa-layer-group"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Family View Container (hidden by default) -->
    <div id="familyContainer" class="row g-3" style="display:none;"></div>

    <!-- Products Grid -->
    <div id="productsGrid" class="products-grid">
        @forelse ($productos as $item)
        <div class="product-card"
             data-search="{{ strtolower($item->nombreCompleto) }}"
             data-category="{{ $item->categoria->caracteristica->nombre ?? '' }}"
             data-marca="{{ $item->marca->caracteristica->nombre ?? '' }}"
             data-status="{{ $item->estado }}"
             data-talla="{{ $item->presentacione->sigla ?? '' }}"
             data-talla-nombre="{{ $item->presentacione->caracteristica->nombre ?? '' }}"
             data-stock="{{ $item->inventario->cantidad ?? 0 }}"
             data-edit-href="{{ route('productos.edit', ['producto' => $item]) }}">
            
            <!-- Product Image -->
            <div class="product-image-container">
                @if (!empty($item->img_path))
                <img src="{{ $item->image_url }}" alt="{{ $item->nombre }}" class="product-image">
                @else
                <div class="product-image product-image-placeholder">
                    <i class="fas fa-image fa-3x text-muted"></i>
                </div>
                @endif
                
                <!-- Status Badge -->
                <span class="product-status-badge badge-{{ $item->estado ? 'success' : 'danger' }}">
                    {{ $item->estado ? 'Activo' : 'Inactivo'}}
                </span>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <div class="d-flex align-items-start gap-2 mb-1 flex-wrap">
                    <h5 class="product-name mb-0" title="{{ $item->nombreCompleto }}">{{ $item->nombre }}</h5>
                    @if($item->presentacione && $item->presentacione->sigla)
                    <span class="badge" style="background:#f59e0b;color:#fff;font-size:0.75rem;white-space:nowrap;align-self:center;">
                        {{ $item->presentacione->sigla }}
                    </span>
                    @endif
                </div>

                <div class="product-meta">
                    <span class="product-code">
                        <i class="fas fa-barcode me-1"></i>
                        Código: {{ $item->codigo }}
                    </span>
                    <span class="product-presentation">
                        <i class="fas fa-box me-1"></i>
                        {{ $item->presentacione->caracteristica->nombre ?? 'Sin talla' }}
                    </span>
                </div>

                <div class="product-tags">
                    <span class="product-tag">
                        <i class="fas fa-tag"></i>
                        {{ $item->categoria->caracteristica->nombre ?? 'Sin categoría' }}
                    </span>
                    <span class="product-tag">
                        <i class="fas fa-certificate"></i>
                        {{ $item->marca->caracteristica->nombre ?? 'Sin marca' }}
                    </span>
                </div>

                <div class="product-price">
                    ${{ number_format($item->precio ?? 0, 0, ',', '.') }}
                </div>
            </div>

            <!-- Product Actions -->
            <div class="product-actions">
                @can('ver-producto')
                <button class="btn btn-sm btn-outline-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#verModal-{{$item->id}}"
                        title="Ver detalles">
                    <i class="fas fa-eye"></i>
                </button>
                @endcan

                @can('editar-producto')
                <a href="{{route('productos.edit',['producto' => $item])}}">
                    <button class="btn btn-sm btn-outline-warning" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                </a>
                @endcan

                @can('crear-inventario')
                <form action="{{route('inventario.create')}}" method="get" class="d-inline">
                    <input type="hidden" name="producto_id" value="{{$item->id}}">
                    <button class="btn btn-sm btn-outline-info" type="submit" title="Inventario">
                        <i class="fas fa-warehouse"></i>
                    </button>
                </form>
                @endcan
            </div>
        </div>

        <!-- Modal Ver Producto -->
        <div class="modal fade" id="verModal-{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: white;">
                        <h1 class="modal-title fs-4" id="exampleModalLabel">
                            <i class="fas fa-box-open me-2"></i>
                            Detalles del Producto
                        </h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group-large">
                                    <label>Nombre Completo</label>
                                    <div class="p-3 bg-light rounded">{{ $item->nombreCompleto }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-large">
                                    <label>Precio</label>
                                    <div class="p-3 bg-light rounded">
                                        <span class="fs-4 fw-bold text-primary">
                                            ${{ number_format($item->precio ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-large">
                                    <label>Categoría</label>
                                    <div class="p-3 bg-light rounded">{{ $item->categoria->caracteristica->nombre ?? 'Sin categoría' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-large">
                                    <label>Marca</label>
                                    <div class="p-3 bg-light rounded">{{ $item->marca->caracteristica->nombre ?? 'Sin marca' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group-large">
                                    <label>Descripción</label>
                                    <div class="p-3 bg-light rounded">{{ $item->descripcion ?? 'No tiene descripción' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group-large">
                                    <label>Imagen del Producto</label>
                                    <div class="text-center p-4 bg-light rounded">
                                        @if (!empty($item->img_path))
                                        <img src="{{ $item->image_url }}" alt="{{ $item->nombre }}"
                                            class="img-fluid rounded shadow" style="max-height: 400px;">
                                        @else
                                        <div class="empty-state py-5">
                                            <i class="fas fa-image"></i>
                                            <h3>Sin imagen</h3>
                                            <p>Este producto no tiene imagen</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-modern-primary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @empty
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No hay productos registrados</h3>
            <p>Comienza agregando tu primer producto</p>
            @can('crear-producto')
            <a href="{{route('productos.create')}}">
                <button class="btn-action-large btn-success">
                    <i class="fas fa-plus-circle"></i>
                    <span>Crear Primer Producto</span>
                </button>
            </a>
            @endcan
        </div>
        @endforelse
    </div>

    <!-- No Results Message -->
    <div id="noResults" class="empty-state" style="display: none;">
        <i class="fas fa-search"></i>
        <h3>No se encontraron productos</h3>
        <p>Intenta con otro término de búsqueda</p>
    </div>
</div>

@endsection

@push('js')
<script>
    let currentView = 'grid';

    function clearFilters() {
        document.getElementById('searchProducts').value = '';
        document.getElementById('filterCategory').value = '';
        document.getElementById('filterMarca').value = '';
        document.getElementById('filterStatus').value = '';
        filterProducts();
    }

    function filterProducts() {
        const searchTerm = document.getElementById('searchProducts').value.toLowerCase();
        const categoryFilter = document.getElementById('filterCategory').value;
        const marcaFilter = document.getElementById('filterMarca').value;
        const statusFilter = document.getElementById('filterStatus').value;
        
        const products = document.querySelectorAll('.product-card');
        let visibleCount = 0;

        products.forEach(product => {
            const searchData = product.getAttribute('data-search');
            const category = product.getAttribute('data-category');
            const marca = product.getAttribute('data-marca');
            const status = product.getAttribute('data-status');

            const matchesSearch = searchData.includes(searchTerm);
            const matchesCategory = !categoryFilter || category === categoryFilter;
            const matchesMarca = !marcaFilter || marca === marcaFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            if (matchesSearch && matchesCategory && matchesMarca && matchesStatus) {
                product.style.display = '';
                visibleCount++;
            } else {
                product.style.display = 'none';
            }
        });

        // Show/hide no results message
        document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
    }

    function setView(view) {
        currentView = view;
        const grid = document.getElementById('productsGrid');
        const familyContainer = document.getElementById('familyContainer');
        const gridBtn = document.getElementById('gridViewBtn');
        const listBtn = document.getElementById('listViewBtn');
        const familyBtn = document.getElementById('familyViewBtn');

        [gridBtn, listBtn, familyBtn].forEach(b => b && b.classList.remove('active'));

        if (view === 'familia') {
            grid.style.display = 'none';
            familyContainer.style.display = '';
            familyBtn.classList.add('active');
            buildFamilyView();
            return;
        }

        familyContainer.style.display = 'none';
        grid.style.display = '';

        if (view === 'grid') {
            grid.classList.remove('products-list');
            grid.classList.add('products-grid');
            gridBtn.classList.add('active');
        } else {
            grid.classList.remove('products-grid');
            grid.classList.add('products-list');
            listBtn.classList.add('active');
        }
    }

    function buildFamilyView() {
        const cards = document.querySelectorAll('#productsGrid .product-card');
        const families = {};

        cards.forEach(card => {
            const rawName = card.querySelector('.product-name') ? card.querySelector('.product-name').textContent.trim() : '';
            const talla = card.dataset.talla || '';
            const tallaNombre = card.dataset.tallaNombre || '';
            const stock = parseInt(card.dataset.stock) || 0;
            const editHref = card.dataset.editHref || '#';
            const status = card.dataset.status;

            // Derive base name: remove " - TALLA" suffix
            let baseName = rawName;
            if (tallaNombre && rawName.endsWith(' - ' + tallaNombre)) {
                baseName = rawName.slice(0, rawName.length - (' - ' + tallaNombre).length).trim();
            } else if (talla && rawName.endsWith(' - ' + talla)) {
                baseName = rawName.slice(0, rawName.length - (' - ' + talla).length).trim();
            }

            if (!families[baseName]) families[baseName] = [];
            families[baseName].push({ talla, tallaNombre, stock, editHref, active: status == '1' });
        });

        const container = document.getElementById('familyContainer');
        container.innerHTML = '';

        Object.entries(families).sort((a,b) => a[0].localeCompare(b[0])).forEach(([baseName, variants]) => {
            const col = document.createElement('div');
            col.className = 'col-md-4 col-lg-3 col-sm-6';

            const variantBadges = variants.map(v => {
                let bg = '#059669'; // green for ok stock
                if (!v.active) bg = '#9ca3af';
                else if (v.stock <= 0) bg = '#dc2626';
                else if (v.stock <= 3) bg = '#f59e0b';
                const displayTalla = v.talla || 'T.U.';
                return `<a href="${v.editHref}" class="d-inline-flex flex-column align-items-center text-decoration-none me-1 mb-1"
                           style="background:${bg};color:#fff;border-radius:8px;padding:4px 10px;font-size:0.8rem;font-weight:700;min-width:42px;">
                    <span>${displayTalla}</span>
                    <span style="font-size:0.65rem;font-weight:400;opacity:0.9;">${v.stock} uds</span>
                </a>`;
            }).join('');

            col.innerHTML = `
                <div class="card shadow-sm h-100">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-2" style="font-size:0.9rem;">${baseName}</h6>
                        <div class="d-flex flex-wrap">${variantBadges}</div>
                        <small class="text-muted mt-1 d-block">${variants.length} talla(s)</small>
                    </div>
                </div>`;
            container.appendChild(col);
        });

        if (container.childElementCount === 0) {
            container.innerHTML = '<div class="col-12 text-center text-muted py-4"><i class="fas fa-layer-group fa-3x mb-2 opacity-25"></i><p>No hay productos con variantes de talla</p></div>';
        }
    }
</script>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-upload"></i> Importar Productos desde Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('productos.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Archivo CSV:</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".csv,.txt" required>
                        <small class="text-muted">Tamaño máximo: 2MB</small>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> Formato del archivo:</strong>
                        <p class="mb-1">El archivo CSV debe tener las siguientes columnas en este orden:</p>
                        <code>Código, Nombre, Descripción, Precio, Categoría, Marca, Presentación, Stock, Estado</code>
                        
                        <hr>
                        <p class="mb-1"><strong>Notas importantes:</strong></p>
                        <ul class="mb-0">
                            <li><strong>Código:</strong> Opcional (se genera automáticamente si está vacío)</li>
                            <li><strong>Nombre:</strong> Requerido</li>
                            <li><strong>Precio:</strong> Requerido (número)</li>
                            <li><strong>Categoría, Marca, Presentación:</strong> Deben existir en el sistema</li>
                            <li><strong>Stock:</strong> Número (0 por defecto)</li>
                            <li><strong>Estado:</strong> "Activo" o "Inactivo"</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-success">
                        <i class="fas fa-lightbulb"></i> <strong>Consejo:</strong> 
                        Puedes usar el botón "Exportar a Excel" para descargar un archivo de ejemplo con el formato correcto.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush



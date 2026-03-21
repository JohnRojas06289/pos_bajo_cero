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
            <a href="{{route('productos.export')}}" class="btn btn-sm btn-ghost" style="border-color:rgba(255,255,255,0.3);color:white;" title="Exportar CSV">
                <i class="fas fa-file-csv"></i>
                <span class="d-none d-sm-inline">Exportar</span>
            </a>
            @endcan
            @can('crear-producto')
            <button type="button" class="btn btn-sm btn-ghost" style="border-color:rgba(255,255,255,0.3);color:white;" data-bs-toggle="modal" data-bs-target="#importModal" title="Importar Excel o CSV">
                <i class="fas fa-file-upload"></i>
                <span class="d-none d-sm-inline">Importar</span>
            </button>
            @endcan
            @if(config('services.gemini.api_key'))
            @can('crear-producto')
            <button type="button" class="btn btn-sm btn-ghost" id="btnCrearDesdeImg"
                    style="border-color:rgba(255,255,255,0.3);color:white;"
                    data-bs-toggle="modal" data-bs-target="#crearDesdeImagenesModal"
                    title="Subir fotos y crear productos automáticamente con IA">
                <i class="fas fa-camera"></i>
                <span class="d-none d-sm-inline">Crear con IA</span>
            </button>
            @endcan
            @can('editar-producto')
            <button type="button" class="btn btn-sm btn-ghost" id="btnGenAllDesc"
                    style="border-color:rgba(255,255,255,0.3);color:white;"
                    title="Generar descripciones con IA para productos sin descripción"
                    onclick="generateAllDescriptions()">
                <i class="fas fa-wand-magic-sparkles"></i>
                <span class="d-none d-sm-inline">IA Descripciones</span>
            </button>
            @endcan
            @endif
            @can('crear-producto')
            <a href="{{route('productos.create')}}" class="btn btn-sm" style="background:rgba(255,255,255,0.95);color:#1B4F72;font-weight:700;border:none;">
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
                    <span class="badge bg-warning" style="font-size:0.75rem;white-space:nowrap;align-self:center;">
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
                @if($item->inventario)
                <a href="{{route('inventario.edit', $item->inventario->id)}}" class="btn btn-sm btn-outline-success" title="Editar Inventario">
                    <i class="fas fa-boxes"></i>
                </a>
                @else
                <form action="{{route('inventario.create')}}" method="get" class="d-inline">
                    <input type="hidden" name="producto_id" value="{{$item->id}}">
                    <button class="btn btn-sm btn-outline-info" type="submit" title="Inicializar Inventario">
                        <i class="fas fa-warehouse"></i>
                    </button>
                </form>
                @endif
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                            class="img-fluid rounded shadow" style="max-height: 400px;" loading="lazy">
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

{{-- ── Modal: Crear productos desde imágenes con IA ─────────────────── --}}
<div class="modal fade" id="crearDesdeImagenesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#1B4F72 0%,#0A1628 100%);color:#fff;">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2" style="color:#1D96C8;"></i>
                    Crear Productos con IA desde Fotos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter:invert(1);"></button>
            </div>
            <div class="modal-body p-4">

                {{-- Drop zone --}}
                <div id="aiDropZone"
                     style="border:2px dashed var(--border-color);border-radius:12px;padding:2rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
                     ondragover="aiDragOver(event)" ondragleave="aiDragLeave(event)" ondrop="aiDrop(event)"
                     onclick="document.getElementById('aiImgInput').click()">
                    <i class="fas fa-cloud-upload-alt fa-3x mb-3" style="color:var(--accent);opacity:.7;"></i>
                    <p class="mb-1 fw-semibold">Arrastra las fotos aquí o haz clic para seleccionarlas</p>
                    <p class="text-muted small mb-0">JPG, PNG, WebP — máx. 5 MB por imagen — hasta 15 fotos</p>
                    <input type="file" id="aiImgInput" accept="image/*" multiple style="display:none;"
                           onchange="aiFilesSelected(this.files)">
                </div>

                {{-- Preview grid --}}
                <div id="aiPreviewGrid" class="row g-2 mt-3" style="display:none!important;"></div>

                {{-- Progress --}}
                <div id="aiProgressWrap" class="mt-3" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold" id="aiProgressLabel">Analizando imagen 1…</span>
                        <span class="small text-muted" id="aiProgressFrac">0 / 0</span>
                    </div>
                    <div class="progress" style="height:8px;">
                        <div id="aiProgressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                             style="width:0%;background:var(--accent);"></div>
                    </div>
                </div>

                {{-- Results --}}
                <div id="aiResults" class="mt-3" style="display:none;"></div>

            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" id="btnCrearIA" class="btn btn-primary" onclick="crearDesdeImagenesIA()" disabled>
                    <i class="fas fa-wand-magic-sparkles me-1"></i>
                    Crear Productos con IA
                    <span id="aiImgCount" class="badge bg-light text-dark ms-1" style="display:none;"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
/* ─── Crear productos desde imágenes con IA ───────────────────────── */
let aiFiles = [];

function aiDragOver(e) {
    e.preventDefault();
    document.getElementById('aiDropZone').style.borderColor = 'var(--accent)';
    document.getElementById('aiDropZone').style.background  = 'rgba(29,150,200,0.05)';
}
function aiDragLeave(e) {
    document.getElementById('aiDropZone').style.borderColor = 'var(--border-color)';
    document.getElementById('aiDropZone').style.background  = '';
}
function aiDrop(e) {
    e.preventDefault();
    aiDragLeave(e);
    aiFilesSelected(e.dataTransfer.files);
}
function aiFilesSelected(fileList) {
    const allowed = Array.from(fileList).filter(f => f.type.startsWith('image/'));
    // merge, deduplicate by name+size, max 15
    allowed.forEach(f => {
        if (aiFiles.length < 15 && !aiFiles.find(x => x.name === f.name && x.size === f.size)) {
            aiFiles.push(f);
        }
    });
    renderAiPreviews();
}
function removeAiFile(idx) {
    aiFiles.splice(idx, 1);
    renderAiPreviews();
}
function renderAiPreviews() {
    const grid = document.getElementById('aiPreviewGrid');
    const btn  = document.getElementById('btnCrearIA');
    const cnt  = document.getElementById('aiImgCount');

    if (aiFiles.length === 0) {
        grid.style.display = 'none';
        grid.innerHTML     = '';
        btn.disabled       = true;
        cnt.style.display  = 'none';
        return;
    }

    grid.style.cssText = '';          // clear display:none!important
    grid.style.display = 'flex';
    grid.style.flexWrap = 'wrap';
    grid.style.gap = '8px';
    grid.innerHTML = aiFiles.map((f, i) => {
        const url = URL.createObjectURL(f);
        return `<div style="position:relative;width:90px;height:90px;border-radius:8px;overflow:hidden;border:1.5px solid var(--border-color);">
            <img src="${url}" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
            <button onclick="removeAiFile(${i})" type="button"
                    style="position:absolute;top:2px;right:2px;background:rgba(0,0,0,.55);border:none;border-radius:50%;width:22px;height:22px;color:#fff;font-size:.65rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
    }).join('');

    btn.disabled      = false;
    cnt.style.display = '';
    cnt.textContent   = aiFiles.length;
}

async function crearDesdeImagenesIA() {
    if (!aiFiles.length) return;

    const btn         = document.getElementById('btnCrearIA');
    const progressWrap= document.getElementById('aiProgressWrap');
    const progressBar = document.getElementById('aiProgressBar');
    const progressLbl = document.getElementById('aiProgressLabel');
    const progressFrc = document.getElementById('aiProgressFrac');
    const resultsDiv  = document.getElementById('aiResults');

    btn.disabled      = true;
    progressWrap.style.display = '';
    resultsDiv.style.display   = 'none';
    resultsDiv.innerHTML       = '';

    const total   = aiFiles.length;
    let   done    = 0;
    const results = [];

    for (const file of aiFiles) {
        progressLbl.textContent = `Analizando: ${file.name}`;
        progressFrc.textContent = `${done} / ${total}`;
        progressBar.style.width = `${Math.round((done / total) * 100)}%`;

        try {
            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}');
            fd.append('imagenes[]', file);

            const res  = await fetch('{{ route("productos.crear-desde-imagenes") }}', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.error) {
                results.push({ success: false, nombre: file.name, error: data.error });
            } else {
                (data.results || []).forEach(r => results.push({ ...r, filename: file.name }));
            }
        } catch (e) {
            results.push({ success: false, nombre: file.name, error: e.message });
        }

        done++;
        progressBar.style.width = `${Math.round((done / total) * 100)}%`;
        progressFrc.textContent  = `${done} / ${total}`;
    }

    progressLbl.textContent = '¡Listo!';
    progressBar.classList.remove('progress-bar-animated');

    // Render results
    const ok  = results.filter(r => r.success);
    const err = results.filter(r => !r.success);

    let html = '';
    if (ok.length) {
        html += `<div class="alert alert-success mb-2 py-2">
            <i class="fas fa-check-circle me-1"></i>
            <strong>${ok.length} producto${ok.length !== 1 ? 's' : ''} creado${ok.length !== 1 ? 's' : ''} correctamente.</strong>
            Los productos se crearon como <em>inactivos</em> para que puedas revisarlos.
        </div>
        <div class="list-group list-group-flush mb-2">`;
        ok.forEach(r => {
            html += `<a href="${r.edit_url}" target="_blank"
                       class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2"
                       style="font-size:.9rem;">
                <i class="fas fa-box text-success"></i>
                <span class="flex-grow-1">${r.nombre}</span>
                <span class="badge bg-warning text-dark">Editar</span>
            </a>`;
        });
        html += '</div>';
    }
    if (err.length) {
        html += `<div class="alert alert-danger mb-0 py-2">
            <i class="fas fa-exclamation-triangle me-1"></i>
            <strong>${err.length} imagen${err.length !== 1 ? 'es' : ''} con error:</strong>
            <ul class="mb-0 mt-1 ps-3 small">`;
        err.forEach(r => { html += `<li>${r.nombre ?? r.filename ?? ''}: ${r.error}</li>`; });
        html += '</ul></div>';
    }

    resultsDiv.innerHTML = html;
    resultsDiv.style.display = '';

    btn.disabled = false;

    // Reload page after 3s if any products created
    if (ok.length > 0) {
        setTimeout(() => window.location.reload(), 3500);
    }
}

// Reset modal state when closed
document.getElementById('crearDesdeImagenesModal').addEventListener('hidden.bs.modal', function () {
    aiFiles = [];
    document.getElementById('aiPreviewGrid').innerHTML = '';
    document.getElementById('aiPreviewGrid').style.display = 'none';
    document.getElementById('aiProgressWrap').style.display = 'none';
    document.getElementById('aiProgressBar').style.width = '0%';
    document.getElementById('aiProgressBar').classList.add('progress-bar-animated');
    document.getElementById('aiResults').style.display = 'none';
    document.getElementById('aiResults').innerHTML = '';
    document.getElementById('btnCrearIA').disabled = true;
    document.getElementById('aiImgCount').style.display = 'none';
    document.getElementById('aiImgInput').value = '';
});
</script>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-upload me-2"></i>Importar Productos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('productos.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">

                    {{-- Download template --}}
                    <div class="d-flex align-items-center gap-3 p-3 rounded mb-3"
                         style="background:#eff6ff;border:1px solid #bfdbfe;">
                        <i class="fas fa-file-excel fa-2x" style="color:#1d6f42;flex-shrink:0;"></i>
                        <div class="flex-1">
                            <div class="fw-bold" style="font-size:0.9rem;">Plantilla Excel lista para llenar</div>
                            <div class="text-muted" style="font-size:0.8rem;">
                                Descarga la plantilla, llénala con tus productos y súbela aquí.
                                Incluye columnas para foto (URL), color, material, género y más.
                            </div>
                        </div>
                        <a href="{{ route('productos.template') }}" class="btn btn-success btn-sm flex-shrink-0" target="_blank">
                            <i class="fas fa-download me-1"></i>Descargar plantilla
                        </a>
                    </div>

                    {{-- File input --}}
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold">Selecciona tu archivo:</label>
                        <input type="file" class="form-control" id="file" name="file"
                               accept=".csv,.txt,.xlsx,.xls" required>
                        <small class="text-muted">Acepta: <strong>Excel (.xlsx, .xls)</strong> o CSV — Máx. 5MB</small>
                    </div>

                    {{-- Column guide --}}
                    <div class="alert alert-info mb-0" style="font-size:0.82rem;">
                        <strong><i class="fas fa-table me-1"></i>Columnas de la plantilla:</strong>
                        <div class="mt-2 row g-1">
                            @foreach([
                                ['Código','Opcional (auto si vacío)','secondary'],
                                ['Nombre','Requerido','danger'],
                                ['Descripción','Opcional','secondary'],
                                ['Precio','Número sin símbolos','secondary'],
                                ['Categoría','Nombre exacto del sistema','secondary'],
                                ['Marca','Nombre exacto del sistema','secondary'],
                                ['Presentación','Nombre de la talla','secondary'],
                                ['Stock','Número entero','secondary'],
                                ['Estado','Activo o Inactivo','secondary'],
                                ['Color','Ej: Negro','secondary'],
                                ['Material','Ej: Cuero','secondary'],
                                ['Género','Hombre, Mujer o Unisex','secondary'],
                                ['URL_Imagen','URL pública de la imagen','secondary'],
                            ] as [$col, $desc, $badge])
                            <div class="col-12 col-sm-6 d-flex align-items-baseline gap-1">
                                <span class="badge bg-{{ $badge }} text-truncate" style="min-width:90px;font-size:0.72rem;">{{ $col }}</span>
                                <small class="text-muted">{{ $desc }}</small>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- AI Descriptions progress toast --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
    <div id="aiToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="aiToastBody">
                <i class="fas fa-spinner fa-spin me-2"></i> Generando descripciones con IA...
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
/* ─── AI: Generate all descriptions ─────────────────── */
async function generateAllDescriptions() {
    const btn = document.getElementById('btnGenAllDesc');
    const toast = document.getElementById('aiToast');
    const toastBody = document.getElementById('aiToastBody');

    if (!confirm('¿Generar descripciones con IA para todos los productos que no tienen? (máx. 15 a la vez)')) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="d-none d-sm-inline">Generando...</span>';

    toastBody.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generando descripciones con Gemini AI...';
    const bsToast = new bootstrap.Toast(toast, { autohide: false });
    bsToast.show();

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                       || '{{ csrf_token() }}';

        const res = await fetch('{{ route("productos.generate-all-descriptions") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (data.error) {
            toastBody.innerHTML = '<i class="fas fa-times-circle me-2 text-danger"></i>' + data.error;
            setTimeout(() => bsToast.hide(), 4000);
        } else {
            toastBody.innerHTML = '<i class="fas fa-check-circle me-2 text-success"></i>' + data.message
                + (data.remaining > 0 ? ` (${data.remaining} aún sin descripción)` : ' ¡Todos actualizados!');
            setTimeout(() => { bsToast.hide(); if (data.count > 0) window.location.reload(); }, 3500);
        }
    } catch (e) {
        toastBody.innerHTML = '<i class="fas fa-exclamation-triangle me-2 text-warning"></i>Error de conexión.';
        setTimeout(() => bsToast.hide(), 3000);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-wand-magic-sparkles"></i> <span class="d-none d-sm-inline">IA Descripciones</span>';
    }
}
</script>
@endpush



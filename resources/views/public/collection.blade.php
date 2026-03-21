@extends('layouts.public')

@section('title', 'Catálogo | Bajo Cero')
@section('meta_description', 'Explora el catálogo completo de Bajo Cero. Chaquetas, gorras y ropa urbana de montaña. Filtra por categoría, marca y precio.')

@section('content')
<div class="container px-5 mt-5 pt-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="fw-bolder text-white">NUESTRO CATÁLOGO</h1>
        <p class="lead fw-normal text-muted mb-0">Descubre las últimas tendencias en moda urbana</p>
        <div style="width:50px;height:3px;background-color:var(--primary-color);margin:20px auto;"></div>
    </div>

    <div class="row gx-5">
        <!-- Filter Toggle Button (Mobile/Tablet Only) -->
        <div class="col-12 d-lg-none mb-4">
            <button class="btn btn-neon w-100 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="fas fa-filter me-2"></i> VER FILTROS
            </button>
        </div>

        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="collapse d-lg-block mb-5" id="filterCollapse">
                <div class="filter-card p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0 text-primary"><i class="fas fa-sliders-h me-2"></i>FILTROS</h4>
                    </div>

                    <form action="{{ route('collection') }}" method="GET">
                        <!-- Text Search -->
                        <div class="mb-4">
                            <label class="filter-label">Buscar</label>
                            <input type="text" name="search" class="form-control form-control-dark" placeholder="¿Qué buscas?" value="{{ request('search') }}">
                        </div>

                        <!-- Categories -->
                        <div class="mb-4">
                            <label class="filter-label">Categoría</label>
                            <select name="categoria" class="form-select form-control-dark">
                                <option value="all">Todas las categorías</option>
                                @foreach($categorias as $cat)
                                    @php $catNombre = $cat->caracteristica->nombre ?? ''; @endphp
                                    @if($catNombre)
                                        <option value="{{ $catNombre }}" {{ request('categoria') == $catNombre ? 'selected' : '' }}>
                                            {{ $catNombre }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- Brands -->
                        <div class="mb-4">
                            <label class="filter-label">Marca</label>
                            <select name="marca" class="form-select form-control-dark">
                                <option value="all">Todas las marcas</option>
                                @foreach($marcas as $marca)
                                    @php $marcaNombre = $marca->caracteristica->nombre ?? ''; @endphp
                                    @if($marcaNombre)
                                        <option value="{{ $marcaNombre }}" {{ request('marca') == $marcaNombre ? 'selected' : '' }}>
                                            {{ $marcaNombre }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-neon w-100 py-2">APLICAR</button>

                        @if(request()->hasAny(['search', 'categoria', 'marca']))
                            <a href="{{ route('collection') }}" class="btn btn-link w-100 mt-2 text-decoration-none small" style="color:var(--text-muted);">
                                <i class="fas fa-times me-1"></i> Limpiar Filtros
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            {{-- Contador de resultados --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="text-muted small mb-0">
                    {{ $products->total() }} artículo{{ $products->total() !== 1 ? 's' : '' }} encontrado{{ $products->total() !== 1 ? 's' : '' }}
                </p>
            </div>

            <div class="row gx-3 gx-md-4 row-cols-2 row-cols-md-3 row-cols-xl-3 justify-content-center">
                @forelse($products as $product)
                    @php
                        $totalStock  = $product->total_stock;
                        $variantes   = $product->variantes;
                        $hasVariants = $variantes->count() > 0;
                    @endphp
                    <div class="col mb-5">
                        <div class="card product-card h-100 border-0">
                            {{-- Stock badge --}}
                            @if($totalStock > 0)
                                <div class="badge bg-success text-white position-absolute" style="top:.5rem;right:.5rem">DISPONIBLE</div>
                            @else
                                <div class="badge bg-danger text-white position-absolute" style="top:.5rem;right:.5rem">AGOTADO</div>
                            @endif

                            {{-- Imagen --}}
                            <a href="{{ route('product.show', $product->id) }}" class="d-block overflow-hidden" style="height:250px;">
                                @if($product->img_path)
                                    <img class="card-img-top h-100 w-100" src="{{ $product->image_url }}" alt="{{ $product->nombre }}" style="object-fit:cover;transition:transform .3s ease;" loading="lazy"
                                         onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'" />
                                @else
                                    <div class="h-100 d-flex align-items-center justify-content-center" style="background:#222;">
                                        <i class="fas fa-vest fa-2x text-muted"></i>
                                    </div>
                                @endif
                            </a>

                            {{-- Indicador de múltiples imágenes --}}
                            @if(!empty($product->imagenes) && count($product->imagenes) > 0)
                                <div class="position-absolute" style="top:.5rem;left:.5rem;">
                                    <span class="badge" style="background:rgba(0,0,0,.6);font-size:.65rem;">
                                        <i class="fas fa-images me-1"></i>{{ count($product->imagenes) + 1 }}
                                    </span>
                                </div>
                            @endif

                            <!-- Detalles -->
                            <div class="card-body p-4">
                                <div class="text-start">
                                    <div class="small text-muted text-uppercase mb-1">{{ $product->marca->caracteristica->nombre ?? 'Jacket Store' }}</div>
                                    <h5 class="fw-bolder text-white text-truncate mb-1">{{ $product->nombre }}</h5>
                                    @if($product->categoria && $product->categoria->caracteristica)
                                        <div class="small text-muted mb-2">{{ $product->categoria->caracteristica->nombre }}</div>
                                    @endif
                                    <div class="text-info fw-bold fs-5">${{ number_format($product->precio, 0) }}</div>

                                    {{-- Badges de variantes (talla / color) --}}
                                    @if($hasVariants)
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        @foreach($variantes as $v)
                                            <span class="badge {{ $v->stock > 0 ? 'bg-secondary' : 'bg-dark opacity-50' }}"
                                                  style="font-size:.72rem;padding:4px 8px;"
                                                  title="{{ $v->stock > 0 ? $v->stock . ' disponibles' : 'Agotado' }}">
                                                {{ $v->label }}
                                            </span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Acción -->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <a class="btn btn-outline-info mt-auto w-100" href="{{ route('product.show', $product->id) }}">VER DETALLES</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <h3>No encontramos productos</h3>
                            <p>Intenta ajustar tus filtros de búsqueda.</p>
                            <a href="{{ route('collection') }}" class="btn btn-outline-light btn-sm mt-2">Ver todos los productos</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

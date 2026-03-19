@extends('layouts.public')

@section('title', 'Jacket Store | Inicio')

@section('content')
<!-- Hero Section -->
<header class="hero-section">
    <div class="container px-5">
        <div class="row gx-5 align-items-center justify-content-center">
            <div class="col-lg-8 col-xl-7 col-xxl-6">
                <div class="my-5 text-center text-xl-start">
                    <h1 class="hero-title mb-2">
                        Estilo que <br><span>Desafía</span>
                    </h1>
                    <p class="lead fw-normal mb-4" style="color:rgba(255,255,255,0.6);">Descubre la nueva colección de chaquetas y gorras diseñadas para la vida urbana. Calidad premium, diseño exclusivo y la actitud que necesitas.</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center justify-content-xl-start">
                        <a class="btn-neon px-4 me-sm-3" href="{{ route('collection') }}">Ver Colección</a>
                        <a class="btn btn-outline-light btn-lg px-4" href="{{ route('contact') }}">Contactar</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-5 col-xxl-6 d-none d-xl-block text-center">
                <div class="hero-img-placeholder">
                    <i class="fas fa-vest fa-5x" style="color:var(--primary-color);opacity:.8;filter:drop-shadow(0 0 10px var(--primary-color));"></i>
                    <div class="hero-img-label">
                        <i class="fas fa-star fa-spin"></i> Nueva Colección
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Features Section -->
<section class="py-5 welcome-alt-section">
    <div class="container px-5 my-5">
        <div class="row gx-5">
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h2 class="h4 fw-bold welcome-feature-title"><i class="fas fa-check-circle text-primary me-2"></i>Calidad Premium</h2>
                <p class="welcome-feature-text">Materiales seleccionados rigurosamente para garantizar durabilidad y confort en cada prenda.</p>
            </div>
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h2 class="h4 fw-bold welcome-feature-title"><i class="fas fa-bolt text-warning me-2"></i>Diseño Exclusivo</h2>
                <p class="welcome-feature-text">Ediciones limitadas y diseños únicos que no encontrarás en ningún otro lugar.</p>
            </div>
            <div class="col-lg-4">
                <h2 class="h4 fw-bold welcome-feature-title"><i class="fas fa-box text-success me-2"></i>Envío Seguro</h2>
                <p class="welcome-feature-text">Recibe tus productos en la puerta de tu casa con total garantía y seguimiento en tiempo real.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5">
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h2 class="fw-bolder section-title-pub">Tendencias <span style="color:var(--primary-color);">Destacadas</span></h2>
            <p class="lead fw-normal text-muted mb-0">Lo más buscado de la temporada</p>
        </div>
        <div class="row gx-5">
            @forelse($featuredProducts as $product)
                <div class="col-lg-3 col-md-6 mb-5">
                    <div class="pub-product-card h-100">
                        {{-- Badge de stock real --}}
                        @php $stock = $product->inventario->cantidad ?? 0; @endphp
                        @if($stock > 0)
                            <span class="badge-stock-in">DISPONIBLE</span>
                        @else
                            <span class="badge-stock-out">AGOTADO</span>
                        @endif

                        {{-- Imagen del producto --}}
                        <a href="{{ route('product.show', $product->id) }}" class="d-block overflow-hidden" style="height:280px;">
                            @if($product->img_path)
                                <img class="pub-product-card-img" src="{{ $product->image_url }}" alt="{{ $product->nombre }}" loading="lazy" />
                            @else
                                <div class="pub-product-card-noimg">
                                    <i class="fas fa-vest fa-3x"></i>
                                </div>
                            @endif
                        </a>

                        <div class="pub-product-card-body">
                            <div class="pub-product-card-brand">{{ $product->marca->caracteristica->nombre ?? 'Jacket Store' }}</div>
                            <h5 class="pub-product-card-title text-truncate">{{ $product->nombre }}</h5>
                            <div class="d-flex small text-warning mb-2">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                            <div class="pub-product-card-price">${{ number_format($product->precio, 0) }}</div>
                        </div>
                        <div class="pub-product-card-footer">
                            <a class="btn btn-outline-primary w-100" href="{{ route('product.show', $product->id) }}">Ver Detalles</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">
                    <p>No hay productos destacados por el momento.</p>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('collection') }}" class="btn-neon px-5">Ver Toda la Colección</a>
        </div>
    </div>
</section>

<!-- Banner / Call to Action -->
<section class="py-5 welcome-cta-section">
    <div class="container px-5 my-5">
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-8 col-xl-6 text-center">
                <h2 class="display-5 fw-bold section-title-pub mb-3">¿LISTO PARA EL SIGUIENTE NIVEL?</h2>
                <p class="lead fw-normal text-muted mb-4">Explora nuestro catálogo completo y encuentra tu estilo. La colección ya está disponible.</p>
                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                    <a class="btn-neon px-5" href="{{ route('collection') }}">IR AL CATÁLOGO</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TikTok Section -->
<section class="py-5 welcome-alt-section">
    <div class="container text-center">
        <h2 class="fw-bold mb-5 section-title-pub">SÍGUENOS EN <span style="color:var(--primary-color);">TIKTOK</span></h2>
        <div class="welcome-tiktok-box d-inline-block">
            <i class="fab fa-tiktok fa-3x mb-3" style="color:var(--text-color);"></i>
            <p style="color:var(--text-color);">Descubre nuestro contenido exclusivo</p>
            <a href="#" class="btn btn-danger btn-sm">Seguir en TikTok</a>
        </div>
    </div>
</section>
@endsection

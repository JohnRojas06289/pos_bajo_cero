@extends('layouts.public')

@section('title', 'Jacket Store | ' . $product->nombre)

@push('css')
<style>
/* ── Gallery ── */
.gallery-main-wrap {
    position: relative;
    background: #111;
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 1 / 1;
    cursor: zoom-in;
}
.gallery-main-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .3s ease;
    display: block;
}
.gallery-main-wrap:hover img { transform: scale(1.04); }

.btn-expand {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(0,0,0,.55);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: .8rem;
    cursor: pointer;
    backdrop-filter: blur(4px);
    transition: background .2s;
    z-index: 2;
}
.btn-expand:hover { background: rgba(0,0,0,.85); }

.gallery-no-img {
    width: 100%;
    aspect-ratio: 1 / 1;
    background: #1a1a1a;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Thumbnails */
.thumbs-strip {
    display: flex;
    gap: 8px;
    margin-top: 10px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.thumbs-strip::-webkit-scrollbar { display: none; }

.thumb-item {
    flex-shrink: 0;
    width: 72px;
    height: 72px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color .2s, transform .2s;
    background: #1a1a1a;
}
.thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.thumb-item:hover { transform: scale(1.05); }
.thumb-item.active { border-color: var(--primary-color); }

/* ── Lightbox ── */
.lightbox-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.94);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    padding: 1rem;
}
.lightbox-overlay.open { display: flex; }

.lightbox-img-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    max-height: calc(100vh - 120px);
    position: relative;
}
.lightbox-img-wrap img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 8px;
    user-select: none;
}

.lb-btn {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    border-radius: 50%;
    width: 48px;
    height: 48px;
    font-size: 1.1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .2s;
    z-index: 10001;
}
.lb-btn:hover { background: rgba(255,255,255,.25); }
.lb-prev { left: 12px; }
.lb-next { right: 12px; }
.lb-close {
    position: fixed;
    top: 14px;
    right: 16px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .2s;
    z-index: 10001;
}
.lb-close:hover { background: rgba(255,0,0,.4); }

.lb-counter {
    color: rgba(255,255,255,.6);
    font-size: .8rem;
    margin-top: 10px;
    letter-spacing: .08em;
    flex-shrink: 0;
}

.lb-thumbs {
    display: flex;
    gap: 6px;
    margin-top: 10px;
    flex-shrink: 0;
    overflow-x: auto;
    scrollbar-width: none;
    padding: 0 6px 4px;
}
.lb-thumbs::-webkit-scrollbar { display: none; }
.lb-thumb {
    width: 56px;
    height: 56px;
    border-radius: 6px;
    overflow: hidden;
    cursor: pointer;
    flex-shrink: 0;
    border: 2px solid transparent;
    transition: border-color .15s;
}
.lb-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.lb-thumb.active { border-color: var(--primary-color); }

/* ── Product info ── */
.product-price-display {
    font-size: 2rem;
    font-weight: 800;
    color: var(--primary-color);
    font-family: 'JetBrains Mono', monospace, sans-serif;
}
.stock-pill {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .9rem;
    border-radius: 20px;
    font-size: .8rem;
    font-weight: 700;
}
.stock-pill.in { background: rgba(39,174,96,.15); color: #27ae60; border: 1px solid rgba(39,174,96,.3); }
.stock-pill.out { background: rgba(231,76,60,.15); color: #e74c3c; border: 1px solid rgba(231,76,60,.3); }
.stock-pill.unknown { background: rgba(255,255,255,.08); color: rgba(255,255,255,.5); border: 1px solid rgba(255,255,255,.15); }

.info-row {
    display: flex;
    gap: .5rem;
    align-items: center;
    padding: .6rem 0;
    border-bottom: 1px solid rgba(255,255,255,.06);
    font-size: .9rem;
}
.info-row .info-label { color: rgba(255,255,255,.4); min-width: 90px; font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; }
.info-row .info-val { color: rgba(255,255,255,.85); font-weight: 500; }

.related-card {
    background: #111;
    border-radius: 12px;
    overflow: hidden;
    transition: transform .25s ease, box-shadow .25s ease;
    border: 1px solid rgba(255,255,255,.07);
}
.related-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.5); }
.related-card img { width: 100%; height: 180px; object-fit: cover; display: block; }
.related-card .rc-body { padding: .75rem 1rem; }
.related-card .rc-name { font-weight: 700; font-size: .9rem; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.related-card .rc-price { color: var(--primary-color); font-weight: 700; font-size: .9rem; }
</style>
@endpush

@section('content')
@php
    $allImages = $product->todas_imagenes_urls; // [{path, url, main}]
    $mainImgUrl = count($allImages) > 0 ? $allImages[0]['url'] : null;
    $stock = $product->inventario->cantidad ?? null;
@endphp

<div class="container px-5 mt-5 pt-5">
    <div class="row gx-5 align-items-start">

        {{-- ══ GALERÍA DE IMÁGENES ══ --}}
        <div class="col-lg-6 mb-5 mb-lg-0">
            @if($mainImgUrl)
                {{-- Imagen principal --}}
                <div class="gallery-main-wrap" id="galleryMain" onclick="openLightbox(0)">
                    <img id="mainImg" src="{{ $mainImgUrl }}" alt="{{ $product->nombre }}" />
                    <button class="btn-expand" title="Ver en pantalla completa">
                        <i class="fas fa-expand-alt"></i>
                    </button>
                </div>

                {{-- Miniaturas (solo si hay más de 1 imagen) --}}
                @if(count($allImages) > 1)
                    <div class="thumbs-strip" id="thumbsStrip">
                        @foreach($allImages as $i => $img)
                            <div class="thumb-item {{ $i === 0 ? 'active' : '' }}"
                                 data-src="{{ $img['url'] }}"
                                 data-index="{{ $i }}"
                                 onclick="selectThumb(this, {{ $i }})">
                                <img src="{{ $img['url'] }}" alt="Foto {{ $i + 1 }}" loading="lazy" />
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="gallery-no-img">
                    <i class="fas fa-vest fa-5x text-muted"></i>
                </div>
            @endif
        </div>

        {{-- ══ INFO DEL PRODUCTO ══ --}}
        <div class="col-lg-6">
            {{-- Badges --}}
            <div class="mb-3 d-flex gap-2 flex-wrap">
                @if($product->marca)
                    <span class="badge bg-primary text-dark fw-bold px-3 py-2">{{ $product->marca->nombre }}</span>
                @endif
                @if($product->categoria && $product->categoria->caracteristica)
                    <span class="badge bg-dark border border-secondary text-white fw-normal px-3 py-2">
                        {{ $product->categoria->caracteristica->nombre }}
                    </span>
                @endif
                @if($product->genero)
                    <span class="badge border border-secondary text-white-50 fw-normal px-3 py-2">{{ $product->genero }}</span>
                @endif
            </div>

            <h1 class="fw-bolder text-white mb-3" style="font-size:clamp(1.6rem,4vw,2.4rem);">{{ $product->nombre }}</h1>

            {{-- Precio --}}
            <div class="mb-3">
                <div class="product-price-display">${{ number_format($product->precio, 0) }}</div>
            </div>

            {{-- Stock --}}
            <div class="mb-4">
                @if($stock !== null)
                    @if($stock > 0)
                        <span class="stock-pill in"><i class="fas fa-check-circle"></i>En stock ({{ $stock }} disponibles)</span>
                    @else
                        <span class="stock-pill out"><i class="fas fa-times-circle"></i>Agotado</span>
                    @endif
                @else
                    <span class="stock-pill unknown"><i class="fas fa-question-circle"></i>Consultar disponibilidad</span>
                @endif
            </div>

            {{-- Descripción --}}
            @if($product->descripcion)
                <p class="text-white-50 mb-4" style="line-height:1.7;">{{ $product->descripcion }}</p>
            @endif

            {{-- Detalles del producto --}}
            <div class="mb-4">
                @if($product->codigo)
                    <div class="info-row">
                        <span class="info-label">Código</span>
                        <span class="info-val">{{ $product->codigo }}</span>
                    </div>
                @endif
                @if($product->presentacione)
                    <div class="info-row">
                        <span class="info-label">Presentación</span>
                        <span class="info-val">{{ $product->presentacione->nombre }}</span>
                    </div>
                @endif
                @if($product->color)
                    <div class="info-row">
                        <span class="info-label">Color</span>
                        <span class="info-val">{{ $product->color }}</span>
                    </div>
                @endif
                @if($product->material)
                    <div class="info-row">
                        <span class="info-label">Material</span>
                        <span class="info-val">{{ $product->material }}</span>
                    </div>
                @endif
            </div>

            {{-- Botón WhatsApp para pedir --}}
            <a href="https://wa.me/573001234567?text=Hola!%20Me%20interesa%20el%20producto:%20{{ urlencode($product->nombre) }}"
               target="_blank" rel="noopener"
               class="btn btn-success w-100 py-3 mb-3 fw-bold fs-6">
                <i class="fab fa-whatsapp me-2"></i> CONSULTAR POR WHATSAPP
            </a>

            <a href="{{ route('collection') }}" class="btn btn-outline-secondary w-100 py-2">
                <i class="fas fa-arrow-left me-2"></i> Volver al catálogo
            </a>

            {{-- Garantías --}}
            <div class="mt-4 pt-4 border-top border-secondary">
                <div class="row text-center g-3">
                    <div class="col-4">
                        <i class="fas fa-truck text-primary mb-1 d-block"></i>
                        <span class="text-muted" style="font-size:.72rem;">Envío Colombia</span>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-shield-alt text-success mb-1 d-block"></i>
                        <span class="text-muted" style="font-size:.72rem;">Garantía 30 días</span>
                    </div>
                    <div class="col-4">
                        <i class="fab fa-whatsapp text-success mb-1 d-block"></i>
                        <span class="text-muted" style="font-size:.72rem;">Atención directa</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ PRODUCTOS RELACIONADOS ══ --}}
    @if($relatedProducts->isNotEmpty())
    <div class="mt-5 pt-5">
        <h3 class="fw-bold text-white mb-4">PRODUCTOS RELACIONADOS</h3>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($relatedProducts as $related)
                @php $relStock = $related->inventario->cantidad ?? 0; @endphp
                <div class="col">
                    <a href="{{ route('product.show', $related->id) }}" class="text-decoration-none">
                        <div class="related-card">
                            @if($related->img_path)
                                <img src="{{ $related->image_url }}" alt="{{ $related->nombre }}" loading="lazy" />
                            @else
                                <div style="height:180px;background:#1a1a1a;display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-vest fa-2x text-muted"></i>
                                </div>
                            @endif
                            <div class="rc-body">
                                <div class="rc-name">{{ $related->nombre }}</div>
                                <div class="rc-price">${{ number_format($related->precio, 0) }}</div>
                                @if($relStock <= 0)
                                    <span style="font-size:.7rem;color:#e74c3c;">Agotado</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- ══ LIGHTBOX ══ --}}
@if(count($allImages) > 0)
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightboxOnBg(event)">
    <button class="lb-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>

    @if(count($allImages) > 1)
        <button class="lb-btn lb-prev" onclick="lbNav(-1)"><i class="fas fa-chevron-left"></i></button>
        <button class="lb-btn lb-next" onclick="lbNav(1)"><i class="fas fa-chevron-right"></i></button>
    @endif

    <div class="lightbox-img-wrap">
        <img id="lbImg" src="" alt="{{ $product->nombre }}" />
    </div>

    @if(count($allImages) > 1)
        <div class="lb-counter" id="lbCounter">1 / {{ count($allImages) }}</div>
        <div class="lb-thumbs" id="lbThumbs">
            @foreach($allImages as $i => $img)
                <div class="lb-thumb {{ $i === 0 ? 'active' : '' }}"
                     data-src="{{ $img['url'] }}"
                     data-index="{{ $i }}"
                     onclick="lbGoTo({{ $i }})">
                    <img src="{{ $img['url'] }}" alt="Foto {{ $i + 1 }}" loading="lazy" />
                </div>
            @endforeach
        </div>
    @endif
</div>
@endif

@push('js')
<script>
const galleryImages = @json(array_values(array_map(fn($img) => $img['url'], $allImages ?? [])));
let lbCurrentIndex = 0;

/* ── Thumbnail selector ── */
function selectThumb(el, index) {
    document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('mainImg').src = el.dataset.src;
    lbCurrentIndex = index;
}

/* ── Lightbox ── */
function openLightbox(index) {
    lbCurrentIndex = index;
    lbRender();
    document.getElementById('lightboxOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightboxOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

function closeLightboxOnBg(e) {
    if (e.target === document.getElementById('lightboxOverlay')) closeLightbox();
}

function lbNav(dir) {
    lbCurrentIndex = (lbCurrentIndex + dir + galleryImages.length) % galleryImages.length;
    lbRender();
}

function lbGoTo(index) {
    lbCurrentIndex = index;
    lbRender();
}

function lbRender() {
    if (!galleryImages.length) return;
    document.getElementById('lbImg').src = galleryImages[lbCurrentIndex];

    const counter = document.getElementById('lbCounter');
    if (counter) counter.textContent = (lbCurrentIndex + 1) + ' / ' + galleryImages.length;

    document.querySelectorAll('.lb-thumb').forEach((t, i) => {
        t.classList.toggle('active', i === lbCurrentIndex);
    });

    // Sync main gallery image too
    const mainImg = document.getElementById('mainImg');
    if (mainImg) mainImg.src = galleryImages[lbCurrentIndex];
    document.querySelectorAll('.thumb-item').forEach((t, i) => {
        t.classList.toggle('active', i === lbCurrentIndex);
    });
}

/* ── Keyboard navigation ── */
document.addEventListener('keydown', function (e) {
    if (!document.getElementById('lightboxOverlay')?.classList.contains('open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft')  lbNav(-1);
    if (e.key === 'ArrowRight') lbNav(1);
});

/* ── Touch swipe on lightbox ── */
(function () {
    let startX = null;
    const overlay = document.getElementById('lightboxOverlay');
    if (!overlay) return;
    overlay.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    overlay.addEventListener('touchend', e => {
        if (startX === null) return;
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) lbNav(diff > 0 ? 1 : -1);
        startX = null;
    });
})();
</script>
@endpush
@endsection

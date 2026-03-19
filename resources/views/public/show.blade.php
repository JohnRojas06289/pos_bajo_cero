@extends('layouts.public')

@section('title', 'Jacket Store | ' . $product->nombre)

@push('css')
<style>
/* ═══════════════════════════════════════
   PRODUCT PAGE LAYOUT
═══════════════════════════════════════ */
.product-page {
    padding-top: 90px;
    padding-bottom: 80px;
    min-height: 100vh;
}

/* ═══════════════════════════════════════
   GALLERY
═══════════════════════════════════════ */
.gallery-col {
    position: sticky;
    top: 80px;
}

/* Main image */
.gallery-main {
    position: relative;
    width: 100%;
    height: 540px;
    border-radius: 16px;
    overflow: hidden;
    background: #111;
    cursor: zoom-in;
}
.gallery-main img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s ease;
}
.gallery-main:hover img {
    transform: scale(1.06);
}
.gallery-main-placeholder {
    width: 100%;
    height: 540px;
    border-radius: 16px;
    background: #111;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Expand button */
.btn-gallery-expand {
    position: absolute;
    bottom: 14px;
    right: 14px;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: background 0.2s;
    z-index: 2;
    letter-spacing: 0.02em;
}
.btn-gallery-expand:hover {
    background: rgba(0,0,0,0.85);
}

/* Image counter badge */
.gallery-counter {
    position: absolute;
    top: 14px;
    left: 14px;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.8);
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 0.72rem;
    font-weight: 600;
    z-index: 2;
    letter-spacing: 0.05em;
}

/* Thumbnails */
.thumbs-row {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.thumbs-row::-webkit-scrollbar { display: none; }

.thumb {
    flex-shrink: 0;
    width: 76px;
    height: 76px;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    border: 2.5px solid transparent;
    transition: border-color 0.2s, transform 0.18s, opacity 0.2s;
    background: #111;
    opacity: 0.65;
}
.thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.thumb:hover { opacity: 1; transform: translateY(-2px); }
.thumb.active { border-color: var(--primary-color); opacity: 1; }

/* ═══════════════════════════════════════
   PRODUCT INFO
═══════════════════════════════════════ */
.product-info-col {
    padding-left: 2.5rem;
}
@media (max-width: 991px) {
    .product-info-col { padding-left: 0; margin-top: 2rem; }
    .gallery-col { position: static; }
    .gallery-main { height: 380px; }
    .gallery-main-placeholder { height: 380px; }
}
@media (max-width: 575px) {
    .gallery-main { height: 300px; }
    .gallery-main-placeholder { height: 300px; }
    .thumb { width: 60px; height: 60px; }
}

.product-brand-badge {
    display: inline-block;
    background: var(--primary-color);
    color: #000;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    padding: 4px 12px;
    border-radius: 6px;
    margin-bottom: 14px;
}
.product-cat-badge {
    display: inline-block;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.6);
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 4px 12px;
    border-radius: 6px;
    margin-bottom: 14px;
    margin-left: 6px;
}

.product-name {
    font-size: clamp(1.5rem, 3.5vw, 2.1rem);
    font-weight: 800;
    color: #fff;
    line-height: 1.2;
    margin-bottom: 0.75rem;
}

.product-price {
    font-size: 2rem;
    font-weight: 800;
    color: var(--primary-color);
    letter-spacing: -0.02em;
    margin-bottom: 1rem;
}

/* Stock pill */
.stock-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 0.78rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
}
.stock-pill.in  { background: rgba(39,174,96,.13); color: #27ae60; border: 1px solid rgba(39,174,96,.25); }
.stock-pill.out { background: rgba(231,76,60,.13); color: #e74c3c; border: 1px solid rgba(231,76,60,.25); }
.stock-pill.ask { background: rgba(255,255,255,.06); color: rgba(255,255,255,.45); border: 1px solid rgba(255,255,255,.1); }

/* Divider */
.info-divider { border-color: rgba(255,255,255,0.07); margin: 1.25rem 0; }

/* Descripción */
.product-description {
    color: rgba(255,255,255,0.55);
    font-size: 0.92rem;
    line-height: 1.75;
    margin-bottom: 1.25rem;
}

/* Atributos */
.attr-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 1.5rem;
}
.attr-item {
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 10px;
    padding: 10px 14px;
}
.attr-label {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(255,255,255,0.35);
    margin-bottom: 3px;
}
.attr-value {
    font-size: 0.88rem;
    font-weight: 600;
    color: rgba(255,255,255,0.85);
}

/* CTA Buttons */
.btn-whatsapp {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 15px;
    border-radius: 12px;
    background: #25d366;
    color: #fff;
    font-weight: 800;
    font-size: 0.95rem;
    letter-spacing: 0.04em;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.2s, transform 0.15s;
    margin-bottom: 10px;
}
.btn-whatsapp:hover { background: #1da851; transform: translateY(-1px); color: #fff; }
.btn-whatsapp:active { transform: translateY(0); }

.btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    background: transparent;
    border: 1px solid rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.5);
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    transition: border-color 0.2s, color 0.2s;
}
.btn-back:hover { border-color: rgba(255,255,255,0.3); color: rgba(255,255,255,0.8); }

/* Garantías */
.guarantees {
    display: flex;
    gap: 0;
    margin-top: 1.5rem;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px;
    overflow: hidden;
}
.guarantee-item {
    flex: 1;
    padding: 14px 8px;
    text-align: center;
    border-right: 1px solid rgba(255,255,255,0.07);
}
.guarantee-item:last-child { border-right: none; }
.guarantee-item i { font-size: 1.1rem; margin-bottom: 5px; display: block; }
.guarantee-item span { font-size: 0.65rem; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.05em; }

/* ═══════════════════════════════════════
   LIGHTBOX
═══════════════════════════════════════ */
.lb-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.96);
    z-index: 10000;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.lb-overlay.open { display: flex; }

.lb-main {
    flex: 1;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 80px 20px;
    min-height: 0;
    position: relative;
}
@media (max-width: 575px) { .lb-main { padding: 60px 16px 20px; } }

.lb-main img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 8px;
    user-select: none;
    display: block;
}

/* Nav arrows */
.lb-arrow {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
    border-radius: 50%;
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.2s;
    z-index: 10001;
}
.lb-arrow:hover { background: rgba(255,255,255,0.2); }
.lb-arrow.lb-prev { left: 16px; }
.lb-arrow.lb-next { right: 16px; }

/* Close */
.lb-close {
    position: fixed;
    top: 16px;
    right: 16px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.2s;
    z-index: 10001;
}
.lb-close:hover { background: rgba(220,50,50,0.5); }

/* Counter + thumbnails bottom bar */
.lb-bottom {
    flex-shrink: 0;
    width: 100%;
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.lb-counter-text {
    color: rgba(255,255,255,0.4);
    font-size: 0.78rem;
    letter-spacing: 0.1em;
}
.lb-thumbs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
    max-width: 100%;
    padding: 2px 4px;
}
.lb-thumbs::-webkit-scrollbar { display: none; }
.lb-thumb {
    flex-shrink: 0;
    width: 58px;
    height: 58px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    opacity: 0.55;
    transition: opacity 0.2s, border-color 0.2s;
}
.lb-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
.lb-thumb:hover { opacity: 0.85; }
.lb-thumb.active { border-color: var(--primary-color); opacity: 1; }

/* ═══════════════════════════════════════
   RELATED PRODUCTS
═══════════════════════════════════════ */
.section-related { margin-top: 80px; padding-top: 48px; border-top: 1px solid rgba(255,255,255,0.07); }
.section-related h2 { font-size: 1.3rem; font-weight: 800; color: #fff; margin-bottom: 1.5rem; letter-spacing: 0.04em; }

.related-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    display: block;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s;
}
.related-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.5);
    border-color: rgba(255,255,255,0.14);
}
.related-card-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
}
.related-card-no-img {
    width: 100%;
    height: 200px;
    background: #111;
    display: flex;
    align-items: center;
    justify-content: center;
}
.related-card-body { padding: 14px 16px; }
.related-card-name { font-weight: 700; font-size: 0.88rem; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 4px; }
.related-card-price { font-size: 0.92rem; font-weight: 800; color: var(--primary-color); }
.related-card-stock { font-size: 0.7rem; color: rgba(255,255,255,0.35); margin-top: 2px; }
</style>
@endpush

@section('content')
@php
    $allImages  = $product->todas_imagenes_urls; // [{path, url, main}]
    $totalImgs  = count($allImages);
    $mainUrl    = $totalImgs > 0 ? $allImages[0]['url'] : null;
    $stock      = $product->inventario ? $product->inventario->cantidad : null;
    $marcaNombre = optional(optional($product->marca)->caracteristica)->nombre;
    $catNombre   = optional(optional($product->categoria)->caracteristica)->nombre;
@endphp

<div class="product-page">
<div class="container px-4 px-md-5">
<div class="row gx-lg-5">

    {{-- ═══ GALERÍA ═══ --}}
    <div class="col-lg-7 gallery-col">

        @if($mainUrl)
            {{-- Imagen principal --}}
            <div class="gallery-main" id="galleryMain" onclick="lbOpen(0)" title="Clic para ampliar">
                <img id="mainImg" src="{{ $mainUrl }}" alt="{{ $product->nombre }}" />

                @if($totalImgs > 1)
                    <div class="gallery-counter">
                        <i class="fas fa-images me-1"></i>
                        <span id="mainCounter">1</span> / {{ $totalImgs }}
                    </div>
                @endif

                <button class="btn-gallery-expand" onclick="event.stopPropagation(); lbOpen(currentThumb)">
                    <i class="fas fa-expand-alt"></i> Ver ampliado
                </button>
            </div>

            {{-- Miniaturas --}}
            @if($totalImgs > 1)
                <div class="thumbs-row" id="thumbsRow">
                    @foreach($allImages as $i => $img)
                        <div class="thumb {{ $i === 0 ? 'active' : '' }}"
                             onclick="selectThumb({{ $i }})"
                             title="Foto {{ $i + 1 }}">
                            <img src="{{ $img['url'] }}" alt="Foto {{ $i + 1 }}" loading="lazy" />
                        </div>
                    @endforeach
                </div>
            @endif

        @else
            <div class="gallery-main-placeholder">
                <i class="fas fa-vest fa-4x" style="color: rgba(255,255,255,0.1);"></i>
            </div>
        @endif
    </div>

    {{-- ═══ INFO DEL PRODUCTO ═══ --}}
    <div class="col-lg-5 product-info-col">

        {{-- Badges de marca / categoría --}}
        <div>
            @if($marcaNombre)
                <span class="product-brand-badge">{{ $marcaNombre }}</span>
            @endif
            @if($catNombre)
                <span class="product-cat-badge">{{ $catNombre }}</span>
            @endif
            @if($product->genero)
                <span class="product-cat-badge">{{ $product->genero }}</span>
            @endif
        </div>

        {{-- Nombre --}}
        <h1 class="product-name">{{ $product->nombre }}</h1>

        {{-- Precio --}}
        <div class="product-price">${{ number_format($product->precio, 0) }}</div>

        {{-- Stock --}}
        @if($stock !== null)
            @if($stock > 0)
                <div class="stock-pill in">
                    <i class="fas fa-check-circle"></i>
                    En stock &mdash; {{ $stock }} disponibles
                </div>
            @else
                <div class="stock-pill out">
                    <i class="fas fa-times-circle"></i> Agotado
                </div>
            @endif
        @else
            <div class="stock-pill ask">
                <i class="fas fa-question-circle"></i> Consultar disponibilidad
            </div>
        @endif

        <hr class="info-divider">

        {{-- Descripción --}}
        @if($product->descripcion)
            <p class="product-description">{{ $product->descripcion }}</p>
        @endif

        {{-- Atributos --}}
        @php
            $attrs = [];
            if ($product->codigo)       $attrs[] = ['label' => 'Código',       'value' => $product->codigo];
            if ($product->color)        $attrs[] = ['label' => 'Color',         'value' => $product->color];
            if ($product->material)     $attrs[] = ['label' => 'Material',      'value' => $product->material];
            if ($product->presentacione) $attrs[] = ['label' => 'Talla / Unidad', 'value' => $product->presentacione->nombre];
        @endphp
        @if(count($attrs))
            <div class="attr-grid">
                @foreach($attrs as $attr)
                    <div class="attr-item">
                        <div class="attr-label">{{ $attr['label'] }}</div>
                        <div class="attr-value">{{ $attr['value'] }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- CTA --}}
        <a href="https://wa.me/573001234567?text={{ urlencode('Hola! Me interesa el producto: ' . $product->nombre . ' — Precio: $' . number_format($product->precio, 0)) }}"
           target="_blank" rel="noopener" class="btn-whatsapp">
            <i class="fab fa-whatsapp" style="font-size:1.2rem;"></i>
            CONSULTAR POR WHATSAPP
        </a>

        <a href="{{ route('collection') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Volver al catálogo
        </a>

        {{-- Garantías --}}
        <div class="guarantees">
            <div class="guarantee-item">
                <i class="fas fa-truck" style="color: var(--primary-color);"></i>
                <span>Envío Colombia</span>
            </div>
            <div class="guarantee-item">
                <i class="fas fa-shield-alt" style="color: #27ae60;"></i>
                <span>Garantía 30 días</span>
            </div>
            <div class="guarantee-item">
                <i class="fab fa-whatsapp" style="color: #25d366;"></i>
                <span>Atención directa</span>
            </div>
        </div>

    </div>
</div>

{{-- ═══ PRODUCTOS RELACIONADOS ═══ --}}
@if($relatedProducts->isNotEmpty())
<div class="section-related">
    <h2>PRODUCTOS RELACIONADOS</h2>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
        @foreach($relatedProducts as $rel)
            @php $relStock = optional($rel->inventario)->cantidad ?? 0; @endphp
            <div class="col">
                <a href="{{ route('product.show', $rel->id) }}" class="related-card">
                    @if($rel->img_path)
                        <img class="related-card-img" src="{{ $rel->image_url }}" alt="{{ $rel->nombre }}" loading="lazy" />
                    @else
                        <div class="related-card-no-img">
                            <i class="fas fa-vest fa-2x" style="color:rgba(255,255,255,0.1);"></i>
                        </div>
                    @endif
                    <div class="related-card-body">
                        <div class="related-card-name">{{ $rel->nombre }}</div>
                        <div class="related-card-price">${{ number_format($rel->precio, 0) }}</div>
                        <div class="related-card-stock">
                            @if($relStock > 0) En stock @else Agotado @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

</div>{{-- /container --}}
</div>{{-- /product-page --}}

{{-- ═══ LIGHTBOX ═══ --}}
@if($totalImgs > 0)
<div class="lb-overlay" id="lbOverlay">
    <button class="lb-close" onclick="lbClose()"><i class="fas fa-times"></i></button>

    @if($totalImgs > 1)
        <button class="lb-arrow lb-prev" onclick="lbNav(-1)"><i class="fas fa-chevron-left"></i></button>
        <button class="lb-arrow lb-next" onclick="lbNav(1)"><i class="fas fa-chevron-right"></i></button>
    @endif

    <div class="lb-main">
        <img id="lbImg" src="" alt="{{ $product->nombre }}" />
    </div>

    <div class="lb-bottom">
        <div class="lb-counter-text" id="lbCounter">1 / {{ $totalImgs }}</div>
        @if($totalImgs > 1)
            <div class="lb-thumbs" id="lbThumbs">
                @foreach($allImages as $i => $img)
                    <div class="lb-thumb {{ $i === 0 ? 'active' : '' }}"
                         onclick="lbGoTo({{ $i }})">
                        <img src="{{ $img['url'] }}" alt="Foto {{ $i + 1 }}" loading="lazy" />
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endif

@endsection

@push('js')
<script>
const IMGS = @json(array_values(array_map(fn($img) => $img['url'], $allImages)));
let currentThumb = 0;

/* ── Select thumbnail (gallery page) ── */
function selectThumb(index) {
    currentThumb = index;
    document.getElementById('mainImg').src = IMGS[index];
    document.querySelectorAll('.thumb').forEach((t, i) => t.classList.toggle('active', i === index));
    const counter = document.getElementById('mainCounter');
    if (counter) counter.textContent = index + 1;
}

/* ── Lightbox ── */
function lbOpen(index) {
    currentThumb = index;
    lbRender();
    document.getElementById('lbOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function lbClose() {
    document.getElementById('lbOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
function lbNav(dir) {
    lbGoTo((currentThumb + dir + IMGS.length) % IMGS.length);
}
function lbGoTo(index) {
    currentThumb = index;
    lbRender();
    // Sync gallery thumbnails too
    selectThumb(index);
}
function lbRender() {
    document.getElementById('lbImg').src = IMGS[currentThumb];
    const counter = document.getElementById('lbCounter');
    if (counter) counter.textContent = (currentThumb + 1) + ' / ' + IMGS.length;
    document.querySelectorAll('.lb-thumb').forEach((t, i) => t.classList.toggle('active', i === currentThumb));
    // Scroll lb-thumb into view
    const active = document.querySelector('.lb-thumb.active');
    if (active) active.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
}

/* ── Keyboard ── */
document.addEventListener('keydown', function(e) {
    if (!document.getElementById('lbOverlay')?.classList.contains('open')) return;
    if (e.key === 'Escape')      lbClose();
    if (e.key === 'ArrowLeft')   lbNav(-1);
    if (e.key === 'ArrowRight')  lbNav(1);
});

/* ── Touch swipe (lightbox) ── */
(function() {
    let startX = null;
    const el = document.getElementById('lbOverlay');
    if (!el) return;
    el.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    el.addEventListener('touchend', e => {
        if (startX === null) return;
        const dx = startX - e.changedTouches[0].clientX;
        if (Math.abs(dx) > 40) lbNav(dx > 0 ? 1 : -1);
        startX = null;
    });
    // Close on backdrop click
    el.addEventListener('click', e => { if (e.target === el) lbClose(); });
})();
</script>
@endpush

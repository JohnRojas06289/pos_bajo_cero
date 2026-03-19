@extends('layouts.public')

@section('title', 'Jacket Store | ' . $product->nombre)

@push('css')
<style>
/* ── Product page layout ── */
.product-page { padding-top: 90px; padding-bottom: 80px; min-height: 100vh; }

/* ── Sticky gallery col ── */
.gallery-col { position: sticky; top: 80px; align-self: flex-start; }
@media (max-width: 991px) { .gallery-col { position: static; } }

/* ── Main image ── */
.gallery-main {
    position: relative;
    width: 100%;
    height: 520px;
    border-radius: 16px;
    overflow: hidden;
    cursor: zoom-in;
}
.gallery-main img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s ease; }
.gallery-main:hover img { transform: scale(1.05); }
.gallery-no-img { width: 100%; height: 520px; border-radius: 16px; display: flex; align-items: center; justify-content: center; }

@media (max-width: 991px) { .gallery-main, .gallery-no-img { height: 380px; } }
@media (max-width: 575px) { .gallery-main, .gallery-no-img { height: 290px; border-radius: 12px; } }

/* Expand badge */
.btn-expand-gallery {
    position: absolute; bottom: 14px; right: 14px;
    background: rgba(0,0,0,0.55); backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.18); color: #fff;
    border-radius: 10px; padding: 7px 13px; font-size: 0.75rem;
    font-weight: 600; cursor: pointer; display: flex; align-items: center;
    gap: 6px; transition: background 0.2s; z-index: 2; letter-spacing: 0.03em;
}
.btn-expand-gallery:hover { background: rgba(0,0,0,0.8); }

/* Counter badge */
.gallery-img-counter {
    position: absolute; top: 14px; left: 14px;
    background: rgba(0,0,0,0.55); backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.12); color: rgba(255,255,255,0.85);
    border-radius: 8px; padding: 4px 10px; font-size: 0.7rem; font-weight: 600; z-index: 2;
}

/* Thumbnails */
.thumbs-row { display: flex; gap: 8px; margin-top: 10px; overflow-x: auto; scrollbar-width: none; padding-bottom: 2px; }
.thumbs-row::-webkit-scrollbar { display: none; }
@media (max-width: 575px) { .gallery-thumb-item { width: 60px; height: 60px; } }

/* ── Product info col ── */
.product-info-col { padding-left: 2.5rem; }
@media (max-width: 991px) { .product-info-col { padding-left: 0; margin-top: 2rem; } }

/* Badges row */
.badge-brand {
    display: inline-block; background: var(--primary-color); color: #000;
    font-size: 0.68rem; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.12em; padding: 4px 12px; border-radius: 6px; margin-right: 6px; margin-bottom: 12px;
}
.badge-cat {
    display: inline-block; background: var(--card-bg); border: 1px solid var(--card-border);
    color: var(--text-muted); font-size: 0.68rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: 0.1em; padding: 4px 12px; border-radius: 6px; margin-right: 6px; margin-bottom: 12px;
}

/* Price */
.price-display { font-size: 2rem; font-weight: 800; color: var(--primary-color); letter-spacing: -0.02em; margin-bottom: 1rem; line-height: 1; }

/* Attr grid */
.attr-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 1.5rem; }

/* ══════════════════════
   LIGHTBOX — blur modal
══════════════════════ */
.lb-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.72);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    padding: 24px 16px;
}
.lb-overlay.open { display: flex; }

/* Dialog box — centered, not covering 100% */
.lb-dialog {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    max-width: min(880px, 96vw);
    width: 100%;
}

/* Image container */
.lb-img-wrap {
    position: relative;
    background: rgba(0,0,0,0.3);
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    max-height: 72vh;
    width: 100%;
}
.lb-img-wrap img {
    max-width: 100%;
    max-height: 72vh;
    object-fit: contain;
    display: block;
    border-radius: 14px;
    user-select: none;
}

/* Nav arrows — over the image */
.lb-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.18);
    color: #fff;
    border-radius: 50%;
    width: 44px; height: 44px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem; cursor: pointer;
    transition: background 0.2s; z-index: 2;
}
.lb-arrow:hover { background: rgba(0,0,0,0.75); }
.lb-arrow-prev { left: 10px; }
.lb-arrow-next { right: 10px; }

/* Close button */
.lb-close {
    position: absolute;
    top: -14px; right: -14px;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.18);
    color: #fff; border-radius: 50%;
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; cursor: pointer;
    transition: background 0.2s; z-index: 3;
}
.lb-close:hover { background: rgba(200,30,30,0.7); }

/* Bottom bar */
.lb-bottom { display: flex; flex-direction: column; align-items: center; gap: 10px; width: 100%; }
.lb-counter { color: rgba(255,255,255,0.5); font-size: 0.75rem; letter-spacing: 0.1em; }
.lb-thumbs-bar { display: flex; gap: 6px; overflow-x: auto; scrollbar-width: none; padding: 2px 4px; max-width: 100%; }
.lb-thumbs-bar::-webkit-scrollbar { display: none; }
.lb-t { flex-shrink: 0; width: 54px; height: 54px; border-radius: 8px; overflow: hidden; cursor: pointer; border: 2px solid transparent; opacity: 0.55; transition: opacity 0.2s, border-color 0.2s; }
.lb-t img { width: 100%; height: 100%; object-fit: cover; display: block; }
.lb-t:hover { opacity: 0.85; }
.lb-t.active { border-color: var(--primary-color); opacity: 1; }

/* ══════════════════════
   SECTIONS BELOW
══════════════════════ */
.product-sections { margin-top: 72px; }
.prod-section { padding-top: 48px; border-top: 1px solid var(--card-border); margin-top: 48px; }
.prod-section:first-child { margin-top: 0; }
</style>
@endpush

@section('content')
@php
    $allImages   = $product->todas_imagenes_urls;
    $totalImgs   = count($allImages);
    $mainUrl     = $totalImgs > 0 ? $allImages[0]['url'] : null;
    $stock       = optional($product->inventario)->cantidad;
    $marcaNombre = optional(optional($product->marca)->caracteristica)->nombre;
    $catNombre   = optional(optional($product->categoria)->caracteristica)->nombre;
@endphp

<div class="product-page">
<div class="container px-4 px-md-5">
<div class="row gx-lg-5">

    {{-- ══ GALERÍA ══ --}}
    <div class="col-lg-7 gallery-col">
        @if($mainUrl)
            <div class="gallery-main gallery-main-bg" id="galleryMain"
                 onclick="lbOpen(currentIdx)" title="Clic para ampliar">
                <img id="mainImg" src="{{ $mainUrl }}" alt="{{ $product->nombre }}" />

                @if($totalImgs > 1)
                    <div class="gallery-img-counter">
                        <i class="fas fa-images me-1"></i>
                        <span id="mainCounterLabel">1</span>&nbsp;/&nbsp;{{ $totalImgs }}
                    </div>
                @endif

                <button class="btn-expand-gallery"
                        onclick="event.stopPropagation(); lbOpen(currentIdx)">
                    <i class="fas fa-expand-alt"></i> Ampliar
                </button>
            </div>

            @if($totalImgs > 1)
                <div class="thumbs-row" id="thumbsRow">
                    @foreach($allImages as $i => $img)
                        <div class="gallery-thumb-item {{ $i === 0 ? 'active' : '' }}"
                             onclick="selectImg({{ $i }})" title="Foto {{ $i + 1 }}">
                            <img src="{{ $img['url'] }}" alt="Foto {{ $i + 1 }}" loading="lazy" />
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="gallery-no-img gallery-main-bg">
                <i class="fas fa-vest fa-4x" style="color:var(--card-border);"></i>
            </div>
        @endif
    </div>

    {{-- ══ INFO ══ --}}
    <div class="col-lg-5 product-info-col">

        {{-- Badges --}}
        <div>
            @if($marcaNombre) <span class="badge-brand">{{ $marcaNombre }}</span> @endif
            @if($catNombre)   <span class="badge-cat">{{ $catNombre }}</span> @endif
            @if($product->genero) <span class="badge-cat">{{ $product->genero }}</span> @endif
        </div>

        {{-- Nombre --}}
        <h1 class="product-heading fw-bold mb-2" style="font-size:clamp(1.4rem,3.2vw,2rem);line-height:1.2;">
            {{ $product->nombre }}
        </h1>

        {{-- Precio --}}
        <div class="price-display">${{ number_format($product->precio, 0) }}</div>

        {{-- Stock --}}
        @if($stock !== null)
            @if($stock > 0)
                <div class="stock-pill-pub stock-pill-in">
                    <i class="fas fa-check-circle"></i> En stock &mdash; {{ $stock }} disponibles
                </div>
            @else
                <div class="stock-pill-pub stock-pill-out">
                    <i class="fas fa-times-circle"></i> Agotado
                </div>
            @endif
        @else
            <div class="stock-pill-pub stock-pill-ask">
                <i class="fas fa-question-circle"></i> Consultar disponibilidad
            </div>
        @endif

        <hr class="info-sep">

        {{-- Descripción --}}
        @if($product->descripcion)
            <p class="product-desc-text mb-4">{{ $product->descripcion }}</p>
        @endif

        {{-- Atributos --}}
        @php
            $attrs = [];
            if ($product->codigo)        $attrs[] = ['l' => 'Código',        'v' => $product->codigo];
            if ($product->color)         $attrs[] = ['l' => 'Color',         'v' => $product->color];
            if ($product->material)      $attrs[] = ['l' => 'Material',      'v' => $product->material];
            if ($product->presentacione) $attrs[] = ['l' => 'Talla / Und.',  'v' => $product->presentacione->nombre];
        @endphp
        @if(count($attrs))
            <div class="attr-grid mb-4">
                @foreach($attrs as $a)
                    <div class="attr-card">
                        <div class="attr-label">{{ $a['l'] }}</div>
                        <div class="attr-value">{{ $a['v'] }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- CTA --}}
        <a href="https://wa.me/573001234567?text={{ urlencode('Hola! Me interesa: ' . $product->nombre . ' — $' . number_format($product->precio, 0)) }}"
           target="_blank" rel="noopener" class="btn-wsp">
            <i class="fab fa-whatsapp" style="font-size:1.15rem;"></i>
            CONSULTAR POR WHATSAPP
        </a>
        <a href="{{ route('collection') }}" class="btn-back-cat">
            <i class="fas fa-arrow-left"></i> Volver al catálogo
        </a>

        {{-- Garantías --}}
        <div class="guarantee-bar">
            <div class="guarantee-bar-item">
                <i class="fas fa-truck" style="color:var(--primary-color);"></i>
                <span>Envío Colombia</span>
            </div>
            <div class="guarantee-bar-item">
                <i class="fas fa-shield-alt" style="color:#27ae60;"></i>
                <span>Garantía 30 días</span>
            </div>
            <div class="guarantee-bar-item">
                <i class="fab fa-whatsapp" style="color:#25d366;"></i>
                <span>Atención directa</span>
            </div>
        </div>

    </div>
</div>

{{-- ══ SECCIONES INFERIORES ══ --}}
<div class="product-sections">

    {{-- Productos relacionados --}}
    @if($relatedProducts->isNotEmpty())
    <div class="prod-section">
        <div class="section-title-pub">Productos relacionados</div>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
            @foreach($relatedProducts as $rel)
                @php $rs = optional($rel->inventario)->cantidad ?? 0; @endphp
                <div class="col">
                    <a href="{{ route('product.show', $rel->id) }}" class="pub-product-card">
                        @if($rel->img_path)
                            <img class="pub-product-card-img" src="{{ $rel->image_url }}" alt="{{ $rel->nombre }}" loading="lazy" />
                        @else
                            <div class="pub-product-card-noimg">
                                <i class="fas fa-vest fa-2x" style="color:var(--card-border);"></i>
                            </div>
                        @endif
                        <div class="pub-product-card-body">
                            <div class="pub-product-card-name">{{ $rel->nombre }}</div>
                            <div class="pub-product-card-price">${{ number_format($rel->precio, 0) }}</div>
                            <div class="pub-product-card-stock">{{ $rs > 0 ? 'En stock' : 'Agotado' }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Productos destacados --}}
    @if($featuredProducts->isNotEmpty())
    <div class="prod-section">
        <div class="section-title-pub">También te puede interesar</div>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
            @foreach($featuredProducts as $feat)
                @php $fs = optional($feat->inventario)->cantidad ?? 0; @endphp
                <div class="col">
                    <a href="{{ route('product.show', $feat->id) }}" class="pub-product-card">
                        @if($feat->img_path)
                            <img class="pub-product-card-img" src="{{ $feat->image_url }}" alt="{{ $feat->nombre }}" loading="lazy" />
                        @else
                            <div class="pub-product-card-noimg">
                                <i class="fas fa-vest fa-2x" style="color:var(--card-border);"></i>
                            </div>
                        @endif
                        <div class="pub-product-card-body">
                            <div class="pub-product-card-name">{{ $feat->nombre }}</div>
                            <div class="pub-product-card-price">${{ number_format($feat->precio, 0) }}</div>
                            <div class="pub-product-card-stock">{{ $fs > 0 ? 'En stock' : 'Agotado' }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>{{-- /product-sections --}}
</div>{{-- /container --}}
</div>{{-- /product-page --}}

{{-- ══ LIGHTBOX ══ --}}
@if($totalImgs > 0)
<div class="lb-overlay" id="lbOverlay" onclick="lbBgClick(event)">
    <div class="lb-dialog" id="lbDialog">
        <button class="lb-close" onclick="lbClose()" title="Cerrar (Esc)">
            <i class="fas fa-times"></i>
        </button>

        <div class="lb-img-wrap" id="lbImgWrap">
            @if($totalImgs > 1)
                <button class="lb-arrow lb-arrow-prev" onclick="event.stopPropagation(); lbNav(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="lb-arrow lb-arrow-next" onclick="event.stopPropagation(); lbNav(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @endif
            <img id="lbImg" src="" alt="{{ $product->nombre }}" onclick="event.stopPropagation()" />
        </div>

        <div class="lb-bottom">
            <div class="lb-counter" id="lbCounter">1 / {{ $totalImgs }}</div>
            @if($totalImgs > 1)
                <div class="lb-thumbs-bar" id="lbThumbsBar">
                    @foreach($allImages as $i => $img)
                        <div class="lb-t {{ $i === 0 ? 'active' : '' }}"
                             onclick="lbGoTo({{ $i }})">
                            <img src="{{ $img['url'] }}" alt="Foto {{ $i+1 }}" loading="lazy" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endif

@endsection

@push('js')
<script>
const IMGS = @json(array_values(array_map(fn($img) => $img['url'], $allImages)));
let currentIdx = 0;

function selectImg(i) {
    currentIdx = i;
    const mi = document.getElementById('mainImg');
    if (mi) mi.src = IMGS[i];
    document.querySelectorAll('.gallery-thumb-item').forEach((t,j) => t.classList.toggle('active', j===i));
    const lbl = document.getElementById('mainCounterLabel');
    if (lbl) lbl.textContent = i + 1;
}

function lbOpen(i) {
    currentIdx = i;
    lbRender();
    document.getElementById('lbOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function lbClose() {
    document.getElementById('lbOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
function lbBgClick(e) {
    // Close only when clicking the dark backdrop (not the dialog itself)
    if (e.target === document.getElementById('lbOverlay')) lbClose();
}
function lbNav(dir) { lbGoTo((currentIdx + dir + IMGS.length) % IMGS.length); }
function lbGoTo(i) {
    currentIdx = i;
    lbRender();
    selectImg(i);
}
function lbRender() {
    document.getElementById('lbImg').src = IMGS[currentIdx];
    const c = document.getElementById('lbCounter');
    if (c) c.textContent = (currentIdx + 1) + ' / ' + IMGS.length;
    document.querySelectorAll('.lb-t').forEach((t,i) => t.classList.toggle('active', i===currentIdx));
    const at = document.querySelector('.lb-t.active');
    if (at) at.scrollIntoView({ behavior:'smooth', inline:'center', block:'nearest' });
}

document.addEventListener('keydown', e => {
    if (!document.getElementById('lbOverlay')?.classList.contains('open')) return;
    if (e.key === 'Escape')     lbClose();
    if (e.key === 'ArrowLeft')  lbNav(-1);
    if (e.key === 'ArrowRight') lbNav(1);
});

// Touch swipe
(function(){
    let sx = null;
    const el = document.getElementById('lbImgWrap');
    if (!el) return;
    el.addEventListener('touchstart', e => { sx = e.touches[0].clientX; }, {passive:true});
    el.addEventListener('touchend', e => {
        if (sx === null) return;
        const dx = sx - e.changedTouches[0].clientX;
        if (Math.abs(dx) > 40) lbNav(dx > 0 ? 1 : -1);
        sx = null;
    });
})();
</script>
@endpush

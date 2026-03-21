@extends('layouts.app')

@section('title','Crear Producto')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<style>
.create-section {
    background: var(--card-bg, #fff);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1rem;
}
.section-title {
    font-size: 0.73rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted, #6b7280);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.section-title i { opacity: 0.7; }

/* Code toggle */
.code-mode-tabs { display: flex; gap: 0.375rem; margin-bottom: 0.625rem; }
.code-mode-tab {
    flex: 1;
    padding: 0.45rem 0.5rem;
    border-radius: 7px;
    border: 1.5px solid var(--border-color, #d1d5db);
    background: transparent;
    color: var(--text-secondary, #4b5563);
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    transition: all 0.15s;
    font-family: inherit;
}
.code-mode-tab:hover { border-color: #2563eb; color: #2563eb; }
.code-mode-tab.active { background: #2563eb; border-color: #2563eb; color: #fff; }

/* AI Button */
.btn-ai-gen {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.4rem 0.9rem;
    border-radius: 7px;
    border: none;
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    color: #fff;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.15s, transform 0.1s;
    font-family: inherit;
    white-space: nowrap;
}
.btn-ai-gen:hover:not(:disabled) { opacity: 0.9; transform: translateY(-1px); }
.btn-ai-gen:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

/* Image gallery */
.img-gallery {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.img-slot {
    position: relative;
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    border: 2px dashed var(--border-color, #d1d5db);
    background: var(--hover-bg, #f9fafb);
    cursor: pointer;
    transition: border-color 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
}
.img-slot:hover { border-color: #2563eb; }
.img-slot.filled { border-style: solid; border-color: #10b981; cursor: default; }
.img-slot.add-btn { border-style: dashed; cursor: pointer; flex-direction: column; gap: 0.25rem; }
.img-slot.add-btn i { font-size: 1.25rem; color: var(--text-muted, #9ca3af); }
.img-slot.add-btn span { font-size: 0.68rem; color: var(--text-muted, #9ca3af); font-weight: 600; }
.img-slot img { width: 100%; height: 100%; object-fit: cover; }
.img-slot .slot-remove {
    position: absolute; top: 4px; right: 4px;
    width: 22px; height: 22px;
    border-radius: 50%; border: none;
    background: rgba(231,76,60,0.85); color: #fff;
    font-size: 0.75rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.15s;
}
.img-slot .slot-remove:hover { background: #c0392b; }
.img-slot .slot-main-badge {
    position: absolute; bottom: 4px; left: 4px;
    background: rgba(16,185,129,0.9); color: #fff;
    font-size: 0.6rem; font-weight: 700;
    padding: 2px 6px; border-radius: 4px;
}

/* Gender radio pills */
.gender-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.gender-pill input[type="radio"] { display: none; }
.gender-pill label {
    padding: 0.35rem 0.875rem;
    border-radius: 20px;
    border: 1.5px solid var(--border-color, #d1d5db);
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    color: var(--text-secondary, #4b5563);
}
.gender-pill input:checked + label { background: #7c3aed; border-color: #7c3aed; color: #fff; }

/* Code preview box */
.code-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--hover-bg, #f3f4f6);
    border: 1.5px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    padding: 0.55rem 0.875rem;
    font-size: 0.85rem;
    color: var(--text-muted, #6b7280);
}
.code-preview strong { color: var(--text-primary, #111827); font-family: 'JetBrains Mono', monospace; }

/* AI photo hint bar */
.img-ai-hint { font-size: 0.76rem; }
.img-ai-hint-bar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(124,58,237,0.07);
    border: 1.5px dashed rgba(124,58,237,0.3);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    color: var(--text-secondary, #4b5563);
    transition: all 0.3s ease;
}
.img-ai-hint-bar.ready {
    background: rgba(16,185,129,0.08);
    border-color: rgba(16,185,129,0.4);
    color: #065f46;
}
.img-ai-hint-bar.analyzing {
    background: rgba(124,58,237,0.1);
    border-color: #7c3aed;
    border-style: solid;
}
.dot {
    display: inline-block;
    width: 9px; height: 9px;
    border-radius: 50%;
    border: 1.5px solid rgba(124,58,237,0.4);
    background: transparent;
    transition: all 0.25s ease;
}
.dot.dot-filled { background: #7c3aed; border-color: #7c3aed; }
.dot.dot-ready  { background: #10b981; border-color: #10b981; }
#imgAiDots { display: flex; gap: 4px; flex-shrink: 0; }

/* Sticky save button */
@media (min-width: 992px) {
    .sticky-save { position: sticky; top: 1rem; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-3" style="max-width: 1050px;">

    <div class="d-flex align-items-center justify-content-between mb-3 mt-1">
        <div>
            <h1 class="h4 mb-0">Crear Producto</h1>
            <nav aria-label="breadcrumb" style="font-size:0.8rem;">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                    <li class="breadcrumb-item active">Crear</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="{{ route('productos.store') }}" method="post" enctype="multipart/form-data" id="createForm">
        @csrf

        <div class="row g-3">

            {{-- ════ LEFT COLUMN ════ --}}
            <div class="col-lg-7">

                {{-- Información principal --}}
                <div class="create-section">
                    <div class="section-title"><i class="fas fa-tag"></i> Información principal</div>

                    {{-- Nombre --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del producto <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="form-control form-control-lg"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Chaqueta de Cuero Negra"
                               required autofocus>
                        @error('nombre')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- Precio + Código --}}
                    <div class="row g-3">
                        <div class="col-sm-5">
                            <label class="form-label fw-semibold">Precio de venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="precio" id="precio" class="form-control"
                                       step="1000" min="0" value="{{ old('precio') }}" placeholder="0">
                            </div>
                            @error('precio')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-sm-7">
                            <label class="form-label fw-semibold">Código de barras</label>

                            {{-- Toggle tabs --}}
                            <div class="code-mode-tabs">
                                <button type="button" class="code-mode-tab active" id="tabAuto"
                                        onclick="setCodeMode('auto')">
                                    <i class="fas fa-magic me-1"></i>Auto
                                </button>
                                <button type="button" class="code-mode-tab" id="tabManual"
                                        onclick="setCodeMode('manual')">
                                    <i class="fas fa-barcode me-1"></i>Manual / Escanear
                                </button>
                            </div>

                            {{-- Auto mode --}}
                            <div id="autoCodeWrap">
                                <div class="code-preview">
                                    <i class="fas fa-sync-alt" style="font-size:0.75rem;"></i>
                                    Se asignará automáticamente:
                                    <strong>{{ $codigoSugerido }}</strong>
                                </div>
                                {{-- Submit empty codigo → controller auto-generates --}}
                                <input type="hidden" name="codigo" id="codigoHidden" value="">
                            </div>

                            {{-- Manual mode --}}
                            <div id="manualCodeWrap" style="display:none;">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                    <input type="text" name="codigo" id="codigoManual"
                                           class="form-control"
                                           value="{{ old('codigo') }}"
                                           placeholder="Escribe o escanea el código"
                                           autocomplete="off">
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle me-1 text-primary"></i>
                                    Conecta tu lector de código de barras y escanea directamente aquí
                                </small>
                            </div>
                            @error('codigo')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </div>

                {{-- Descripción + IA --}}
                <div class="create-section">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="section-title mb-0">
                            <i class="fas fa-align-left"></i> Descripción
                        </div>
                        @if(config('services.gemini.api_key'))
                        <button type="button" class="btn-ai-gen" id="btnAI" onclick="generateDescriptionAI()">
                            <i class="fas fa-wand-magic-sparkles" id="aiIcon"></i>
                            <span id="aiLabel">Generar con IA</span>
                        </button>
                        @endif
                    </div>
                    <textarea name="descripcion" id="descripcion" rows="3" class="form-control"
                              placeholder="Describe el producto: características, materiales, uso...&#10;O usa el botón 'Generar con IA' para que Gemini la escriba automáticamente.">{{ old('descripcion') }}</textarea>
                    @error('descripcion')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                {{-- Atributos del producto (nivel base) --}}
                <div class="create-section">
                    <div class="section-title"><i class="fas fa-sliders-h"></i> Atributos</div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Material <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="material" class="form-control"
                                   value="{{ old('material') }}" placeholder="Cuero, Algodón...">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Género</label>
                            <div class="gender-pills mt-1">
                                @foreach(['Unisex', 'Hombre', 'Mujer'] as $g)
                                <div class="gender-pill">
                                    <input type="radio" name="genero" id="genero{{ $g }}" value="{{ $g }}"
                                           {{ (old('genero', 'Unisex') == $g) ? 'checked' : '' }}>
                                    <label for="genero{{ $g }}">
                                        {{ $g === 'Unisex' ? '⚧ Unisex' : ($g === 'Hombre' ? '♂ Hombre' : '♀ Mujer') }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Variantes (talla + color + stock) ────────────────── --}}
                <div class="create-section">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="section-title mb-0">
                            <i class="fas fa-layer-group"></i> Variantes
                            <span class="ms-2 badge bg-secondary" id="variantesCount">1</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addVariantRow()">
                            <i class="fas fa-plus me-1"></i> Agregar variante
                        </button>
                    </div>

                    <div class="mb-2" style="font-size:0.75rem;color:var(--text-muted);">
                        <i class="fas fa-info-circle me-1"></i>
                        Cada variante tiene su propia talla, color y stock. Si el producto es único, deja una sola fila.
                    </div>

                    {{-- Cabecera de la tabla --}}
                    <div class="row g-1 mb-1" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);padding:0 4px;">
                        <div class="col-3">Talla</div>
                        <div class="col-3">Color</div>
                        <div class="col-2">Código SKU</div>
                        <div class="col-2">Stock</div>
                        <div class="col-2"></div>
                    </div>

                    {{-- Contenedor de filas --}}
                    <div id="variantesContainer"></div>

                    @error('variantes')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                </div>

                {{-- Template de fila (oculto, clonado por JS) --}}
                <template id="variantRowTemplate">
                    <div class="variant-row row g-1 align-items-center mb-2">
                        <div class="col-3">
                            <select name="variantes[__IDX__][presentacione_id]" class="form-select form-select-sm">
                                <option value="">Sin talla</option>
                                @foreach ($presentaciones as $item)
                                <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <input type="text" name="variantes[__IDX__][color]"
                                   class="form-control form-control-sm" placeholder="Negro...">
                        </div>
                        <div class="col-2">
                            <input type="text" name="variantes[__IDX__][codigo]"
                                   class="form-control form-control-sm" placeholder="SKU-001">
                        </div>
                        <div class="col-2">
                            <input type="number" name="variantes[__IDX__][stock]"
                                   class="form-control form-control-sm" value="0" min="0" placeholder="0">
                        </div>
                        <div class="col-2 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger variant-remove-btn"
                                    onclick="removeVariantRow(this)" title="Eliminar variante">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </template>

            </div>

            {{-- ════ RIGHT COLUMN ════ --}}
            <div class="col-lg-5">

                <div class="sticky-save">

                    {{-- Imágenes --}}
                    <div class="create-section">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="section-title mb-0"><i class="fas fa-images"></i> Fotos del producto</div>
                            <small class="text-muted" style="font-size:0.72rem;">Máx. 6 fotos</small>
                        </div>

                        {{-- AI photo hint (changes dynamically) --}}
                        <div id="imgAiHint" class="img-ai-hint mb-2">
                            <div class="img-ai-hint-bar" id="imgAiBar">
                                <span id="imgAiDots">
                                    <span class="dot dot-empty"></span>
                                    <span class="dot dot-empty"></span>
                                    <span class="dot dot-empty"></span>
                                </span>
                                <span id="imgAiHintText">Sube mínimo <strong>3 fotos</strong> — la IA completará título, marca y descripción</span>
                            </div>
                        </div>

                        {{-- Gallery grid (populated by JS) --}}
                        <div class="img-gallery" id="imgGallery"></div>

                        {{-- Hidden file inputs (populated by JS before submit) --}}
                        <div id="hiddenImgInputs" style="display:none;"></div>

                        {{-- Invisible file picker --}}
                        <input type="file" id="imgPickerMain" class="d-none"
                               accept="image/png,image/jpeg,image/jpg,image/webp,image/avif,image/gif"
                               multiple onchange="handleFilePicker(this.files)">

                        @error('img_path')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- Clasificación --}}
                    <div class="create-section">
                        <div class="section-title"><i class="fas fa-folder"></i> Clasificación</div>

                        <div class="mb-3">
                            <label class="form-label">Categoría <small class="text-muted">(opcional)</small></label>
                            <select data-size="5" title="Sin categoría" data-live-search="true"
                                    name="categoria_id" id="categoria_id"
                                    class="form-control selectpicker show-tick">
                                <option value="">Ninguna</option>
                                @foreach ($categorias as $item)
                                <option value="{{ $item->id }}" {{ old('categoria_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('categoria_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div>
                            <label class="form-label">Marca <small class="text-muted">(opcional)</small></label>
                            <select data-size="5" title="Sin marca" data-live-search="true"
                                    name="marca_id" id="marca_id"
                                    class="form-control selectpicker show-tick">
                                <option value="">Ninguna</option>
                                @foreach ($marcas as $item)
                                <option value="{{ $item->id }}" {{ old('marca_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('marca_id')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>

                    {{-- Guardar --}}
                    <div class="create-section">
                        <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold" id="submitBtn">
                            <i class="fas fa-save me-2"></i> Guardar Producto
                        </button>
                        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            Cancelar
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </form>

</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
<script>
/* ─── Code mode toggle ─────────────────────────────── */
function setCodeMode(mode) {
    const autoWrap   = document.getElementById('autoCodeWrap');
    const manualWrap = document.getElementById('manualCodeWrap');
    const manualInput = document.getElementById('codigoManual');
    const tabAuto    = document.getElementById('tabAuto');
    const tabManual  = document.getElementById('tabManual');

    if (mode === 'auto') {
        autoWrap.style.display   = '';
        manualWrap.style.display = 'none';
        if (manualInput) manualInput.disabled = true;
        tabAuto.classList.add('active');
        tabManual.classList.remove('active');
    } else {
        autoWrap.style.display   = 'none';
        manualWrap.style.display = '';
        if (manualInput) {
            manualInput.disabled = false;
            manualInput.focus();
        }
        tabAuto.classList.remove('active');
        tabManual.classList.add('active');
    }
}
// Init
setCodeMode('auto');

/* ─── Gestor de variantes ─────────────────────────── */
let variantIdx = 0;

function addVariantRow(data = {}) {
    const template = document.getElementById('variantRowTemplate');
    const container = document.getElementById('variantesContainer');
    const clone = template.content.cloneNode(true);

    // Replace __IDX__ placeholder with current index
    clone.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace('__IDX__', variantIdx);
    });

    // Pre-fill values if provided (for edit mode)
    if (data.presentacione_id) {
        const sel = clone.querySelector('select');
        if (sel) sel.value = data.presentacione_id;
    }
    if (data.color) {
        const inp = clone.querySelector('input[name$="[color]"]');
        if (inp) inp.value = data.color;
    }
    if (data.codigo) {
        const inp = clone.querySelector('input[name$="[codigo]"]');
        if (inp) inp.value = data.codigo;
    }
    if (data.stock !== undefined) {
        const inp = clone.querySelector('input[name$="[stock]"]');
        if (inp) inp.value = data.stock;
    }

    container.appendChild(clone);
    variantIdx++;
    updateVariantCount();
    updateRemoveButtons();
}

function removeVariantRow(btn) {
    const row = btn.closest('.variant-row');
    if (document.querySelectorAll('.variant-row').length <= 1) return; // mínimo 1
    row.remove();
    updateVariantCount();
    updateRemoveButtons();
}

function updateVariantCount() {
    const count = document.querySelectorAll('.variant-row').length;
    const badge = document.getElementById('variantesCount');
    if (badge) badge.textContent = count;
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.variant-row');
    rows.forEach(row => {
        const btn = row.querySelector('.variant-remove-btn');
        if (btn) btn.disabled = rows.length <= 1;
    });
}

function checkVariantDuplicates() {
    const rows = document.querySelectorAll('.variant-row');
    const seen = {};
    let hasDups = false;

    rows.forEach(row => {
        const talla = row.querySelector('select')?.value ?? '';
        const color = (row.querySelector('input[name$="[color]"]')?.value ?? '').trim().toLowerCase();
        const key   = talla + '|' + color;

        if (seen[key]) {
            row.classList.add('border', 'border-danger', 'rounded');
            seen[key].classList.add('border', 'border-danger', 'rounded');
            hasDups = true;
        } else {
            seen[key] = row;
            row.classList.remove('border', 'border-danger', 'rounded');
        }
    });
    return hasDups;
}

// Escuchar cambios en talla/color para marcar duplicados en tiempo real
document.getElementById('variantesContainer').addEventListener('change', () => checkVariantDuplicates());
document.getElementById('variantesContainer').addEventListener('input',  () => checkVariantDuplicates());

// Bloquear envío si hay duplicados
document.getElementById('createForm').addEventListener('submit', function(e) {
    if (checkVariantDuplicates()) {
        e.preventDefault();
        alert('Hay variantes duplicadas (misma talla y color). Corrígelas antes de guardar.');
    }
});

// Inicializar con una fila vacía al cargar
document.addEventListener('DOMContentLoaded', () => {
    addVariantRow();
});

/* ─── Multi-image gallery ─────────────────────────── */
const MAX_IMAGES    = 6;
const MIN_AI_PHOTOS = 3;
let selectedImages  = []; // [{ file: File, previewUrl: string }]

const gallery        = document.getElementById('imgGallery');
const imgPickerMain  = document.getElementById('imgPickerMain');
const submitBtn      = document.getElementById('submitBtn');
const aiHintBar      = document.getElementById('imgAiBar');
const aiHintText     = document.getElementById('imgAiHintText');
const aiHintDots     = document.getElementById('imgAiDots');

function updateAiHint() {
    const n = selectedImages.length;
    const remaining = MIN_AI_PHOTOS - n;

    // Update dots
    aiHintDots.innerHTML = [0,1,2].map(i =>
        `<span class="dot ${n > i ? (n >= MIN_AI_PHOTOS ? 'dot-ready' : 'dot-filled') : 'dot-empty'}"></span>`
    ).join('');

    if (n === 0) {
        aiHintBar.className = 'img-ai-hint-bar';
        aiHintText.innerHTML = `Sube mínimo <strong>3 fotos</strong> — la IA completará título, marca y descripción`;
    } else if (n < MIN_AI_PHOTOS) {
        aiHintBar.className = 'img-ai-hint-bar';
        aiHintText.innerHTML = `${remaining} foto${remaining > 1 ? 's' : ''} más para activar el análisis IA`;
    } else {
        aiHintBar.className = 'img-ai-hint-bar ready';
        aiHintText.innerHTML = `<i class="fas fa-check-circle me-1"></i>¡Listo! Analizando con IA…`;
    }
}

function renderGallery() {
    gallery.innerHTML = '';

    selectedImages.forEach((img, i) => {
        const slot = document.createElement('div');
        slot.className = 'img-slot filled';
        slot.innerHTML =
            `<img src="${img.previewUrl}" alt="foto ${i + 1}">` +
            (i === 0 ? '<span class="slot-main-badge">Principal</span>' : '') +
            `<button type="button" class="slot-remove" onclick="removeImageFromGallery(${i})" title="Eliminar">` +
            `<i class="fas fa-times"></i></button>`;
        gallery.appendChild(slot);
    });

    if (selectedImages.length < MAX_IMAGES) {
        const addSlot = document.createElement('div');
        addSlot.className = 'img-slot add-btn';
        addSlot.innerHTML = '<i class="fas fa-plus"></i><span>Añadir foto</span>';
        addSlot.onclick = () => imgPickerMain.click();
        gallery.appendChild(addSlot);
    }

    updateAiHint();
}

async function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload  = e => resolve(e.target.result.split(',')[1]);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

async function compressIfNeeded(file) {
    if (file.size <= 1024 * 1024) return file;
    try {
        const compressed = await imageCompression(file, { maxSizeMB: 0.7, maxWidthOrHeight: 1280, useWebWorker: true });
        return new File([compressed], file.name, { type: file.type });
    } catch (e) { return file; }
}

async function handleFilePicker(files) {
    const prevCount = selectedImages.length;
    const remaining = MAX_IMAGES - prevCount;
    const toProcess = Array.from(files).slice(0, remaining);
    if (!toProcess.length) return;

    submitBtn.disabled  = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';

    for (const file of toProcess) {
        const processed  = await compressIfNeeded(file);
        const previewUrl = URL.createObjectURL(processed);
        selectedImages.push({ file: processed, previewUrl });
    }

    imgPickerMain.value = '';
    renderGallery();

    submitBtn.disabled  = false;
    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i> Guardar Producto';

    // Auto-trigger full AI analysis when reaching MIN_AI_PHOTOS and nombre is empty
    if (prevCount < MIN_AI_PHOTOS && selectedImages.length >= MIN_AI_PHOTOS
        && !document.getElementById('nombre').value.trim()) {
        setTimeout(generateFromImagesAI, 400);
    }
}

function removeImageFromGallery(index) {
    URL.revokeObjectURL(selectedImages[index].previewUrl);
    selectedImages.splice(index, 1);
    renderGallery();
}

// Drag & drop
gallery.addEventListener('dragover', e => {
    e.preventDefault();
    gallery.style.outline = '2px dashed #2563eb';
    gallery.style.borderRadius = '8px';
});
gallery.addEventListener('dragleave', () => { gallery.style.outline = ''; });
gallery.addEventListener('drop', e => {
    e.preventDefault();
    gallery.style.outline = '';
    if (e.dataTransfer.files.length) handleFilePicker(e.dataTransfer.files);
});

// Pre-submit: populate hidden file inputs
document.getElementById('createForm').addEventListener('submit', function () {
    const container = document.getElementById('hiddenImgInputs');
    container.innerHTML = '';
    if (selectedImages.length === 0) return;

    const mainDt = new DataTransfer();
    mainDt.items.add(selectedImages[0].file);
    const mainInput = document.createElement('input');
    mainInput.type = 'file'; mainInput.name = 'img_path'; mainInput.style.display = 'none';
    container.appendChild(mainInput);
    mainInput.files = mainDt.files;

    for (let i = 1; i < selectedImages.length; i++) {
        const dt = new DataTransfer();
        dt.items.add(selectedImages[i].file);
        const input = document.createElement('input');
        input.type = 'file'; input.name = 'imagenes_extra[]'; input.style.display = 'none';
        container.appendChild(input);
        input.files = dt.files;
    }
});

// Init
renderGallery();

/* Load a File into an HTMLImageElement */
function loadImage(file) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload  = () => { URL.revokeObjectURL(url); resolve(img); };
        img.onerror = () => { URL.revokeObjectURL(url); reject(); };
        img.src = url;
    });
}

/**
 * Combine up to 3 images into a single side-by-side canvas and return base64 JPEG.
 * Total width ≤ 900px, each tile is square-cropped to keep the collage tidy.
 * Final payload stays ≈ 100-200 KB regardless of how many photos are used.
 */
async function buildCollageForAI(files, tileSize = 300, quality = 0.75) {
    const imgs = await Promise.all(files.map(f => loadImage(f)));
    const n = imgs.length;
    const canvas = document.createElement('canvas');
    canvas.width  = tileSize * n;
    canvas.height = tileSize;
    const ctx = canvas.getContext('2d');

    imgs.forEach((img, i) => {
        // Centre-crop to square
        const side = Math.min(img.width, img.height);
        const sx   = (img.width  - side) / 2;
        const sy   = (img.height - side) / 2;
        ctx.drawImage(img, sx, sy, side, side, i * tileSize, 0, tileSize, tileSize);
    });

    return canvas.toDataURL('image/jpeg', quality).split(',')[1];
}

/* ─── AI: generar título + marca + descripción desde fotos ─── */
async function generateFromImagesAI() {
    if (selectedImages.length < MIN_AI_PHOTOS) {
        alert(`Sube al menos ${MIN_AI_PHOTOS} fotos para que la IA analice el producto.`);
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                   || document.querySelector('input[name="_token"]')?.value;

    // Show analyzing state on the hint bar
    aiHintBar.className = 'img-ai-hint-bar analyzing';
    aiHintText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Analizando imágenes con IA…';

    // Also reflect on the AI button
    const btn     = document.getElementById('btnAI');
    const aiIcon  = document.getElementById('aiIcon');
    const aiLabel = document.getElementById('aiLabel');
    if (btn) { btn.disabled = true; aiIcon.className = 'fas fa-spinner fa-spin'; aiLabel.textContent = 'Analizando…'; }

    try {
        const formData = new FormData();
        formData.append('_token', csrfToken);

        // Combine up to 3 photos into one collage — 200px tiles → ~40-80KB total payload
        const photoFiles = selectedImages.slice(0, 3).map(s => s.file);
        const collageB64 = await buildCollageForAI(photoFiles, 200, 0.70);
        formData.append('image_base64_0', collageB64);
        formData.append('image_mime_0',   'image/jpeg');

        const res  = await fetch('{{ route("productos.generate-from-images") }}', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.error) {
            aiHintBar.className = 'img-ai-hint-bar';
            aiHintText.innerHTML = `<i class="fas fa-exclamation-circle me-1 text-danger"></i>Error IA: ${data.error}`;
            return;
        }

        // Fill título
        if (data.titulo) {
            const nombreEl = document.getElementById('nombre');
            if (!nombreEl.value.trim()) nombreEl.value = data.titulo;
        }

        // Fill descripción
        if (data.descripcion) {
            document.getElementById('descripcion').value = data.descripcion;
        }

        // Match marca (case-insensitive) against existing options
        if (data.marca) {
            const marcaSelect = document.getElementById('marca_id');
            const suggested   = data.marca.toLowerCase().trim();
            let matched = false;
            for (const opt of marcaSelect.options) {
                if (opt.value && opt.text.toLowerCase().trim().includes(suggested)) {
                    $(marcaSelect).val(opt.value).selectpicker('refresh');
                    matched = true;
                    break;
                }
            }
            if (!matched && data.marca) {
                // Show suggestion but don't force it
                aiHintText.innerHTML += ` · Marca sugerida: <em>${data.marca}</em> (no está en el catálogo)`;
            }
        }

        aiHintBar.className = 'img-ai-hint-bar ready';
        aiHintText.innerHTML = '<i class="fas fa-check-circle me-1"></i>¡IA completó título, marca y descripción!';

    } catch (e) {
        console.error(e);
        aiHintBar.className = 'img-ai-hint-bar';
        aiHintText.innerHTML = '<i class="fas fa-exclamation-circle me-1 text-danger"></i>Error de conexión';
    } finally {
        if (btn) { btn.disabled = false; aiIcon.className = 'fas fa-wand-magic-sparkles'; aiLabel.textContent = 'Generar con IA'; }
    }
}

/* ─── AI: solo descripción (botón manual) ───────────── */
async function generateDescriptionAI() {
    // If 3+ photos, do the full analysis instead
    if (selectedImages.length >= MIN_AI_PHOTOS) {
        return generateFromImagesAI();
    }

    const nombre   = document.getElementById('nombre').value.trim();
    const hasImage = selectedImages.length > 0;

    if (!nombre && !hasImage) {
        alert('Ingresa el nombre del producto o sube al menos una foto.');
        document.getElementById('nombre').focus();
        return;
    }

    const btn     = document.getElementById('btnAI');
    const aiIcon  = document.getElementById('aiIcon');
    const aiLabel = document.getElementById('aiLabel');

    btn.disabled        = true;
    aiIcon.className    = 'fas fa-spinner fa-spin';
    aiLabel.textContent = 'Generando...';

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                       || document.querySelector('input[name="_token"]')?.value;

        const formData = new FormData();
        formData.append('_token',   csrfToken);
        formData.append('nombre',   nombre);
        formData.append('color',    document.getElementById('colorInput')?.value    || '');
        formData.append('material', document.getElementById('materialInput')?.value || '');
        formData.append('genero',   document.querySelector('input[name="genero"]:checked')?.value || 'Unisex');

        const catEl   = document.getElementById('categoria_id');
        const marcaEl = document.getElementById('marca_id');
        if (catEl   && catEl.value)   formData.append('categoria', catEl.options[catEl.selectedIndex]?.text   || '');
        if (marcaEl && marcaEl.value) formData.append('marca',     marcaEl.options[marcaEl.selectedIndex]?.text || '');

        if (hasImage) {
            const b64 = await fileToBase64(selectedImages[0].file);
            formData.append('image_base64', b64);
            formData.append('image_mime',   selectedImages[0].file.type || 'image/jpeg');
        }

        const res  = await fetch('{{ route("productos.generate-description") }}', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.error) alert('Error IA: ' + data.error);
        else document.getElementById('descripcion').value = data.description;

    } catch (e) {
        alert('Error de conexión al generar descripción.');
        console.error(e);
    } finally {
        btn.disabled        = false;
        aiIcon.className    = 'fas fa-wand-magic-sparkles';
        aiLabel.textContent = 'Generar con IA';
    }
}
</script>
@endpush

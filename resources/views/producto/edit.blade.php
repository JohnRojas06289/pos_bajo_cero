@extends('layouts.app')

@section('title','Editar Producto')

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
.img-slot.removing { opacity: 0.5; pointer-events: none; }
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

/* Barcode section */
.barcode-wrap {
    text-align: center;
    padding: 0.75rem;
    background: #fff;
    border-radius: 8px;
    border: 1px solid var(--border-color, #e5e7eb);
}
.barcode-wrap img { max-width: 100%; height: auto; }

@media (min-width: 992px) {
    .sticky-save { position: sticky; top: 1rem; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-3" style="max-width: 1050px;">

    <div class="d-flex align-items-center justify-content-between mb-3 mt-1">
        <div>
            <h1 class="h4 mb-0">Editar Producto</h1>
            <nav aria-label="breadcrumb" style="font-size:0.8rem;">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="{{ route('productos.update', ['producto' => $producto]) }}" method="post"
          enctype="multipart/form-data" id="editForm">
        @method('PATCH')
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
                               value="{{ old('nombre', $producto->nombre) }}"
                               placeholder="Ej: Chaqueta de Cuero Negra" required autofocus>
                        @error('nombre')<small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    {{-- Precio + Código --}}
                    <div class="row g-3">
                        <div class="col-sm-5">
                            <label class="form-label fw-semibold">Precio de venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="precio" id="precio" class="form-control"
                                       step="1000" min="0"
                                       value="{{ old('precio', $producto->precio) }}" placeholder="0">
                            </div>
                            @error('precio')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="col-sm-7">
                            <label class="form-label fw-semibold">Código de barras</label>
                            <input type="text" name="codigo" id="codigo" class="form-control"
                                   value="{{ old('codigo', $producto->codigo) }}"
                                   placeholder="Código EAN-13 o personalizado">
                            @error('codigo')<small class="text-danger">{{ $message }}</small>@enderror
                            @if($producto->codigo)
                            <div class="barcode-wrap mt-2">
                                <?php
                                try {
                                    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
                                    echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($producto->codigo, $generator::TYPE_EAN_13)) . '" alt="Código de barras">';
                                } catch (Exception $e) {
                                    echo '<small class="text-muted">Sin código de barras compatible</small>';
                                }
                                ?>
                            </div>
                            @endif
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
                              placeholder="Describe el producto: características, materiales, uso...">{{ old('descripcion', $producto->descripcion) }}</textarea>
                    @error('descripcion')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                {{-- Atributos del producto (nivel base) --}}
                <div class="create-section">
                    <div class="section-title"><i class="fas fa-sliders-h"></i> Atributos</div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Material <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="material" id="materialInput" class="form-control"
                                   value="{{ old('material', $producto->material) }}" placeholder="Cuero, Algodón...">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Género</label>
                            <div class="gender-pills mt-1">
                                @foreach(['Unisex', 'Hombre', 'Mujer'] as $g)
                                <div class="gender-pill">
                                    <input type="radio" name="genero" id="genero{{ $g }}" value="{{ $g }}"
                                           {{ (old('genero', $producto->genero ?? 'Unisex') == $g) ? 'checked' : '' }}>
                                    <label for="genero{{ $g }}">
                                        {{ $g === 'Unisex' ? '⚧ Unisex' : ($g === 'Hombre' ? '♂ Hombre' : '♀ Mujer') }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Variantes ────────────────────────────────────────── --}}
                <div class="create-section">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="section-title mb-0">
                            <i class="fas fa-layer-group"></i> Variantes
                            <span class="ms-2 badge bg-secondary" id="variantesCount">{{ $producto->variantes->count() }}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addVariantRow()">
                            <i class="fas fa-plus me-1"></i> Agregar variante
                        </button>
                    </div>

                    <div class="mb-2" style="font-size:0.75rem;color:var(--text-muted);">
                        <i class="fas fa-info-circle me-1"></i>
                        Cada variante tiene su propia talla, color y stock. La imagen por defecto es la del producto.
                    </div>

                    {{-- Cabecera --}}
                    <div class="row g-1 mb-1" style="font-size:0.72rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);padding:0 4px;">
                        <div class="col-3">Talla</div>
                        <div class="col-3">Color</div>
                        <div class="col-2">Código SKU</div>
                        <div class="col-2">Stock</div>
                        <div class="col-2"></div>
                    </div>

                    {{-- Filas de variantes existentes --}}
                    <div id="variantesContainer">
                        @foreach($producto->variantes as $vi => $variante)
                        <div class="variant-row row g-1 align-items-center mb-2">
                            <input type="hidden" name="variantes[{{ $vi }}][id]" value="{{ $variante->id }}">
                            <div class="col-3">
                                <select name="variantes[{{ $vi }}][presentacione_id]" class="form-select form-select-sm">
                                    <option value="">Sin talla</option>
                                    @foreach ($presentaciones as $item)
                                    <option value="{{ $item->id }}" {{ $variante->presentacione_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="text" name="variantes[{{ $vi }}][color]"
                                       class="form-control form-control-sm"
                                       value="{{ $variante->color }}" placeholder="Negro...">
                            </div>
                            <div class="col-2">
                                <input type="text" name="variantes[{{ $vi }}][codigo]"
                                       class="form-control form-control-sm"
                                       value="{{ $variante->codigo }}" placeholder="SKU-001">
                            </div>
                            <div class="col-2">
                                <input type="number" name="variantes[{{ $vi }}][stock]"
                                       class="form-control form-control-sm"
                                       value="{{ $variante->stock }}" min="0">
                            </div>
                            <div class="col-2 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger variant-remove-btn"
                                        onclick="removeVariantRow(this)" title="Eliminar variante"
                                        {{ $producto->variantes->count() <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @error('variantes')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                </div>

                {{-- Template para nuevas filas (clonado por JS) --}}
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
                                   class="form-control form-control-sm" value="0" min="0">
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

                        {{-- Existing images gallery (rendered by PHP → passed to JS as JSON) --}}
                        <div class="img-gallery" id="imgGallery"></div>

                        {{-- Hidden inputs for new images --}}
                        <div id="hiddenImgInputs" style="display:none;"></div>

                        <small class="text-muted d-block mt-1" style="font-size:0.72rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            La primera foto es la imagen principal. Haz clic en ✕ para eliminar fotos existentes o añade nuevas.
                        </small>

                        <input type="file" id="imgPickerNew" class="d-none"
                               accept="image/png,image/jpeg,image/jpg,image/webp,image/avif,image/gif"
                               multiple onchange="handleNewFilePicker(this.files)">

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
                                <option value="{{ $item->id }}"
                                    {{ ($producto->categoria_id == $item->id || old('categoria_id') == $item->id) ? 'selected' : '' }}>
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
                                <option value="{{ $item->id }}"
                                    {{ ($producto->marca_id == $item->id || old('marca_id') == $item->id) ? 'selected' : '' }}>
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
                            <i class="fas fa-save me-2"></i> Guardar Cambios
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
/* ─── Gestor de variantes ─────────────────────────── */
let variantIdx = {{ $producto->variantes->count() }};

function addVariantRow(data = {}) {
    const template  = document.getElementById('variantRowTemplate');
    const container = document.getElementById('variantesContainer');
    const clone = template.content.cloneNode(true);

    clone.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace('__IDX__', variantIdx);
    });
    if (data.color) {
        const inp = clone.querySelector('input[name$="[color]"]');
        if (inp) inp.value = data.color;
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
    if (document.querySelectorAll('.variant-row').length <= 1) return;
    btn.closest('.variant-row').remove();
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

/* ─── Existing images from server ─────────────────── */
// Each item: { path, url, main, isNew: false }
// New items : { file, previewUrl, isNew: true }
const MAX_IMAGES    = 6;
const removeImgUrl  = '{{ route("productos.remove-imagen", ["producto" => $producto->id]) }}';
const csrfToken     = document.querySelector('meta[name="csrf-token"]')?.content
                   || document.querySelector('input[name="_token"]')?.value;

let existingImages = @json($producto->todasImagenesUrls);
// Convert to unified format
let existingSlots = existingImages.map(img => ({
    path:       img.path,
    url:        img.url,
    main:       img.main,
    isNew:      false,
}));

let newImages = []; // { file, previewUrl }

const gallery       = document.getElementById('imgGallery');
const imgPickerNew  = document.getElementById('imgPickerNew');
const submitBtn     = document.getElementById('submitBtn');

function totalCount() { return existingSlots.length + newImages.length; }

function renderGallery() {
    gallery.innerHTML = '';
    let slotIndex = 0;

    // Existing images
    existingSlots.forEach((img, i) => {
        const slot = document.createElement('div');
        slot.className = 'img-slot filled';
        slot.id = 'slot-existing-' + i;
        const isMain = (i === 0 && newImages.length === 0) || img.main;
        slot.innerHTML =
            `<img src="${img.url}" alt="foto existente">` +
            (img.main ? '<span class="slot-main-badge">Principal</span>' : '') +
            `<button type="button" class="slot-remove" onclick="removeExistingImage(${i})" title="Eliminar">` +
            `<i class="fas fa-times"></i></button>`;
        gallery.appendChild(slot);
        slotIndex++;
    });

    // New images (pending upload)
    newImages.forEach((img, i) => {
        const slot = document.createElement('div');
        slot.className = 'img-slot filled';
        slot.innerHTML =
            `<img src="${img.previewUrl}" alt="nueva foto">` +
            (existingSlots.length === 0 && i === 0 ? '<span class="slot-main-badge">Principal</span>' : '') +
            `<span class="slot-main-badge" style="background:rgba(37,99,235,0.9);left:auto;right:4px;bottom:4px;">Nueva</span>` +
            `<button type="button" class="slot-remove" onclick="removeNewImage(${i})" title="Eliminar">` +
            `<i class="fas fa-times"></i></button>`;
        gallery.appendChild(slot);
    });

    // Add "+" button if under max
    if (totalCount() < MAX_IMAGES) {
        const addSlot = document.createElement('div');
        addSlot.className = 'img-slot add-btn';
        addSlot.innerHTML = '<i class="fas fa-plus"></i><span>Añadir foto</span>';
        addSlot.onclick = () => imgPickerNew.click();
        gallery.appendChild(addSlot);
    }
}

async function removeExistingImage(index) {
    const img  = existingSlots[index];
    const slot = document.getElementById('slot-existing-' + index);
    if (slot) slot.classList.add('removing');

    try {
        const fd = new FormData();
        fd.append('_token', csrfToken);
        fd.append('path',   img.path);

        const res  = await fetch(removeImgUrl, { method: 'POST', body: fd });
        const data = await res.json();

        if (data.success) {
            existingSlots.splice(index, 1);
            renderGallery();
        } else {
            alert('Error al eliminar la imagen: ' + (data.error || 'Inténtalo de nuevo'));
            if (slot) slot.classList.remove('removing');
        }
    } catch (e) {
        alert('Error de conexión al eliminar la imagen.');
        if (slot) slot.classList.remove('removing');
    }
}

async function compressIfNeeded(file) {
    if (file.size <= 1024 * 1024) return file;
    try {
        const compressed = await imageCompression(file, { maxSizeMB: 0.7, maxWidthOrHeight: 1280, useWebWorker: true });
        return new File([compressed], file.name, { type: file.type });
    } catch (e) { return file; }
}

async function handleNewFilePicker(files) {
    const remaining = MAX_IMAGES - totalCount();
    const toProcess = Array.from(files).slice(0, remaining);
    if (!toProcess.length) return;

    submitBtn.disabled  = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';

    for (const file of toProcess) {
        const processed  = await compressIfNeeded(file);
        const previewUrl = URL.createObjectURL(processed);
        newImages.push({ file: processed, previewUrl });
    }

    imgPickerNew.value = '';
    renderGallery();

    submitBtn.disabled  = false;
    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i> Guardar Cambios';
}

function removeNewImage(index) {
    URL.revokeObjectURL(newImages[index].previewUrl);
    newImages.splice(index, 1);
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
    if (e.dataTransfer.files.length) handleNewFilePicker(e.dataTransfer.files);
});

// Pre-submit: validate duplicates + populate hidden inputs for new images
document.getElementById('editForm').addEventListener('submit', function (e) {
    if (checkVariantDuplicates()) {
        e.preventDefault();
        alert('Hay variantes duplicadas (misma talla y color). Corrígelas antes de guardar.');
        return;
    }
    const container = document.getElementById('hiddenImgInputs');
    container.innerHTML = '';
    newImages.forEach(img => {
        const dt    = new DataTransfer();
        dt.items.add(img.file);
        const input = document.createElement('input');
        input.type  = 'file';
        input.name  = 'imagenes_nuevas[]';
        input.style.display = 'none';
        container.appendChild(input);
        input.files = dt.files;
    });
});

// Init
renderGallery();

/* ─── AI Description generator ─────────────────────── */
async function generateDescriptionAI() {
    const nombre   = document.getElementById('nombre').value.trim();
    const hasExistingImg = existingSlots.length > 0;
    const hasNewImg      = newImages.length > 0;
    const hasImage       = hasExistingImg || hasNewImg;

    if (!nombre && !hasImage) {
        alert('Ingresa el nombre del producto o sube una foto para que la IA pueda generar la descripción.');
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

        // Use new image first (if available), then fall back to existing image URL
        if (hasNewImg) {
            const imgFile = newImages[0].file;
            const b64 = await new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload  = e => resolve(e.target.result.split(',')[1]);
                reader.onerror = reject;
                reader.readAsDataURL(imgFile);
            });
            formData.append('image_base64', b64);
            formData.append('image_mime',   imgFile.type || 'image/jpeg');
        } else if (hasExistingImg) {
            // Send the URL of the first existing image so the controller can fetch it
            formData.append('image_url', existingSlots[0].url);
        }

        const res  = await fetch('{{ route("productos.generate-description") }}', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.error) {
            alert('Error IA: ' + data.error);
        } else {
            document.getElementById('descripcion').value = data.description;
        }
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

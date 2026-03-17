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

/* Drop zone */
.drop-zone {
    border: 2px dashed var(--border-color, #d1d5db);
    border-radius: 10px;
    padding: 2.25rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.18s;
    background: var(--hover-bg, #f9fafb);
    position: relative;
}
.drop-zone:hover, .drop-zone.dragover { border-color: #2563eb; background: #eff6ff; }
.drop-zone.has-image { border-style: solid; border-color: #10b981; padding: 0.5rem; background: transparent; }
.drop-zone img { max-height: 220px; border-radius: 8px; display: block; margin: 0 auto; }
.drop-zone-hint { pointer-events: none; }
.drop-zone-hint i { font-size: 2rem; color: var(--text-muted, #9ca3af); margin-bottom: 0.5rem; display: block; }
.drop-zone-hint p { font-size: 0.85rem; color: var(--text-muted, #6b7280); margin: 0 0 0.25rem; }
.drop-zone-hint small { font-size: 0.75rem; color: var(--text-muted, #9ca3af); }

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

                {{-- Atributos y tallas --}}
                <div class="create-section">
                    <div class="section-title"><i class="fas fa-sliders-h"></i> Atributos</div>

                    {{-- Modo tallas switch --}}
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="modoTallas" onchange="toggleModoTallas(this)">
                        <label class="form-check-label" for="modoTallas" style="font-size:0.88rem;font-weight:600;">
                            <i class="fas fa-layer-group me-1 text-warning"></i>
                            Crear variantes por talla (un producto por talla)
                        </label>
                    </div>

                    {{-- Single talla --}}
                    <div id="singleTallaSection">
                        <div class="row g-3 mb-3">
                            <div class="col-sm-4">
                                <label class="form-label">Talla <small class="text-muted">(opcional)</small></label>
                                <select data-size="4" title="Sin talla" data-live-search="true"
                                        name="presentacione_id" id="presentacione_id"
                                        class="form-control selectpicker show-tick">
                                    <option value="">Ninguna</option>
                                    @foreach ($presentaciones as $item)
                                    <option value="{{ $item->id }}" {{ old('presentacione_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('presentacione_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Color</label>
                                <input type="text" name="color" id="colorInput" class="form-control"
                                       value="{{ old('color') }}" placeholder="Negro, Blanco...">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label">Material</label>
                                <input type="text" name="material" id="materialInput" class="form-control"
                                       value="{{ old('material') }}" placeholder="Cuero, Tela...">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Género</label>
                            <div class="gender-pills">
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

                    {{-- Multi talla --}}
                    <div id="multiTallaSection" style="display:none;">
                        <label class="form-label fw-semibold">Selecciona las tallas a crear:</label>
                        <div class="d-flex gap-2 flex-wrap p-3 border rounded mb-2" style="background:var(--hover-bg,#f9fafb);">
                            @foreach ($presentaciones as $item)
                            <div class="form-check form-check-inline m-0">
                                <input class="form-check-input" type="checkbox" name="tallas_ids[]"
                                       id="talla_{{ $item->id }}" value="{{ $item->id }}">
                                <label class="form-check-label badge fs-6 fw-bold"
                                       for="talla_{{ $item->id }}"
                                       style="background:#f59e0b;color:#fff;cursor:pointer;padding:6px 14px;border-radius:8px;">
                                    {{ $item->nombre }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Se creará un producto por talla. Ej: "Chaqueta Negra - S", "Chaqueta Negra - M"
                        </small>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Color</label>
                                <input type="text" name="color" class="form-control"
                                       value="{{ old('color') }}" placeholder="Negro, Blanco...">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Material</label>
                                <input type="text" name="material" class="form-control"
                                       value="{{ old('material') }}" placeholder="Cuero, Tela...">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ════ RIGHT COLUMN ════ --}}
            <div class="col-lg-5">

                <div class="sticky-save">

                    {{-- Imagen --}}
                    <div class="create-section">
                        <div class="section-title"><i class="fas fa-image"></i> Imagen del producto</div>

                        <div class="drop-zone" id="dropZone" onclick="document.getElementById('img_path').click()">
                            <div class="drop-zone-hint" id="dropHint">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Arrastra o haz clic para subir</p>
                                <small>PNG, JPG, WEBP — Máx. 5 MB</small>
                            </div>
                            <img id="imgPreview" src="" alt="" style="display:none;">
                        </div>

                        <input type="file" name="img_path" id="img_path" class="d-none"
                               accept="image/png,image/jpeg,image/jpg,image/webp,image/avif,image/gif">
                        @error('img_path')<small class="text-danger">{{ $message }}</small>@enderror

                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2 w-100"
                                id="btnRemoveImg" style="display:none;" onclick="removeImage()">
                            <i class="fas fa-trash-alt me-1"></i> Quitar imagen
                        </button>
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

/* ─── Tallas mode ─────────────────────────────────── */
function toggleModoTallas(checkbox) {
    const single = document.getElementById('singleTallaSection');
    const multi  = document.getElementById('multiTallaSection');
    if (checkbox.checked) {
        single.style.display = 'none';
        document.querySelectorAll('input[name="tallas_ids[]"]').forEach(cb => cb.checked = false);
        multi.style.display  = '';
    } else {
        multi.style.display  = 'none';
        document.querySelectorAll('input[name="tallas_ids[]"]').forEach(cb => cb.checked = false);
        single.style.display = '';
    }
}

/* ─── Image drop zone ─────────────────────────────── */
const dropZone   = document.getElementById('dropZone');
const dropHint   = document.getElementById('dropHint');
const imgPreview = document.getElementById('imgPreview');
const imgInput   = document.getElementById('img_path');
const removeBtn  = document.getElementById('btnRemoveImg');
const submitBtn  = document.getElementById('submitBtn');

dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    if (e.dataTransfer.files.length) handleImageFile(e.dataTransfer.files[0]);
});

imgInput.addEventListener('change', function () {
    if (this.files && this.files[0]) handleImageFile(this.files[0]);
});

async function handleImageFile(file) {
    let processedFile = file;

    if (file.size > 1024 * 1024) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Comprimiendo...';
        try {
            const compressed = await imageCompression(file, { maxSizeMB: 0.5, maxWidthOrHeight: 1280, useWebWorker: true });
            processedFile = new File([compressed], file.name, { type: file.type });
            const dt = new DataTransfer();
            dt.items.add(processedFile);
            imgInput.files = dt.files;
        } catch (e) {
            console.error('Compression error:', e);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i> Guardar Producto';
        }
    }

    const reader = new FileReader();
    reader.onload = e => {
        imgPreview.src        = e.target.result;
        imgPreview.style.display = 'block';
        dropHint.style.display   = 'none';
        dropZone.classList.add('has-image');
        removeBtn.style.display  = '';
    };
    reader.readAsDataURL(processedFile);
}

function removeImage() {
    imgInput.value           = '';
    imgPreview.src           = '';
    imgPreview.style.display = 'none';
    dropHint.style.display   = '';
    dropZone.classList.remove('has-image');
    removeBtn.style.display  = 'none';
}

/* ─── AI Description generator ───────────────────── */
async function generateDescriptionAI() {
    const nombre = document.getElementById('nombre').value.trim();
    if (!nombre) {
        alert('Por favor ingresa el nombre del producto primero.');
        document.getElementById('nombre').focus();
        return;
    }

    const btn     = document.getElementById('btnAI');
    const aiIcon  = document.getElementById('aiIcon');
    const aiLabel = document.getElementById('aiLabel');

    btn.disabled       = true;
    aiIcon.className   = 'fas fa-spinner fa-spin';
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

        const catEl  = document.getElementById('categoria_id');
        const marcaEl = document.getElementById('marca_id');
        if (catEl  && catEl.value)  formData.append('categoria', catEl.options[catEl.selectedIndex]?.text   || '');
        if (marcaEl && marcaEl.value) formData.append('marca',  marcaEl.options[marcaEl.selectedIndex]?.text || '');

        const res  = await fetch('{{ route("productos.generate-description") }}', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.error) {
            alert('Error IA: ' + data.error);
        } else {
            document.getElementById('descripcion').value = data.description;
        }
    } catch (e) {
        alert('Error de conexión al generar descripción.');
    } finally {
        btn.disabled       = false;
        aiIcon.className   = 'fas fa-wand-magic-sparkles';
        aiLabel.textContent = 'Generar con IA';
    }
}
</script>
@endpush

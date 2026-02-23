@extends('layouts.app')

@section('title', 'Nueva Importación')

@push('css')
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
@endpush

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.template>
        <x-breadcrumb.item href="{{ route('importaciones.index') }}">Importaciones</x-breadcrumb.item>
        <x-breadcrumb.item active>Nueva</x-breadcrumb.item>
    </x-breadcrumb.template>

    <h2 class="fw-bold mb-4"><i class="fa-solid fa-plane-arrival me-2 text-primary"></i>Registrar Importación</h2>

    <form action="{{ route('importaciones.store') }}" method="POST" id="importForm">
        @csrf
        <div class="row g-4">
            <!-- Datos generales -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header fw-semibold"><i class="fa-solid fa-info-circle me-2"></i>Datos Generales</div>
                    <div class="card-body row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Proveedor Extranjero</label>
                            <select name="proveedor_id" class="form-select">
                                <option value="">— Sin proveedor asignado —</option>
                                @foreach($proveedores as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->persona->nombre ?? 'Sin nombre' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">País de Origen <span class="text-danger">*</span></label>
                            <input type="text" name="pais_origen" class="form-control @error('pais_origen') is-invalid @enderror"
                                value="{{ old('pais_origen') }}" placeholder="Ej: China, EEUU, Colombia" required>
                            @error('pais_origen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Fecha de Llegada <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_llegada" class="form-control @error('fecha_llegada') is-invalid @enderror"
                                value="{{ old('fecha_llegada', date('Y-m-d')) }}" required>
                            @error('fecha_llegada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Moneda de Compra <span class="text-danger">*</span></label>
                            <select name="moneda_costo" class="form-select" required>
                                <option value="USD" selected>USD - Dólar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="CNY">CNY - Yuan chino</option>
                                <option value="COP">COP - Peso colombiano</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">TRM (Tasa de Cambio) <span class="text-danger">*</span></label>
                            <input type="number" name="tasa_cambio" class="form-control @error('tasa_cambio') is-invalid @enderror"
                                value="{{ old('tasa_cambio', 4200) }}" min="1" step="1" required>
                            @error('tasa_cambio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Flete (COP)</label>
                            <input type="number" name="flete" class="form-control" value="{{ old('flete', 0) }}" min="0" step="1000">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Seguro (COP)</label>
                            <input type="number" name="seguro" class="form-control" value="{{ old('seguro', 0) }}" min="0" step="1000">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Arancel / Impuesto (COP)</label>
                            <input type="number" name="arancel" class="form-control" value="{{ old('arancel', 0) }}" min="0" step="1000">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Otros Gastos (COP)</label>
                            <input type="number" name="otros_gastos" class="form-control" value="{{ old('otros_gastos', 0) }}" min="0" step="1000">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas</label>
                            <textarea name="notas" class="form-control" rows="2">{{ old('notas') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold d-flex justify-content-between">
                        <span><i class="fa-solid fa-box me-2"></i>Productos Importados</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarProducto()">
                            <i class="fa-solid fa-plus me-1"></i>Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="productosContainer">
                            <!-- Filas dinámicas -->
                        </div>
                        <p class="text-muted small mt-2" id="emptyMsg">Haz clic en "Agregar" para añadir productos</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Registrar Importación</button>
            <a href="{{ route('importaciones.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
var contadorProductos = 0;
var productos = @json($productos->map(fn($p) => ['id' => $p->id, 'nombre' => $p->nombre, 'precio' => $p->precio]));

function agregarProducto() {
    document.getElementById('emptyMsg').style.display = 'none';
    const idx = contadorProductos++;
    const select = productos.map(p =>
        `<option value="${p.id}">${p.nombre} — $${p.precio?.toLocaleString('es-CO') ?? '0'}</option>`
    ).join('');

    const html = `
    <div class="border rounded p-3 mb-2 bg-light" id="fila_${idx}">
        <div class="row g-2">
            <div class="col-12">
                <select name="productos[${idx}][producto_id]" class="form-select form-select-sm" required>
                    <option value="">— Seleccionar producto —</option>
                    ${select}
                </select>
            </div>
            <div class="col-4">
                <input type="number" name="productos[${idx}][cantidad]" class="form-control form-control-sm" placeholder="Cantidad" min="1" required>
            </div>
            <div class="col-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text" id="lbl_${idx}">USD</span>
                    <input type="number" name="productos[${idx}][costo_unitario_moneda]" class="form-control" placeholder="Costo unitario" min="0" step="0.01" required>
                </div>
            </div>
            <div class="col-3">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="document.getElementById('fila_${idx}').remove()">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    </div>`;
    document.getElementById('productosContainer').insertAdjacentHTML('beforeend', html);
}

// Actualizar label moneda
document.addEventListener('change', function(e) {
    if (e.target.name === 'moneda_costo') {
        document.querySelectorAll('[id^="lbl_"]').forEach(el => el.textContent = e.target.value);
    }
});
</script>
@endpush

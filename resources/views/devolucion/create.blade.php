@extends('layouts.app')

@section('title', 'Nueva Devolución')

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.breadcrumb>
        <x-breadcrumb.item href="{{ route('devoluciones.index') }}">Devoluciones</x-breadcrumb.item>
        <x-breadcrumb.item active>Nueva</x-breadcrumb.item>
    </x-breadcrumb.breadcrumb>

    <h2 class="fw-bold mb-4"><i class="fa-solid fa-rotate-left me-2 text-warning"></i>Registrar Devolución / Cambio</h2>

    <form action="{{ route('devoluciones.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold">Datos de la Devolución</div>
                    <div class="card-body row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Venta a devolver <span class="text-danger">*</span></label>
                            <select name="venta_id" class="form-select @error('venta_id') is-invalid @enderror" required>
                                <option value="">— Seleccionar venta —</option>
                                @foreach($ventas as $v)
                                <option value="{{ $v->id }}" {{ old('venta_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->numero }} — {{ $v->cliente?->persona?->nombre ?? 'Sin cliente' }} — {{ $v->created_at->format('d/m/Y') }}
                                </option>
                                @endforeach
                            </select>
                            @error('venta_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="Devolucion" {{ old('tipo','Devolucion')=='Devolucion'?'selected':'' }}>Devolución</option>
                                <option value="Cambio" {{ old('tipo')=='Cambio'?'selected':'' }}>Cambio de Talla/Color</option>
                            </select>
                            @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Motivo <span class="text-danger">*</span></label>
                            <textarea name="motivo" class="form-control @error('motivo') is-invalid @enderror" rows="3"
                                placeholder="Ej: Talla incorrecta, defecto de fábrica, cambio de color...">{{ old('motivo') }}</textarea>
                            @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notas adicionales</label>
                            <textarea name="notas" class="form-control" rows="2">{{ old('notas') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header fw-semibold d-flex justify-content-between">
                        <span>Productos a Devolver</span>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="agregarProducto()">
                            <i class="fa-solid fa-plus me-1"></i>Agregar
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="productosContainer"></div>
                        <p class="text-muted small" id="emptyMsg">Agrega los productos que se devuelven</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-warning text-dark"><i class="fa-solid fa-save me-1"></i>Registrar Devolución</button>
            <a href="{{ route('devoluciones.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
var idx = 0;
function agregarProducto() {
    document.getElementById('emptyMsg').style.display = 'none';
    const i = idx++;
    const html = `
    <div class="border rounded p-3 mb-2 bg-light" id="fp_${i}">
        <div class="row g-2 align-items-end">
            <div class="col-5">
                <label class="small fw-semibold">Producto</label>
                <input type="text" name="productos[${i}][producto_id]" class="form-control form-control-sm"
                    placeholder="ID del producto" required>
            </div>
            <div class="col-2">
                <label class="small fw-semibold">Cant.</label>
                <input type="number" name="productos[${i}][cantidad]" class="form-control form-control-sm" min="1" value="1" required>
            </div>
            <div class="col-3">
                <label class="small fw-semibold">Precio venta</label>
                <input type="number" name="productos[${i}][precio_venta]" class="form-control form-control-sm" min="0" step="100" required>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="document.getElementById('fp_${i}').remove()">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        </div>
    </div>`;
    document.getElementById('productosContainer').insertAdjacentHTML('beforeend', html);
}
</script>
@endpush

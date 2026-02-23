@extends('layouts.app')

@section('title', 'Nueva Variante')

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.template>
        <x-breadcrumb.item href="{{ route('productos.index') }}">Productos</x-breadcrumb.item>
        <x-breadcrumb.item href="{{ route('productos.variantes.index', $producto) }}">Variantes</x-breadcrumb.item>
        <x-breadcrumb.item active>Nueva</x-breadcrumb.item>
    </x-breadcrumb.template>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-plus me-2"></i>Nueva Variante — {{ $producto->nombre }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('productos.variantes.store', $producto) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Talla</label>
                                <input type="text" name="talla" class="form-control @error('talla') is-invalid @enderror"
                                    value="{{ old('talla') }}" placeholder="Ej: S, M, L, XL, 38, 40...">
                                @error('talla')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Color</label>
                                <input type="text" name="color" class="form-control @error('color') is-invalid @enderror"
                                    value="{{ old('color') }}" placeholder="Ej: Negro, Azul, Rojo">
                                @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">SKU <small class="text-muted">(Código único opcional)</small></label>
                                <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                    value="{{ old('sku') }}" placeholder="Ej: CHQ-NIKE-M-NEG">
                                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Stock inicial <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                    value="{{ old('stock', 0) }}" min="0" required>
                                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Stock mínimo</label>
                                <input type="number" name="stock_minimo" class="form-control @error('stock_minimo') is-invalid @enderror"
                                    value="{{ old('stock_minimo', 2) }}" min="0">
                                @error('stock_minimo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Precio especial <small class="text-muted">(opcional - si vacío usa precio del producto)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio" class="form-control @error('precio') is-invalid @enderror"
                                        value="{{ old('precio') }}" min="0" step="100" placeholder="{{ $producto->precio }}">
                                </div>
                                @error('precio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i>Guardar</button>
                            <a href="{{ route('productos.variantes.index', $producto) }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

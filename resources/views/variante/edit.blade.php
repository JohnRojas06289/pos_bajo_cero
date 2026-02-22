@extends('layouts.app')

@section('title', 'Editar Variante')

@section('content')
<div class="container-fluid px-4">
    <x-breadcrumb.breadcrumb>
        <x-breadcrumb.item href="{{ route('productos.index') }}">Productos</x-breadcrumb.item>
        <x-breadcrumb.item href="{{ route('productos.variantes.index', $producto) }}">Variantes</x-breadcrumb.item>
        <x-breadcrumb.item active>Editar</x-breadcrumb.item>
    </x-breadcrumb.breadcrumb>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fa-solid fa-pen me-2"></i>Editar Variante — {{ $producto->nombre }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('productos.variantes.update', [$producto, $variante]) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Talla</label>
                                <input type="text" name="talla" class="form-control @error('talla') is-invalid @enderror"
                                    value="{{ old('talla', $variante->talla) }}" placeholder="Ej: S, M, L, XL">
                                @error('talla')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Color</label>
                                <input type="text" name="color" class="form-control @error('color') is-invalid @enderror"
                                    value="{{ old('color', $variante->color) }}" placeholder="Ej: Negro, Azul">
                                @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                    value="{{ old('sku', $variante->sku) }}">
                                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                    value="{{ old('stock', $variante->stock) }}" min="0" required>
                                @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Stock mínimo</label>
                                <input type="number" name="stock_minimo" class="form-control @error('stock_minimo') is-invalid @enderror"
                                    value="{{ old('stock_minimo', $variante->stock_minimo) }}" min="0">
                                @error('stock_minimo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Precio especial</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio" class="form-control @error('precio') is-invalid @enderror"
                                        value="{{ old('precio', $variante->precio) }}" min="0" step="100">
                                </div>
                                @error('precio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Estado</label>
                                <select name="estado" class="form-select">
                                    <option value="1" {{ $variante->estado ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ !$variante->estado ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-warning"><i class="fa-solid fa-save me-1"></i>Actualizar</button>
                            <a href="{{ route('productos.variantes.index', $producto) }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

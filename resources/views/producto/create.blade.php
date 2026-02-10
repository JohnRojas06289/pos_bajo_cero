@extends('layouts.app')

@section('title','Crear Producto')

@push('css')
<style>
    #descripcion {
        resize: none;
    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
@endpush

@section('content')
<div class="container-fluid px-2">
    <h1 class="mt-1 text-center">Crear Producto</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('productos.index')}}">Productos</a></li>
        <li class="breadcrumb-item active">Crear producto</li>
    </ol>

    <div class="card">
        <form action="{{ route('productos.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card-body text-bg-light">

                <div class="row g-4">

                    <!---Nombre---->
                    <div class="col-12">
                        <label for="nombre" class="form-label">Nombre:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre')}}">
                        @error('nombre')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                    <!---Descripción---->
                    <div class="col-12">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control">{{old('descripcion')}}</textarea>
                        @error('descripcion')
                        <small class="text-danger">{{'*'.$message}}</small>
                        @enderror
                    </div>

                </div>

                <br>

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="row g-4">

                            <!---Imagen---->
                            <div class="col-12">
                                <label for="img_path" class="form-label">Imagen:</label>
                                <input type="file" name="img_path" id="img_path" class="form-control" accept="image/*">
                                @error('img_path')
                                <small class="text-danger">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            <!----Codigo---->
                            <div class="col-12">
                                <label for="codigo" class="form-label">Código:</label>
                                <input type="text" name="codigo" id="codigo" class="form-control" 
                                       value="{{old('codigo')}}" 
                                       placeholder="Sugerido: {{ $codigoSugerido }}">
                                <small class="text-muted">Código sugerido: {{ $codigoSugerido }} (puedes cambiarlo si lo deseas)</small>
                                @error('codigo')
                                <small class="text-danger">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            <!---Marca---->
                            <div class="col-12">
                                <label for="marca_id" class="form-label">Marca:</label>
                                <select data-size="4"
                                    title="Seleccione una marca"
                                    data-live-search="true"
                                    name="marca_id"
                                    id="marca_id"
                                    class="form-control selectpicker show-tick">
                                    <option value="">No tiene marca</option>
                                    @foreach ($marcas as $item)
                                    <option value="{{$item->id}}" {{ old('marca_id') == $item->id ? 'selected' : '' }}>{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                                @error('marca_id')
                                <small class="text-danger">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            <!---Presentaciones---->
                            <div class="col-12">
                                <label for="presentacione_id" class="form-label">Presentación:</label>
                                <select data-size="4"
                                    title="Seleccione una presentación"
                                    data-live-search="true"
                                    name="presentacione_id"
                                    id="presentacione_id"
                                    class="form-control selectpicker show-tick">
                                    @foreach ($presentaciones as $item)
                                    <option value="{{$item->id}}" {{ old('presentacione_id') == $item->id ? 'selected' : '' }}>
                                        {{$item->nombre}}
                                    </option>
                                    @endforeach
                                </select>
                                @error('presentacione_id')
                                <small class="text-danger">{{'*'.$message}}</small>
                                @enderror
                            </div>

                            <!---Categorías---->
                            <div class="col-12">
                                <label for="categoria_id" class="form-label">Categoría:</label>
                                <select data-size="4"
                                    title="Seleccione la categoría"
                                    data-live-search="true"
                                    name="categoria_id"
                                    id="categoria_id"
                                    class="form-control selectpicker show-tick">
                                    <option value="">No tiene categoría</option>
                                    @foreach ($categorias as $item)
                                    <option value="{{$item->id}}" {{ old('categoria_id') == $item->id ? 'selected' : '' }}>
                                        {{$item->nombre}}
                                    </option>
                                    @endforeach
                                </select>
                                @error('categoria_id')
                                <small class="text-danger">{{'*'.$message}}</small>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <p>Imagen del producto:</p>

                        <img id="img-default"
                            class="img-fluid"
                            src="{{ asset('assets/img/paisaje.png') }}"
                            alt="Imagen por defecto">

                        <img src="" alt="Ha cargado un archivo no compatible"
                            id="img-preview"
                            class="img-fluid img-thumbnail" style="display: none;">

                    </div>

                </div>
            </div>

            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>


</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
<script>
    const inputImagen = document.getElementById('img_path');
    const imagenPreview = document.getElementById('img-preview');
    const imagenDefault = document.getElementById('img-default');
    const submitBtn = document.querySelector('button[type="submit"]');

    inputImagen.addEventListener('change', async function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            
            // Si la imagen es mayor a 1MB, comprimir
            if (file.size > 1024 * 1024) {
                // Deshabilitar botón para evitar envío antes de tiempo
                submitBtn.disabled = true;
                const originalText = submitBtn.innerText;
                submitBtn.innerText = '⏳ Comprimiendo imagen...';

                try {
                    const options = {
                        maxSizeMB: 0.5, // Objetivo: 500KB
                        maxWidthOrHeight: 1280,
                        useWebWorker: true
                    };
                    
                    const compressedFile = await imageCompression(file, options);
                    
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(new File([compressedFile], file.name, { type: file.type }));
                    inputImagen.files = dataTransfer.files;
                    
                    console.log(`Imagen comprimida: ${(compressedFile.size / 1024 / 1024).toFixed(2)} MB`);
                } catch (error) {
                    console.error('Error al comprimir:', error);
                    alert('Error al procesar la imagen. Intenta con una más pequeña.');
                } finally {
                    // Habilitar botón de nuevo
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                }
            }

            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                imagenPreview.src = e.target.result;
                imagenPreview.style.display = 'block';
                imagenDefault.style.display = 'none';
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
@endpush



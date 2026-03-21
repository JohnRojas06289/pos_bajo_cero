@extends('layouts.app')

@section('title','Realizar compra')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
<style>
    .card-custom {
        border: none;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 10px;
    }
    .card-header-custom {
        background: var(--color-primary);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        padding: 15px 20px;
    }
    .btn-add {
        background: var(--color-secondary);
        color: white;
        font-weight: bold;
    }
    .input-group-text {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <div>
            <h1>Crear Compra</h1>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('compras.index')}}">Compras</a></li>
                <li class="breadcrumb-item active">Nueva</li>
            </ol>
        </div>
        <a href="{{ route('compras.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <form action="{{ route('compras.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Left Column: Products -->
            <div class="col-lg-8">
                <div class="card card-custom mb-4">
                    <div class="card-header card-header-custom">
                        <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Detalle de Productos</h5>
                    </div>
                    <div class="card-body">
                        <!-- Add Product Form -->
                        <div class="row g-3 align-items-end mb-4 bg-light p-3 rounded">
                            <div class="col-md-5">
                                <label for="producto_id" class="form-label fw-semibold">Producto</label>
                                <select id="producto_id" class="form-control selectpicker" data-live-search="true" title="Buscar producto...">
                                    @foreach ($productos as $item)
                                    <option value="{{ $item->id }}"
                                            data-talla="{{ $item->presentacione->caracteristica->nombre ?? '' }}"
                                            data-sigla="{{ $item->presentacione->sigla ?? '' }}"
                                            data-stock="{{ $item->inventario->cantidad ?? 0 }}">
                                        {{ $item->nombre_completo }}
                                    </option>
                                    @endforeach
                                </select>
                                <div id="stockInfo" class="mt-1 small" style="display:none;"></div>
                            </div>
                            <div class="col-md-2">
                                <label for="cantidad" class="form-label fw-semibold">Cantidad</label>
                                <input type="number" id="cantidad" class="form-control" placeholder="0" min="1">
                            </div>
                            <div class="col-md-3">
                                <label for="precio_compra" class="form-label fw-semibold">Costo Unitario</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="precio_compra" class="form-control" step="0.1" placeholder="0.00" min="0">
                                </div>
                            </div>
                            <div class="col-md-3 ms-auto d-grid">
                                <button id="btn_agregar" class="btn btn-add" type="button">
                                    <i class="fas fa-plus me-1"></i> Agregar
                                </button>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="tabla_detalle" class="table table-hover align-middle mb-0">
                                <thead style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                    <tr>
                                        <th class="fw-bold text-secondary">Producto</th>
                                        <th class="fw-bold text-secondary text-center" style="width:110px;">Cantidad</th>
                                        <th class="fw-bold text-secondary text-center">Costo Unit.</th>
                                        <th class="fw-bold text-secondary text-center">Subtotal</th>
                                        <th class="fw-bold text-secondary text-center" style="width:70px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaBody">
                                    <!-- Rows added via JS -->
                                </tbody>
                                <tfoot>
                                    <tr class="table-warning">
                                        <td colspan="3" class="text-end fw-bold fs-6">TOTAL:</td>
                                        <td class="fw-bold fs-5 text-success">
                                            $ <span id="total">0</span>
                                            <input type="hidden" name="total" value="0" id="inputTotal">
                                            <input type="hidden" name="subtotal" value="0" id="inputSubtotal">
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info -->
            <div class="col-lg-4">
                <div class="card card-custom">
                    <div class="card-header card-header-custom bg-secondary">
                        <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Datos de Factura <small class="fw-normal opacity-75">(opcional)</small></h5>
                    </div>
                    <div class="card-body">

                        {{-- Adjuntar comprobante con IA --}}
                        <div class="mb-3 p-3 rounded" style="background:#f0f4ff;border:1.5px dashed #6c8ebf;">
                            <label class="form-label fw-semibold mb-1">
                                <i class="fas fa-camera me-1 text-primary"></i> Foto o archivo del comprobante
                            </label>
                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                <input type="file" name="file_comprobante" id="file_comprobante"
                                       class="form-control flex-grow-1"
                                       accept="image/*,.pdf"
                                       style="max-width:calc(100% - 130px);">
                                <button type="button" id="btn_camara" class="btn btn-outline-secondary btn-sm"
                                        title="Tomar foto directamente">
                                    <i class="fas fa-camera me-1"></i>Cámara
                                </button>
                                <input type="file" id="input_camara" accept="image/*" capture="environment"
                                       style="display:none;">
                            </div>
                            <div class="mt-2">
                                <button type="button" id="btn_ia" class="btn btn-sm btn-primary w-100" disabled>
                                    <i class="fas fa-magic me-1"></i> Rellenar campos con IA
                                </button>
                            </div>
                            <div id="ia_status" class="mt-2 small" style="display:none;"></div>
                        </div>

                        <div class="mb-3">
                            <label for="proveedore_id" class="form-label">Proveedor</label>
                            <select name="proveedore_id" id="proveedore_id" class="form-control selectpicker show-tick" data-live-search="true" title="Seleccionar...">
                                @foreach ($proveedores as $item)
                                <option value="{{$item->id}}" data-nombre="{{ strtolower($item->nombre_documento) }}">{{$item->nombre_documento}}</option>
                                @endforeach
                            </select>
                            @error('proveedore_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="fecha_hora" class="form-label">Fecha y Hora</label>
                            <input type="datetime-local" name="fecha_hora" id="fecha_hora" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                            @error('fecha_hora') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                             <label for="metodo_pago" class="form-label">Método de Pago</label>
                             <select name="metodo_pago" id="metodo_pago" class="form-control selectpicker" title="Seleccionar...">
                                 @foreach ($optionsMetodoPago as $item)
                                 <option value="{{$item->value}}">{{$item->name}}</option>
                                 @endforeach
                             </select>
                             @error('metodo_pago') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label for="comprobante_id" class="form-label">Tipo Comp.</label>
                                <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker" title="Tipo">
                                    @foreach ($comprobantes as $item)
                                    <option value="{{$item->id}}">{{$item->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="numero_comprobante" class="form-label">N° Comp.</label>
                                <input type="text" name="numero_comprobante" id="numero_comprobante" class="form-control">
                            </div>
                        </div>

                         <div class="d-grid gap-2">
                             <button type="submit" class="btn btn-primary btn-lg" id="guardar">
                                 <i class="fas fa-save me-2"></i> Registrar Compra
                             </button>
                             <button type="button" class="btn btn-outline-danger" id="cancelar" onclick="cancelarCompra()">
                                 Cancelar
                             </button>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        $('#btn_agregar').click(function() {
            agregarProducto();
        });

        // Atajo: Enter en campos de detalle agrega producto
        $('#cantidad, #precio_compra').keypress(function(e) {
            if (e.which === 13) { e.preventDefault(); agregarProducto(); }
        });

        // Mostrar info de stock al seleccionar producto
        $('#producto_id').on('changed.bs.select', function() {
            var $opt = $(this).find('option:selected');
            var stock = parseInt($opt.data('stock')) || 0;
            var sigla = $opt.data('sigla') || '';
            var $info = $('#stockInfo');
            if ($opt.val()) {
                var color = stock > 5 ? 'text-success' : (stock > 0 ? 'text-warning' : 'text-danger');
                var tallaText = sigla ? ' &nbsp;|&nbsp; <span class="badge bg-warning text-dark">' + sigla + '</span>' : '';
                $info.html('<span class="' + color + '"><i class="fas fa-boxes me-1"></i>Stock actual: <strong>' + stock + '</strong></span>' + tallaText).show();
                $('#cantidad').focus();
            } else {
                $info.hide();
            }
        });

        disableButtons();
    });

    //Variables
    let cont = 0;
    let subtotal = [];
    let sumas = 0;
    let total = 0;
    let arrayIdProductos = [];

    function cancelarCompra() {
        $('#tablaBody').empty();
        cont = 0;
        subtotal = [];
        sumas = 0;
        total = 0;
        arrayIdProductos = [];
        updateTotals();
        limpiarCampos();
        disableButtons();
    }

    function disableButtons() {
        $('#guardar').prop('disabled', total == 0);
    }

    function agregarProducto() {
        let idProducto = $('#producto_id').val();
        let $selected = $('#producto_id option:selected');
        let textProducto = $selected.text();
        let siglaProducto = $selected.data('sigla') || '';
        let stockProducto = parseInt($selected.data('stock')) || 0;
        let cantidad = parseInt($('#cantidad').val());
        let precioCompra = parseFloat($('#precio_compra').val());
            if (!idProducto || !textProducto.trim()) {
            showModal('Selecciona un producto', 'warning'); return;
        }
        if (isNaN(cantidad) || cantidad < 1) {
            showModal('La cantidad debe ser mayor a 0', 'warning'); return;
        }
        if (isNaN(precioCompra) || precioCompra <= 0) {
            showModal('El costo unitario debe ser mayor a 0', 'warning'); return;
        }
        if (arrayIdProductos.includes(idProducto)) {
            showModal('Este producto ya está en la lista. Elimínalo para modificarlo.', 'warning'); return;
        }

        // Stock warning (no block, only advisory for incoming stock)
        if (stockProducto > 0 && cantidad > stockProducto * 3) {
            showModal('Cantidad muy alta. Stock actual: ' + stockProducto, 'info');
        }

        // Extract product name (part after last ' - ')
        let nameProducto = textProducto.trim();
        try { nameProducto = textProducto.split(' - ').slice(1, -1).join(' - ').trim() || textProducto.trim(); } catch(e) {}

        subtotal[cont] = round(cantidad * precioCompra);
        sumas = round(sumas + subtotal[cont]);
        total = round(sumas);

        let tallaBadge = siglaProducto ? '<span class="badge bg-warning text-dark ms-1" style="font-size:0.7rem;">' + siglaProducto + '</span>' : '';

        let fila = '<tr id="fila' + cont + '" class="align-middle">' +
            '<td>' +
                '<input type="hidden" name="arrayidproducto[]" value="' + idProducto + '">' +
                '<span class="fw-semibold">' + nameProducto + '</span>' + tallaBadge +
            '</td>' +
            '<td class="text-center">' +
                '<input type="number" class="form-control form-control-sm text-center" style="width:85px;margin:auto;" ' +
                       'name="arraycantidad[]" ' +
                       'value="' + cantidad + '" ' +
                       'min="1" ' +
                       'onchange="recalcularFila(this, ' + cont + ', ' + precioCompra + ')" ' +
                       'onclick="this.select()">' +
            '</td>' +
            '<td class="text-center">' +
                '<input type="hidden" name="arraypreciocompra[]" value="' + precioCompra + '">' +
                '<span class="text-muted">$</span>' + precioCompra.toLocaleString() +
            '</td>' +
            '<td class="text-center fw-bold text-success">' +
                '<input type="hidden" name="arrayfechavencimiento[]" value="">' +
                '$<span id="subtotal-fila' + cont + '">' + subtotal[cont] + '</span>' +
            '</td>' +
            '<td class="text-center">' +
                '<button class="btn btn-sm btn-outline-danger" type="button" onclick="eliminarProducto(' + cont + ', \'' + idProducto + '\')">' +
                    '<i class="fas fa-trash"></i>' +
                '</button>' +
            '</td>' +
        '</tr>';

        $('#tablaBody').append(fila);
        limpiarCampos();
        cont++;
        updateTotals();
        arrayIdProductos.push(idProducto);
        disableButtons();
        // Re-focus on product selector for quick next entry
        setTimeout(function() { $('#producto_id').selectpicker('toggle'); }, 150);
    }

    function recalcularFila(input, indice, precio) {
        var nuevaCantidad = parseInt(input.value) || 1;
        if (nuevaCantidad < 1) { nuevaCantidad = 1; input.value = 1; }

        var nuevoSubtotal = round(nuevaCantidad * precio);
        sumas = round(sumas - subtotal[indice] + nuevoSubtotal);
        total = round(sumas);
        subtotal[indice] = nuevoSubtotal;

        document.getElementById('subtotal-fila' + indice).textContent = nuevoSubtotal;
        updateTotals();
        disableButtons();
    }

    function eliminarProducto(indice, idProducto) {
        sumas -= round(subtotal[indice]);
        total = round(sumas);
        $('#fila' + indice).remove();
        let index = arrayIdProductos.indexOf(idProducto.toString());
        if (index > -1) {
            arrayIdProductos.splice(index, 1);
        }
        updateTotals();
        disableButtons();
    }

    function updateTotals() {
        $('#total').html(total.toLocaleString());
        $('#inputTotal').val(total);
        $('#inputSubtotal').val(total);
    }

    function limpiarCampos() {
        $('#producto_id').selectpicker('val', '');
        $('#stockInfo').hide();
        $('#cantidad').val('');
        $('#precio_compra').val('');
    }

    function round(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0) return signo * Math.round(num);
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        num = num.toString().split('e');
        return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
    }

    function showModal(message, icon = 'error') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        Toast.fire({ icon: icon, title: message });
    }

    // ── Helpers base64 (mismo patrón que productos) ───────────────────────────
    function fileToBase64(file) {
        return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload  = function (e) { resolve(e.target.result.split(',')[1]); };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    // Comprime imagen con canvas antes de enviar (≈ 800px, 80% calidad)
    function compressImage(file, maxPx, quality) {
        maxPx   = maxPx   || 800;
        quality = quality || 0.80;
        return new Promise(function (resolve) {
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () {
                URL.revokeObjectURL(url);
                var ratio  = Math.min(1, maxPx / Math.max(img.width, img.height));
                var canvas = document.createElement('canvas');
                canvas.width  = Math.round(img.width  * ratio);
                canvas.height = Math.round(img.height * ratio);
                canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                var dataUrl = canvas.toDataURL('image/jpeg', quality);
                resolve({ base64: dataUrl.split(',')[1], mime: 'image/jpeg' });
            };
            img.onerror = function () { URL.revokeObjectURL(url); resolve(null); };
            img.src = url;
        });
    }

    // ── Cámara / archivo ──────────────────────────────────────────────────────
    $('#btn_camara').on('click', function () {
        $('#input_camara').val('').trigger('click');
    });

    // Al tomar foto con cámara, transferir al input principal
    $('#input_camara').on('change', function () {
        if (this.files && this.files[0]) {
            var dt = new DataTransfer();
            dt.items.add(this.files[0]);
            document.getElementById('file_comprobante').files = dt.files;
            $('#file_comprobante').trigger('change');
        }
    });

    // Habilitar botón IA cuando hay archivo seleccionado
    $('#file_comprobante').on('change', function () {
        var hasFile = this.files && this.files.length > 0;
        $('#btn_ia').prop('disabled', !hasFile);
        if (!hasFile) $('#ia_status').hide();
    });

    // ── Extracción con IA ─────────────────────────────────────────────────────
    $('#btn_ia').on('click', async function () {
        var fileInput = document.getElementById('file_comprobante');
        if (!fileInput.files || !fileInput.files[0]) return;

        var file = fileInput.files[0];
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Analizando...');
        $('#ia_status').html('<span class="text-info"><i class="fas fa-circle-notch fa-spin me-1"></i> Procesando imagen...</span>').show();

        var b64, mime;
        try {
            if (file.type === 'application/pdf') {
                // PDF: enviar directo en base64
                b64  = await fileToBase64(file);
                mime = 'application/pdf';
            } else {
                // Imagen: comprimir antes de enviar (igual que en productos)
                var compressed = await compressImage(file, 800, 0.80);
                b64  = compressed.base64;
                mime = compressed.mime;
            }
        } catch (e) {
            $('#ia_status').html('<span class="text-danger"><i class="fas fa-times-circle me-1"></i> Error al procesar el archivo.</span>');
            $btn.prop('disabled', false).html('<i class="fas fa-magic me-1"></i> Rellenar campos con IA');
            return;
        }

        var formData = new FormData();
        formData.append('_token',       '{{ csrf_token() }}');
        formData.append('image_base64', b64);
        formData.append('image_mime',   mime);

        $.ajax({
            url: '{{ route("compras.extract-factura") }}',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.error) {
                    $('#ia_status').html('<span class="text-danger"><i class="fas fa-times-circle me-1"></i>' + data.error + '</span>');
                    $btn.prop('disabled', false).html('<i class="fas fa-magic me-1"></i> Rellenar campos con IA');
                    return;
                }

                var filled = [];

                if (data.proveedore_id) {
                    $('#proveedore_id').selectpicker('val', String(data.proveedore_id));
                    $('#proveedore_id').selectpicker('refresh');
                    filled.push('Proveedor');
                } else if (data.proveedor_nombre) {
                    var needle = data.proveedor_nombre.toLowerCase();
                    $('#proveedore_id option').each(function () {
                        var opt = $(this).data('nombre') || '';
                        if (opt && opt.indexOf(needle) !== -1) {
                            $('#proveedore_id').selectpicker('val', $(this).val());
                            $('#proveedore_id').selectpicker('refresh');
                            filled.push('Proveedor');
                            return false;
                        }
                    });
                }

                if (data.fecha_hora) {
                    $('#fecha_hora').val(data.fecha_hora);
                    filled.push('Fecha');
                }

                if (data.metodo_pago) {
                    $('#metodo_pago').selectpicker('val', data.metodo_pago.toUpperCase());
                    $('#metodo_pago').selectpicker('refresh');
                    filled.push('Método de pago');
                }

                if (data.numero_comprobante) {
                    $('#numero_comprobante').val(data.numero_comprobante);
                    filled.push('N° comprobante');
                }

                if (filled.length > 0) {
                    $('#ia_status').html('<span class="text-success"><i class="fas fa-check-circle me-1"></i> Completado: ' + filled.join(', ') + '</span>');
                } else {
                    $('#ia_status').html('<span class="text-warning"><i class="fas fa-exclamation-circle me-1"></i> No se encontraron datos legibles.</span>');
                }

                $btn.prop('disabled', false).html('<i class="fas fa-magic me-1"></i> Rellenar campos con IA');
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Error al procesar el archivo.';
                $('#ia_status').html('<span class="text-danger"><i class="fas fa-times-circle me-1"></i>' + msg + '</span>');
                $btn.prop('disabled', false).html('<i class="fas fa-magic me-1"></i> Rellenar campos con IA');
            }
        });
    });
</script>
@endpush



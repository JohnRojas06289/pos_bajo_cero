@extends('layouts.app')

@section('title','Tallas')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" type="text/css">
@endpush
@push('css')
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
@endpush

@section('content')

<div class="container-fluid px-2 py-3">

    <!-- Page Header -->
    <div class="section-header">
        <h1><i class="fas fa-ruler-combined"></i> Tallas</h1>
        @can('crear-presentacione')
        <a href="{{ route('presentaciones.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Nueva Talla
        </a>
        @endcan
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Tallas</li>
    </ol>

    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-ruler text-primary"></i>
            <span>Lista de tallas</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover mb-0 fs-6">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Sigla</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($presentaciones as $item)
                        <tr>
                            <td>
                                <span class="fw-semibold text-dark" style="font-size:0.85rem;">{{ $item->caracteristica->nombre }}</span>
                            </td>
                            <td>
                                <span class="badge" style="background:#fef3c7;color:#92400e;font-weight:700;font-size:0.78rem;">{{ $item->sigla }}</span>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size:0.83rem;">{{ $item->caracteristica->descripcion }}</span>
                            </td>
                            <td>
                                @if($item->caracteristica->estado)
                                    <span class="badge" style="background:#d1fae5;color:#065f46;">Activo</span>
                                @else
                                    <span class="badge" style="background:#fee2e2;color:#991b1b;">Eliminado</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions justify-content-end">
                                    @can('editar-presentacione')
                                    <a href="{{ route('presentaciones.edit', ['presentacione' => $item]) }}"
                                       class="btn-icon-sm btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan

                                    @can('eliminar-presentacione')
                                    <button title="{{ $item->caracteristica->estado == 1 ? 'Eliminar' : 'Restaurar' }}"
                                            class="btn-icon-sm {{ $item->caracteristica->estado == 1 ? 'btn-delete' : '' }}"
                                            style="{{ $item->caracteristica->estado != 1 ? 'color:#059669;border-color:var(--border-color);' : '' }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmModal-{{ $item->id }}">
                                        <i class="fas {{ $item->caracteristica->estado == 1 ? 'fa-trash' : 'fa-rotate' }}"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Confirm -->
                        <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-{{ $item->caracteristica->estado == 1 ? 'exclamation-triangle text-danger' : 'undo text-success' }} me-2"></i>
                                            {{ $item->caracteristica->estado == 1 ? 'Eliminar talla' : 'Restaurar talla' }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-0">
                                            {{ $item->caracteristica->estado == 1 ? '¿Seguro que quieres eliminar la talla' : '¿Seguro que quieres restaurar la talla' }}
                                            <strong>{{ $item->caracteristica->nombre }}</strong>?
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <form action="{{ route('presentaciones.destroy', ['presentacione' => $item->id]) }}" method="post">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $item->caracteristica->estado == 1 ? 'btn-danger' : 'btn-success' }}">
                                                <i class="fas {{ $item->caracteristica->estado == 1 ? 'fa-trash' : 'fa-rotate' }} me-1"></i>
                                                Confirmar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection

@push('js')
<script src="{{ asset('js/simple-datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush

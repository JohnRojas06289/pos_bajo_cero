@extends('layouts.app')

@section('title','categorías')

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
        <h1><i class="fas fa-tag"></i> Categorías</h1>
        @can('crear-categoria')
        <a href="{{ route('categorias.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Nueva Categoría
        </a>
        @endcan
    </div>

    <x-breadcrumb.template>
        <x-breadcrumb.item :href="route('panel')" content="Inicio" />
        <x-breadcrumb.item active='true' content="Categorías" />
    </x-breadcrumb.template>

    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-tags text-primary"></i>
            <span>Lista de categorías</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
            <table id="datatablesSimple" class="table table-hover mb-0 fs-6">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorias as $categoria)
                    <tr>
                        <td>
                            {{$categoria->caracteristica->nombre}}
                        </td>
                        <td>
                            {{$categoria->caracteristica->descripcion}}
                        </td>
                        <td>
                            @if($categoria->caracteristica->estado)
                                <span class="badge" style="background:#d1fae5;color:#065f46;border-radius:6px;">Activo</span>
                            @else
                                <span class="badge" style="background:#fee2e2;color:#991b1b;border-radius:6px;">Eliminado</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions justify-content-end">
                                @can('editar-categoria')
                                <a href="{{ route('categorias.edit', ['categoria' => $categoria]) }}"
                                   class="btn-icon-sm btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('eliminar-categoria')
                                <button title="{{ $categoria->caracteristica->estado == 1 ? 'Eliminar' : 'Restaurar' }}"
                                        class="btn-icon-sm {{ $categoria->caracteristica->estado == 1 ? 'btn-delete' : '' }}"
                                        style="{{ $categoria->caracteristica->estado != 1 ? 'color:#059669;border-color:var(--border-color);' : '' }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal-{{ $categoria->id }}">
                                    <i class="fas {{ $categoria->caracteristica->estado == 1 ? 'fa-trash' : 'fa-rotate' }}"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Confirm -->
                    <div class="modal fade" id="confirmModal-{{ $categoria->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-{{ $categoria->caracteristica->estado == 1 ? 'exclamation-triangle text-danger' : 'undo text-success' }} me-2"></i>
                                        {{ $categoria->caracteristica->estado == 1 ? 'Eliminar categoría' : 'Restaurar categoría' }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">
                                        {{ $categoria->caracteristica->estado == 1 ? '¿Seguro que quieres eliminar la categoría' : '¿Seguro que quieres restaurar la categoría' }}
                                        <strong>{{ $categoria->caracteristica->nombre }}</strong>?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <form action="{{ route('categorias.destroy', ['categoria' => $categoria->id]) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $categoria->caracteristica->estado == 1 ? 'btn-danger' : 'btn-success' }}">
                                            <i class="fas {{ $categoria->caracteristica->estado == 1 ? 'fa-trash' : 'fa-rotate' }} me-1"></i>
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



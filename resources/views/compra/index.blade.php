@extends('layouts.app')

@section('title','Compras')

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
        <h1><i class="fas fa-store"></i> Compras</h1>
        @can('crear-compra')
        <a href="{{ route('compras.create') }}" class="btn-add">
            <i class="fas fa-plus"></i> Nueva Compra
        </a>
        @endcan
    </div>

    <!-- Breadcrumb -->
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Compras</li>
    </ol>

    <!-- Table Card -->
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fas fa-boxes text-primary"></i>
            <span>Historial de compras</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="datatablesSimple" class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Proveedor</th>
                            <th>Fecha y hora</th>
                            <th>Usuario</th>
                            <th>Total</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compras as $item)
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size:0.85rem;">{{ $item->comprobante?->nombre ?? 'N/A' }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $item->numero_comprobante }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="font-size:0.85rem;">
                                    {{ ucfirst($item->proveedore?->persona?->tipo?->value ?? 'N/A') }}
                                </div>
                                <div class="text-muted" style="font-size:0.75rem;">{{ $item->proveedore?->persona?->razon_social ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <div style="font-size:0.82rem;">
                                    <span class="text-muted me-1"><i class="fa-solid fa-calendar-days"></i></span>
                                    <span class="fw-medium">{{ $item->fecha }}</span>
                                </div>
                                <div style="font-size:0.78rem;margin-top:2px;">
                                    <span class="text-muted me-1"><i class="fa-solid fa-clock"></i></span>
                                    <span class="text-muted">{{ $item->hora }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-size:0.85rem;">{{ $item->user?->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-primary" style="font-size:0.9rem;">
                                    ${{ number_format($item->total, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions justify-content-end">
                                    @can('mostrar-compra')
                                    <form action="{{ route('compras.show', ['compra' => $item]) }}" method="get">
                                        <button type="submit" class="btn-icon-sm btn-view" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </form>
                                    @endcan

                                    <button type="button"
                                            class="btn-icon-sm"
                                            style="color:#8b5cf6;border-color:var(--border-color);"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verPDFModal-{{ $item->id }}"
                                            title="Ver PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal PDF -->
                        <div class="modal fade" id="verPDFModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                            PDF — Compra #{{ $item->numero_comprobante }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-0">
                                        @if ($item->comprobante_path)
                                        <iframe src="{{ asset($item->comprobante_path) }}"
                                                style="width:100%;height:550px;border:none;"></iframe>
                                        @else
                                        <div class="text-center text-muted py-5">
                                            <i class="fas fa-file-slash fa-3x mb-3 opacity-25"></i>
                                            <p>No se ha cargado un comprobante para esta compra</p>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-1"></i>Cerrar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-center">
            {{ $compras->links() }}
        </div>
    </div>

</div>

@endsection

@push('js')
<script src="{{ asset('js/simple-datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
@endpush

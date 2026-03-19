@extends('layouts.app')

@section('title', 'Reservas')

@push('css')
<style>
.reserva-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1rem;
    transition: box-shadow 0.2s;
}
.reserva-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.12); }

.reserva-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-secondary);
    flex-wrap: wrap;
    gap: 0.5rem;
}

.reserva-name {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-primary);
}

.reserva-phone {
    font-size: 0.82rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.reserva-body {
    padding: 1rem 1.25rem;
}

.reserva-productos {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin-bottom: 0.75rem;
}

.reserva-producto-badge {
    background: var(--hover-bg);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 3px 10px;
    font-size: 0.78rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.reserva-total {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--accent);
}

.reserva-notas {
    font-size: 0.82rem;
    color: var(--text-muted);
    font-style: italic;
    margin-top: 0.4rem;
}

.reserva-footer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-top: 1px solid var(--border-color);
    background: var(--bg-secondary);
    flex-wrap: wrap;
}

.reserva-date {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-left: auto;
}

/* Pending count badge */
.pending-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(243,156,18,0.15);
    border: 1px solid rgba(243,156,18,0.3);
    color: #f39c12;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
}

/* Filter strip */
.filter-strip {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.filter-strip .btn-filter {
    padding: 6px 14px;
    border-radius: 999px;
    border: 1.5px solid var(--border-color);
    background: var(--card-bg);
    color: var(--text-secondary);
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: border-color 0.2s, color 0.2s, background 0.2s;
}
.filter-strip .btn-filter:hover,
.filter-strip .btn-filter.active {
    border-color: var(--accent);
    color: var(--accent);
    background: rgba(29,150,200,0.08);
}
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Page header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-0" style="font-weight:800;">
                <i class="fas fa-calendar-check me-2" style="color:var(--accent);"></i>
                Reservas
                @if($pendientesCount > 0)
                    <span class="pending-badge ms-2">{{ $pendientesCount }} pendiente{{ $pendientesCount > 1 ? 's' : '' }}</span>
                @endif
            </h4>
            <p class="mb-0 mt-1" style="font-size:0.82rem;color:var(--text-muted);">
                Solicitudes de reserva recibidas desde la tienda
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-strip">
        <a href="{{ route('reservas.index') }}" class="btn-filter {{ !request('estado') ? 'active' : '' }}">
            <i class="fas fa-list me-1"></i> Todas
        </a>
        <a href="{{ route('reservas.index', ['estado' => 'pendiente']) }}" class="btn-filter {{ request('estado') === 'pendiente' ? 'active' : '' }}">
            <i class="fas fa-clock me-1"></i> Pendientes
        </a>
        <a href="{{ route('reservas.index', ['estado' => 'contactado']) }}" class="btn-filter {{ request('estado') === 'contactado' ? 'active' : '' }}">
            <i class="fas fa-phone me-1"></i> Contactados
        </a>
        <a href="{{ route('reservas.index', ['estado' => 'confirmada']) }}" class="btn-filter {{ request('estado') === 'confirmada' ? 'active' : '' }}">
            <i class="fas fa-check me-1"></i> Confirmadas
        </a>
        <a href="{{ route('reservas.index', ['estado' => 'cancelada']) }}" class="btn-filter {{ request('estado') === 'cancelada' ? 'active' : '' }}">
            <i class="fas fa-times me-1"></i> Canceladas
        </a>

        <!-- Search -->
        <form action="{{ route('reservas.index') }}" method="GET" class="ms-auto d-flex gap-2">
            @if(request('estado'))<input type="hidden" name="estado" value="{{ request('estado') }}">@endif
            <input type="text" name="search" class="form-control form-control-sm" style="width:200px;"
                   placeholder="Buscar nombre o teléfono…" value="{{ request('search') }}">
            <button class="btn btn-sm btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @forelse($reservas as $reserva)
        <div class="reserva-card">
            <div class="reserva-header">
                <div>
                    <div class="reserva-name">
                        <i class="fas fa-user me-1" style="color:var(--text-muted);font-size:0.85rem;"></i>
                        {{ $reserva->nombre }}
                    </div>
                    <div class="reserva-phone">
                        <i class="fas fa-phone"></i>{{ $reserva->telefono }}
                        @if($reserva->email)
                            <span class="ms-2"><i class="fas fa-envelope me-1"></i>{{ $reserva->email }}</span>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-{{ $reserva->estado_badge }}">{{ $reserva->estado_label }}</span>
                </div>
            </div>

            <div class="reserva-body">
                <div class="reserva-productos">
                    @foreach($reserva->productos as $p)
                        <span class="reserva-producto-badge">
                            {{ $p['nombre'] }} ×{{ $p['cantidad'] }}
                            <span style="color:var(--accent);font-weight:700;"> ${{ number_format($p['subtotal'], 0) }}</span>
                        </span>
                    @endforeach
                </div>
                <div class="reserva-total">${{ number_format($reserva->total, 0) }} COP</div>
                @if($reserva->notas)
                    <div class="reserva-notas"><i class="fas fa-comment me-1"></i>{{ $reserva->notas }}</div>
                @endif
            </div>

            <div class="reserva-footer">
                {{-- WhatsApp button --}}
                @php
                    $numero = env('WHATSAPP_NUMERO', '573001234567');
                    $msg    = "Hola {$reserva->nombre}, te escribimos de Bajo Cero sobre tu reserva de: " .
                              collect($reserva->productos)->pluck('nombre')->join(', ') . ".";
                    $urlWsp = "https://wa.me/{$numero}?text=" . urlencode($msg);
                @endphp
                <a href="{{ $urlWsp }}" target="_blank" class="btn btn-success btn-sm">
                    <i class="fab fa-whatsapp me-1"></i> WhatsApp
                </a>

                {{-- Change estado --}}
                <form action="{{ route('reservas.estado', $reserva) }}" method="POST" class="d-flex gap-2 align-items-center">
                    @csrf @method('PATCH')
                    <select name="estado" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                        <option value="pendiente"  {{ $reserva->estado === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                        <option value="contactado" {{ $reserva->estado === 'contactado' ? 'selected' : '' }}>Contactado</option>
                        <option value="confirmada" {{ $reserva->estado === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="cancelada"  {{ $reserva->estado === 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </form>

                <span class="reserva-date">
                    <i class="fas fa-clock me-1"></i>{{ $reserva->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
    @empty
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="fas fa-calendar-times fa-3x mb-3" style="color:var(--text-muted);"></i>
                <h5 style="color:var(--text-primary);">No hay reservas</h5>
                <p style="color:var(--text-muted);">Aún no se han recibido solicitudes de reserva.</p>
            </div>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $reservas->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection

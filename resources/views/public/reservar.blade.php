@extends('layouts.public')

@section('title', 'Bajo Cero | Reservar Productos')

@push('css')
<style>
.reservar-hero {
    padding: 6rem 0 3rem;
    text-align: center;
    background: linear-gradient(180deg, var(--secondary-color) 0%, var(--accent-color) 100%);
    border-bottom: 1px solid var(--card-border);
}
.reservar-hero h1 { color: var(--text-color); font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; }
.reservar-hero p  { color: var(--text-muted); font-size: 1rem; }

.reservar-section {
    padding: 3rem 0 5rem;
    background: var(--secondary-color);
}

.form-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--card-shadow);
}

.form-card-title {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: var(--primary-color);
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.product-picker {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.product-pick-item {
    border: 1.5px solid var(--card-border);
    border-radius: 10px;
    padding: 0.75rem;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    text-align: center;
    background: var(--card-bg);
}

.product-pick-item.selected {
    border-color: var(--primary-color);
    background: rgba(29,150,200,0.08);
}

.product-pick-img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}

.product-pick-noimg {
    width: 100%;
    height: 100px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--accent-color);
    color: var(--text-muted);
    margin-bottom: 0.5rem;
}

.product-pick-name {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-color);
    line-height: 1.3;
    margin-bottom: 0.25rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-pick-price {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--primary-color);
}

.qty-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.qty-btn {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 1.5px solid var(--card-border);
    background: var(--card-bg);
    color: var(--text-color);
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    font-weight: 700;
    transition: border-color 0.2s;
}
.qty-btn:hover { border-color: var(--primary-color); color: var(--primary-color); }

.qty-display {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-color);
    min-width: 20px;
    text-align: center;
}

.resumen-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.6rem 0;
    border-bottom: 1px solid var(--card-border);
    font-size: 0.85rem;
}

.resumen-item:last-child { border-bottom: none; }

.resumen-nombre { color: var(--text-color); font-weight: 500; flex: 1; }
.resumen-precio { color: var(--primary-color); font-weight: 700; white-space: nowrap; }

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 0.75rem;
    margin-top: 0.25rem;
    border-top: 2px solid var(--primary-color);
    font-weight: 800;
    font-size: 1rem;
    color: var(--text-color);
}

.btn-reservar {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    background: #25d366;
    color: #fff;
    font-weight: 700;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.2s, transform 0.15s;
    margin-top: 1rem;
}
.btn-reservar:hover { background: #1da851; transform: translateY(-1px); color: #fff; }

.field-group { margin-bottom: 1rem; }
.field-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 0.4rem;
}
.field-input {
    width: 100%;
    background: var(--accent-color);
    border: 1.5px solid var(--card-border);
    border-radius: 8px;
    color: var(--text-color);
    padding: 0.65rem 0.875rem;
    font-size: 0.9rem;
    font-family: inherit;
    transition: border-color 0.2s;
}
.field-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(29,150,200,0.12);
}
.field-input::placeholder { color: var(--text-muted); }

.empty-cart-msg {
    text-align: center;
    color: var(--text-muted);
    padding: 2rem 0;
    font-size: 0.9rem;
}

.no-select-warning {
    background: rgba(231,76,60,0.08);
    border: 1px solid rgba(231,76,60,0.2);
    border-radius: 8px;
    color: #e74c3c;
    padding: 0.6rem 0.875rem;
    font-size: 0.84rem;
    display: none;
    margin-bottom: 0.75rem;
}
</style>
@endpush

@section('content')
<div class="reservar-hero">
    <div class="container">
        <h1><i class="fas fa-snowflake me-2" style="color:var(--primary-color);"></i>Reservar Productos</h1>
        <p>Selecciona lo que te interesa y te contactamos por WhatsApp</p>
    </div>
</div>

<div class="reservar-section">
    <div class="container">
        @if($errors->any())
            <div class="alert-error d-flex align-items-center gap-2 mb-4" style="background:rgba(231,76,60,0.1);border:1px solid rgba(231,76,60,0.2);border-left:3px solid #e74c3c;border-radius:8px;padding:0.75rem 1rem;color:#fca5a5;font-size:0.88rem;">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('reservar.store') }}" method="POST" id="reservarForm">
            @csrf
            <div class="row gx-4">

                <!-- Left: Product picker -->
                <div class="col-lg-8">
                    <div class="form-card mb-4">
                        <div class="form-card-title">
                            <i class="fas fa-box-open"></i> Elige tus productos
                        </div>
                        <div class="product-picker" id="productPicker">
                            @foreach($products as $p)
                                @php $stock = $p->total_stock; @endphp
                                <div class="product-pick-item {{ $preselected && $preselected->id === $p->id ? 'selected' : '' }}"
                                     data-id="{{ $p->id }}"
                                     data-nombre="{{ $p->nombre }}"
                                     data-precio="{{ $p->precio }}"
                                     data-qty="0"
                                     onclick="toggleProduct(this)">
                                    @if($p->img_path)
                                        <img class="product-pick-img" src="{{ $p->image_url }}" alt="{{ $p->nombre }}" loading="lazy">
                                    @else
                                        <div class="product-pick-noimg"><i class="fas fa-vest fa-2x"></i></div>
                                    @endif
                                    <div class="product-pick-name">{{ $p->nombre }}</div>
                                    <div class="product-pick-price">${{ number_format($p->precio, 0) }}</div>
                                    <div class="qty-row justify-content-center" style="display:none !important;" id="qty-row-{{ $p->id }}">
                                        <button type="button" class="qty-btn" onclick="event.stopPropagation(); changeQty('{{ $p->id }}', -1)">−</button>
                                        <span class="qty-display" id="qty-{{ $p->id }}">1</span>
                                        <button type="button" class="qty-btn" onclick="event.stopPropagation(); changeQty('{{ $p->id }}', +1)">+</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Customer info -->
                    <div class="form-card">
                        <div class="form-card-title">
                            <i class="fas fa-user"></i> Tus datos
                        </div>
                        <div class="row">
                            <div class="col-md-6 field-group">
                                <label class="field-label">Nombre *</label>
                                <input type="text" name="nombre" class="field-input" placeholder="Tu nombre completo" value="{{ old('nombre') }}" required>
                            </div>
                            <div class="col-md-6 field-group">
                                <label class="field-label">WhatsApp / Teléfono *</label>
                                <input type="tel" name="telefono" class="field-input" placeholder="Ej: 3001234567" value="{{ old('telefono') }}" required>
                            </div>
                            <div class="col-md-12 field-group">
                                <label class="field-label">Email (opcional)</label>
                                <input type="email" name="email" class="field-input" placeholder="correo@ejemplo.com" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-12 field-group">
                                <label class="field-label">Notas adicionales</label>
                                <textarea name="notas" class="field-input" rows="3" placeholder="Talla, color preferido, pregunta...">{{ old('notas') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Summary -->
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="form-card" style="position: sticky; top: 90px;">
                        <div class="form-card-title">
                            <i class="fas fa-receipt"></i> Resumen
                        </div>

                        <div id="noSelectWarning" class="no-select-warning" style="display:none;">
                            Selecciona al menos un producto.
                        </div>

                        <div id="resumenList">
                            <div class="empty-cart-msg" id="emptyMsg">
                                <i class="fas fa-shopping-bag fa-2x mb-2 d-block"></i>
                                Ningún producto seleccionado
                            </div>
                        </div>

                        <div id="totalRow" class="total-row" style="display:none;">
                            <span>Total estimado</span>
                            <span id="totalDisplay">$0</span>
                        </div>

                        <!-- Hidden inputs container -->
                        <div id="hiddenInputs"></div>

                        <div class="no-select-warning" id="warnMsg" style="display:none;">
                            <i class="fas fa-exclamation-circle me-1"></i> Selecciona al menos un producto.
                        </div>

                        <button type="submit" class="btn-reservar" onclick="return validateForm()">
                            <i class="fab fa-whatsapp"></i>
                            Reservar por WhatsApp
                        </button>

                        <p style="text-align:center;font-size:0.72rem;color:var(--text-muted);margin-top:0.75rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            La reserva no descuenta inventario. Solo registra tu interés.
                        </p>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script>
const selectedProducts = {};

function formatPrice(n) {
    return '$' + Number(n).toLocaleString('es-CO');
}

function toggleProduct(el) {
    const id     = el.dataset.id;
    const nombre = el.dataset.nombre;
    const precio = parseFloat(el.dataset.precio);
    const qtyRow = document.getElementById('qty-row-' + id);

    if (selectedProducts[id]) {
        delete selectedProducts[id];
        el.classList.remove('selected');
        qtyRow.style.setProperty('display', 'none', 'important');
    } else {
        selectedProducts[id] = { nombre, precio, qty: 1 };
        el.classList.add('selected');
        qtyRow.style.removeProperty('display');
        qtyRow.style.display = 'flex';
        document.getElementById('qty-' + id).textContent = '1';
    }
    renderResumen();
}

function changeQty(id, delta) {
    if (!selectedProducts[id]) return;
    let qty = selectedProducts[id].qty + delta;
    if (qty < 1) qty = 1;
    if (qty > 20) qty = 20;
    selectedProducts[id].qty = qty;
    document.getElementById('qty-' + id).textContent = qty;
    renderResumen();
}

function renderResumen() {
    const list   = document.getElementById('resumenList');
    const empty  = document.getElementById('emptyMsg');
    const total  = document.getElementById('totalRow');
    const hidden = document.getElementById('hiddenInputs');
    const ids    = Object.keys(selectedProducts);

    // Clear hidden inputs
    hidden.innerHTML = '';

    if (ids.length === 0) {
        list.innerHTML = '<div class="empty-cart-msg" id="emptyMsg"><i class="fas fa-shopping-bag fa-2x mb-2 d-block"></i>Ningún producto seleccionado</div>';
        total.style.display = 'none';
        return;
    }

    let html = '';
    let totalVal = 0;
    ids.forEach((id, i) => {
        const p = selectedProducts[id];
        const sub = p.precio * p.qty;
        totalVal += sub;
        html += `<div class="resumen-item">
            <span class="resumen-nombre">${p.nombre} ×${p.qty}</span>
            <span class="resumen-precio">${formatPrice(sub)}</span>
        </div>`;
        // Hidden inputs for form submission
        hidden.innerHTML += `<input type="hidden" name="items[${i}][id]" value="${id}">`;
        hidden.innerHTML += `<input type="hidden" name="items[${i}][qty]" value="${p.qty}">`;
    });

    list.innerHTML = html;
    total.style.display = 'flex';
    document.getElementById('totalDisplay').textContent = formatPrice(totalVal);
}

function validateForm() {
    if (Object.keys(selectedProducts).length === 0) {
        document.getElementById('warnMsg').style.display = 'flex';
        return false;
    }
    document.getElementById('warnMsg').style.display = 'none';
    return true;
}

// Handle preselected product
@if($preselected)
window.addEventListener('DOMContentLoaded', function() {
    const el = document.querySelector('[data-id="{{ $preselected->id }}"]');
    if (el) toggleProduct(el);
});
@endif
</script>
@endpush

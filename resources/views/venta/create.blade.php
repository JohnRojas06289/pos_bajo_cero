@extends('layouts.app')

@section('title', 'Realizar venta')

@push('css')
<style>
    body { overflow: hidden; }
    .pos-container { height: calc(100vh - 56px - 30px); overflow: hidden; }
    @media (max-width: 767px) { .pos-container { height: calc(100vh - 56px); } }
    
    /* Sidebar de categorías mejorada */
    .category-sidebar {
        height: 100%;
        overflow-y: auto;
        background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
        border-right: 1px solid rgba(255,255,255,0.06);
        box-shadow: 2px 0 12px rgba(0,0,0,0.15);
        max-width: 15%;
    }
    
    .product-grid {
        height: 100%;
        overflow-y: auto;
        padding: 1rem;
        padding-top: 0.5rem;
        background: #f1f5f9;
    }

    /* Fix for extra spacing at the top */
    main {
        padding: 0 !important;
        margin: 0 !important;
    }

    
    .cart-section {
        height: 100%;
        display: flex;
        flex-direction: column;
        background-color: #fff;
        border-left: 1px solid #e2e8f0;
        box-shadow: -4px 0 16px rgba(0,0,0,0.04);
    }



    /* Botones de categoría */
    .category-btn {
        width: 100%;
        text-align: left;
        padding: 10px 12px;
        background: transparent;
        border: none;
        border-bottom: 1px solid rgba(255,255,255,0.04);
        color: rgba(255,255,255,0.55);
        transition: all 0.2s ease;
        font-weight: 500;
        font-size: 0.82rem;
        position: relative;
        overflow: hidden;
        white-space: nowrap;
        display: flex;
        align-items: center;
    }

    .category-btn::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%) scaleY(0);
        height: 60%;
        width: 3px;
        background: #f59e0b;
        border-radius: 0 2px 2px 0;
        transition: transform 0.2s ease;
    }

    .category-btn:hover {
        background: rgba(245,158,11,0.08);
        color: rgba(255,255,255,0.85);
        padding-left: 16px;
    }

    .category-btn.active {
        background: linear-gradient(90deg, rgba(245,158,11,0.2) 0%, rgba(249,115,22,0.1) 100%);
        color: #fbbf24;
        font-weight: 600;
        border-left: 3px solid #f59e0b;
    }

    .category-btn.active::before {
        transform: translateY(-50%) scaleY(1);
    }

    .category-btn i {
        width: 22px;
        text-align: center;
        margin-right: 7px;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .category-btn .shortcut-hint {
        margin-left: auto;
        font-size: 0.68rem;
        opacity: 0.5;
        background: rgba(255,255,255,0.08);
        padding: 1px 5px;
        border-radius: 3px;
    }

    /* Tarjetas de producto */
    .product-card {
        cursor: pointer;
        transition: all 0.18s ease;
        border: 1.5px solid #e2e8f0 !important;
        overflow: hidden;
        border-radius: 10px !important;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
    }

    .product-card:hover {
        transform: translateY(-3px);
        border-color: #f59e0b !important;
        box-shadow: 0 6px 14px rgba(245,158,11,0.15) !important;
    }

    .product-card:active {
        transform: translateY(-1px) scale(0.99);
    }
    
    .product-img-container { 
        height: 140px; 
        overflow: hidden; 
        background: linear-gradient(135deg, #f5f5f5 0%, #e9ecef 100%);
        display: flex; 
        align-items: center; 
        justify-content: center;
        position: relative;
    }
    
    .product-img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-img {
        transform: scale(1.1);
    }
    
    .product-price { 
        font-weight: bold; 
        color: #f59e0b; 
        font-size: 1.15rem;
    }
    
    .product-name {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    /* Items del carrito más espaciados */
    .cart-items { 
        flex-grow: 1; 
        overflow-y: auto; 
        padding: 0;
        background: #fafafa;
    }
    
    .cart-item { 
        padding: 15px; 
        border-bottom: 1px solid #e5e7eb; 
        display: flex; 
        align-items: center; 
        justify-content: space-between;
        background: #fff;
        margin: 8px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    
    .cart-item:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transform: translateX(4px);
    }
    
    .cart-item-new {
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .cart-footer {
        padding: 1rem 1.125rem;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        box-shadow: 0 -4px 12px rgba(0,0,0,0.04);
    }
    
    .smart-cash-btn { 
        font-size: 0.9rem; 
        font-weight: 700; 
        padding: 10px 8px;
        border-radius: 8px;
        transition: all 0.2s ease;
        border: 2px solid #dee2e6;
    }
    
    .smart-cash-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-color: #f59e0b;
        background-color: #fff3e0;
    }
    
    /* Total más prominente */
    .total-display {
        font-size: 2rem !important;
        font-weight: 800 !important;
        color: #059669 !important;
        letter-spacing: -0.02em;
    }
    
    /* Botón de cobrar mejorado */
    #btnPay {
        font-size: 1.25rem;
        font-weight: 900;
        padding: 18px !important;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        transition: all 0.3s ease;
        border: none;
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }
    
    #btnPay:not(:disabled):hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.4);
    }
    
    #btnPay:not(:disabled):active {
        transform: translateY(-1px);
    }
    
    #btnPay:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Inputs mejorados */
    .form-control {
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        padding: 10px 12px;
        font-size: 1rem;
        transition: all 0.2s ease;
    }
    
    .form-control:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    
    /* Búsqueda mejorada */
    #searchInput {
        font-size: 1.1rem;
        padding: 14px 16px;
        border-radius: 12px;
    }
    
    /* Badge del carrito */
    #cartCount {
        font-size: 1rem;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 700;
    }
    
    /* Scrollbar personalizada */
    ::-webkit-scrollbar { 
        width: 8px; 
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb { 
        background: linear-gradient(180deg, #cbd5e1 0%, #94a3b8 100%);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #94a3b8 0%, #64748b 100%);
    }
    
    footer { display: none !important; }
    
    /* Talla filter strip */
    .talla-filter-btn {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        transition: all 0.2s ease;
        letter-spacing: 0.5px;
    }
    .talla-filter-btn.active {
        background: #f59e0b;
        color: #fff;
        border-color: #f59e0b;
    }
    .talla-filter-btn:not(.active) {
        background: #fff;
        color: #495057;
        border-color: #dee2e6;
    }
    .talla-filter-btn:hover:not(.active) {
        background: #fff3e0;
        border-color: #f59e0b;
        color: #f59e0b;
    }

    /* ── Tabs de modo de pago ── */
    .pay-mode-tabs {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 4px;
        background: #f1f5f9;
        border-radius: 10px;
        padding: 4px;
    }

    .pay-tab {
        padding: 6px 4px;
        border-radius: 7px;
        border: none;
        background: transparent;
        color: #64748b;
        font-weight: 600;
        font-size: 0.72rem;
        cursor: pointer;
        transition: all 0.18s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        white-space: nowrap;
    }

    .pay-tab i { font-size: 0.75rem; }

    .pay-tab:hover {
        background: rgba(255,255,255,0.7);
        color: #334155;
    }

    .pay-tab.active {
        background: white;
        color: #1e293b;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }

    .pay-tab.active-nequi   { background: #e8f5e9; color: #1b5e20; }
    .pay-tab.active-daviplata { background: #e3f2fd; color: #0d47a1; }
    .pay-tab.active-tarjeta { background: #ede7f6; color: #4527a0; }

    /* ── Botones de vuelto inteligente ── */
    .smart-cash-suggestion {
        flex: 1 1 0;
        min-width: 0;
        padding: 7px 4px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background: white;
        color: #334155;
        font-weight: 700;
        font-size: 0.78rem;
        cursor: pointer;
        transition: all 0.18s ease;
        text-align: center;
        white-space: nowrap;
    }

    .smart-cash-suggestion:hover {
        border-color: #f59e0b;
        background: #fffbeb;
        color: #d97706;
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(245,158,11,0.2);
    }

    .smart-cash-btn-exact {
        background: transparent;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        padding: 3px 10px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .smart-cash-btn-exact:hover {
        border-color: #94a3b8;
        background: #f8fafc;
        color: #334155;
    }

    /* ── Caja de info pago virtual ── */
    .virtual-info-box {
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #166534;
    }

    .virtual-info-box i { font-size: 1.3rem; color: #16a34a; }
    .virtual-info-box.nequi     { background: #f0fdf4; border-color: #86efac; color: #166534; }
    .virtual-info-box.daviplata { background: #eff6ff; border-color: #93c5fd; color: #1e40af; }
    .virtual-info-box.daviplata i { color: #2563eb; }
    .virtual-info-box.tarjeta   { background: #faf5ff; border-color: #c4b5fd; color: #4527a0; }
    .virtual-info-box.tarjeta i { color: #7c3aed; }

    /* Indicador de atajo de teclado */
    .keyboard-hint {
        position: fixed;
        bottom: 10px;
        right: 10px;
        background: rgba(0,0,0,0.8);
        color: #fff;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    
    .keyboard-hint.show {
        opacity: 1;
    }
    
    /* Animación de pulso para el botón de cobrar */
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
    
    #btnPay:not(:disabled) {
        animation: pulse 2s infinite;
    }
    
    /* Responsive mejorado */
    @media (max-width: 768px) {
        .product-card {
            border-radius: 10px;
        }
        
        .product-img-container {
            height: 120px;
        }
        
        .total-display {
            font-size: 2rem !important;
        }
        
        #btnPay {
            font-size: 1.1rem;
            padding: 16px !important;
        }
    }


    @media (min-width: 992px) {
        .col-lg-20 {
            flex: 0 0 auto;
            width: 20%;
        }
    }
</style>
<script src="{{ asset('js/sweetalert2.min.js') }}"></script>
@endpush

@section('content')
<!-- POS Info Bar -->
<div class="pos-info-bar d-none d-md-flex">
    <div class="info-item">
        <i class="fas fa-calendar-day"></i>
        <strong id="posDate"></strong>
    </div>
    <div class="info-item">
        <i class="fas fa-clock"></i>
        <strong id="posTime"></strong>
    </div>
    <div class="info-item">
        <i class="fas fa-user-circle"></i>
        <span>Cajero:</span> <strong>{{ auth()->user()->name }}</strong>
    </div>
</div>
<script>
    (function() {
        const d = document.getElementById('posDate');
        const t = document.getElementById('posTime');
        function update() {
            const now = new Date();
            if (d) d.textContent = now.toLocaleDateString('es-CO', {weekday:'short', day:'numeric', month:'short'});
            if (t) t.textContent = now.toLocaleTimeString('es-CO', {hour:'2-digit', minute:'2-digit'});
        }
        update();
        setInterval(update, 30000);
    })();
</script>
<form action="{{ route('ventas.store') }}" method="post" id="ventaForm" class="h-100">
    @csrf
    <div class="row g-0 pos-container">
        
        <!-- Column 1: Categories -->
        <div class="col-md-2 category-sidebar d-none d-md-block">
            <div class="p-3 text-white border-bottom border-secondary">
                <h5 class="m-0"><i class="fa-solid fa-layer-group me-2"></i>Categorías</h5>
            </div>
            <button type="button" class="category-btn active" onclick="filterCategory('all', this)">
                <i class="fa-solid fa-border-all"></i> Todo
            </button>
            @foreach ($categorias as $cat)
            <button type="button" class="category-btn" onclick="filterCategory('{{$cat->id}}', this)">
                <i class="fa-solid fa-tag"></i> {{$cat->caracteristica->nombre}}
            </button>
            @endforeach
        </div>

        <!-- Column 2: Products -->
        <div class="col-12 col-md product-grid">
            <div class="sticky-top pb-2 pt-1 mb-2" style="z-index: 10;">
                <!-- Talla Filter Strip -->
                <div id="tallaFilterStrip" class="d-flex gap-1 flex-wrap mb-2" style="min-height: 30px;"></div>
                <!-- Search -->
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-transparent border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control bg-transparent border-start-0" placeholder="Buscar producto..." autofocus>
                </div>
            </div>

            <div class="row g-3" id="productsContainer">
                @foreach ($productos as $item)
                <div class="col-6 col-md-3 col-lg-20 product-item"
                     data-category="{{$item->categoria_id}}"
                     data-talla="{{ $item->sigla }}"
                     data-genero="{{ $item->genero ?? '' }}"
                     data-search="{{ strtolower($item->nombre . ' ' . $item->codigo . ' ' . $item->talla_nombre) }}">
                    <div class="card h-100 product-card shadow-sm border-0 {{ $item->cantidad <= 0 ? 'opacity-75' : '' }}" onclick="addToCart('{{$item->id}}', '{{addslashes($item->nombre)}}', {{$item->precio ?? 0}}, {{$item->cantidad}}, '{{$item->sigla ?? 'UND'}}')">
                        <div class="product-img-container position-relative">
                            @if($item->cantidad <= 0)
                            <div class="position-absolute w-100 h-100 d-flex align-items-center justify-content-center" style="background:rgba(0,0,0,0.45);z-index:2;top:0;left:0;">
                                <span class="badge bg-danger" style="font-size:0.85rem;">AGOTADO</span>
                            </div>
                            @endif
                            @if($item->img_path)
                                <img src="{{ $item->image_url }}" class="product-img" alt="{{$item->nombre}}" onerror="this.parentElement.innerHTML='<div class=\'text-muted text-center p-3\'><i class=\'fa-solid fa-image fa-3x mb-2 opacity-25\'></i><br><small>Sin imagen</small></div>'">
                            @else
                                <div class="text-muted text-center p-3">
                                    <i class="fa-solid fa-image fa-3x mb-2 opacity-25"></i>
                                    <br><small>Sin imagen</small>
                                </div>
                            @endif
                        </div>
                        <div class="card-body p-2 text-center">
                            <h6 class="card-title mb-1 text-truncate product-name" title="{{$item->nombre}}">{{$item->nombre}}</h6>
                            <div class="d-flex justify-content-center align-items-center gap-1 mb-1 flex-wrap">
                                @if($item->sigla && $item->sigla !== 'UND')
                                <span class="badge" style="background:#f59e0b;color:#fff;font-size:0.7rem;letter-spacing:.5px;">{{ $item->sigla }}</span>
                                @endif
                                @if($item->genero === 'Hombre')
                                <span class="badge bg-primary" style="font-size:0.65rem;"><i class="fa-solid fa-mars"></i></span>
                                @elseif($item->genero === 'Mujer')
                                <span class="badge" style="background:#e879a0;font-size:0.65rem;"><i class="fa-solid fa-venus"></i></span>
                                @endif
                            </div>
                            <div class="product-price">{{$empresa->moneda->simbolo ?? '$'}} {{ number_format($item->precio ?? 0, 0, ',', '.') }}</div>
                            @if($item->cantidad <= 0)
                            <small class="text-danger d-block" style="font-size:0.7rem;">Sin stock</small>
                            @elseif($item->cantidad <= 3)
                            <span class="badge bg-danger" style="font-size:0.65rem;">¡Últimas {{ $item->cantidad }}!</span>
                            @else
                            <small class="text-success d-block" style="font-size:0.7rem;">Stock: {{$item->cantidad}}</small>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Column 3: Cart -->
        <div class="col-md-3 cart-section shadow-lg">
            <div class="p-3 bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="m-0"><i class="fa-solid fa-cart-shopping me-2"></i>Carrito</h5>
                <span class="badge bg-warning text-dark" id="cartCount">0</span>
            </div>
            
            <div class="d-none">
                <select name="cliente_id"><option value="{{$clientes->first()->id ?? ''}}" selected></option></select>
                <select name="comprobante_id"><option value="{{$comprobantes->first()->id ?? ''}}" selected></option></select>
            </div>
            {{-- metodo_pago se actualiza dinámicamente --}}
            <input type="hidden" name="metodo_pago" id="metodo_pago_input" value="EFECTIVO">

            <div class="cart-items" id="cartItemsContainer">
                <div class="text-center text-muted mt-5" id="emptyCartMessage">
                    <i class="fa-solid fa-basket-shopping fa-3x mb-3 opacity-50"></i>
                    <p>Carrito vacío</p>
                </div>
                <div id="cartList"></div>
            </div>

            <div class="cart-footer">

                <!-- Total -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fs-6 fw-bold text-secondary">TOTAL:</span>
                    <span class="total-display">{{$empresa->moneda->simbolo ?? '$'}} <span id="totalDisplay">0</span></span>
                </div>

                <input type="hidden" name="subtotal" id="inputSubtotal" value="0">
                <input type="hidden" name="total" id="inputTotal" value="0">

                <!-- Tabs de modo de pago -->
                <div class="pay-mode-tabs mb-2">
                    <button type="button" id="tabEfectivo" class="pay-tab active" onclick="setModoEfectivo()">
                        <i class="fas fa-money-bill-wave"></i> Efectivo
                    </button>
                    <button type="button" id="tabNequi" class="pay-tab" onclick="setModoVirtual('NEQUI', this)">
                        <i class="fas fa-mobile-alt"></i> Nequi
                    </button>
                    <button type="button" id="tabDaviplata" class="pay-tab" onclick="setModoVirtual('DAVIPLATA', this)">
                        <i class="fas fa-mobile-alt"></i> Daviplata
                    </button>
                    <button type="button" id="tabTarjeta" class="pay-tab" onclick="setModoVirtual('TARJETA', this)">
                        <i class="fas fa-credit-card"></i> Tarjeta
                    </button>
                </div>

                <!-- Sección Efectivo -->
                <div id="seccionEfectivo">
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="small text-muted fw-semibold">Vuelto sugerido</label>
                            <button type="button" class="smart-cash-btn-exact" onclick="setExactCash()" title="Pago exacto">
                                <i class="fas fa-equals me-1"></i>Exacto
                            </button>
                        </div>
                        <div id="smartCashButtons" class="d-flex gap-1 flex-wrap"></div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="small text-muted">Recibido</label>
                            <input type="text" id="dinero_recibido_display" class="form-control fw-bold" placeholder="0" oninput="updateReceived(this)">
                            <input type="hidden" id="dinero_recibido" name="monto_recibido">
                        </div>
                        <div class="col-6">
                            <label class="small text-muted">Vuelto</label>
                            <input type="text" id="vuelto_display" class="form-control fw-bold text-success bg-white" readonly placeholder="0">
                            <input type="hidden" id="vuelto" name="vuelto_entregado">
                        </div>
                    </div>
                </div>

                <!-- Sección Virtual -->
                <div id="seccionVirtual" style="display:none;">
                    <div class="virtual-info-box mb-2" id="virtualInfoBox">
                        <i class="fas fa-mobile-alt"></i>
                        <span id="virtualInfoText">Selecciona un método virtual</span>
                    </div>
                </div>

                <!-- Botón cobrar -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg fw-bold py-3" id="btnPay" disabled>
                        <i class="fa-solid fa-cash-register me-2"></i>
                        <span id="btnPayLabel">COBRAR</span>
                        <i class="fa-solid fa-check ms-2"></i>
                    </button>
                    <button type="button" class="btn btn-light btn-sm text-danger" onclick="cancelarVenta()">
                        <i class="fa-solid fa-times me-1"></i> Cancelar Venta
                    </button>
                </div>

                <div class="keyboard-hint" id="keyboardHint"></div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('js')
<script>
    var cart = [];
    var total = 0;
    var soundEnabled = true; // Cambiar a false para desactivar sonidos
    var activeCategory = 'all';
    var activeTalla = 'all';
    var searchValue = '';

    function applyFilters() {
        var items = document.querySelectorAll('.product-item');
        items.forEach(function(item) {
            var matchCat = activeCategory === 'all' || item.dataset.category === activeCategory;
            var matchTalla = activeTalla === 'all' || item.dataset.talla === activeTalla;
            var matchSearch = item.dataset.search.includes(searchValue);
            item.style.display = (matchCat && matchTalla && matchSearch) ? '' : 'none';
        });
    }

    function filterTalla(talla, btn) {
        activeTalla = talla;
        document.querySelectorAll('.talla-filter-btn').forEach(function(b) { b.classList.remove('active'); });
        btn.classList.add('active');
        applyFilters();
        setTimeout(function() { document.getElementById('searchInput').focus(); }, 100);
    }

    // Sonidos simples usando Web Audio API
    function playSound(frequency, duration) {
        if (!soundEnabled) return;
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = frequency;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + duration);
        } catch(e) {
            // Silenciar errores de audio
        }
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function showKeyboardHint(text) {
        const hint = document.getElementById('keyboardHint');
        hint.textContent = text;
        hint.classList.add('show');
        setTimeout(() => {
            hint.classList.remove('show');
        }, 1500);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-collapse sidebar with animation
        if (!document.body.classList.contains('sb-sidenav-toggled')) {
            setTimeout(function() {
                document.body.classList.add('sb-sidenav-toggled');
            }, 500); // Delay to show animation
        }

        // Auto-focus en búsqueda
        const searchInput = document.getElementById('searchInput');
        searchInput.focus();

        // Build talla filter strip dynamically from product data
        const tallasEncontradas = new Set();
        document.querySelectorAll('.product-item').forEach(function(item) {
            var talla = item.dataset.talla;
            if (talla && talla !== 'UND' && talla !== '') tallasEncontradas.add(talla);
        });
        const strip = document.getElementById('tallaFilterStrip');
        if (tallasEncontradas.size > 0) {
            // Add "TODOS" button
            var todoBtn = document.createElement('button');
            todoBtn.type = 'button';
            todoBtn.className = 'btn btn-sm talla-filter-btn active';
            todoBtn.textContent = 'TODOS';
            todoBtn.onclick = function() { filterTalla('all', this); };
            strip.appendChild(todoBtn);
            // Add talla buttons in logical order
            var tallaOrder = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
            var ordered = tallaOrder.filter(function(t) { return tallasEncontradas.has(t); });
            tallasEncontradas.forEach(function(t) { if (!tallaOrder.includes(t)) ordered.push(t); });
            ordered.forEach(function(talla) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-sm talla-filter-btn';
                btn.textContent = talla;
                btn.onclick = function() { filterTalla(talla, this); };
                strip.appendChild(btn);
            });
        } else {
            strip.style.display = 'none';
        }

        // Búsqueda mejorada
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                const visibleProducts = document.querySelectorAll('.product-item:not([style*="display: none"])');
                if (visibleProducts.length === 1) {
                    visibleProducts[0].querySelector('.product-card').click();
                    this.value = '';
                    searchValue = '';
                    applyFilters();
                }
                return;
            }
            searchValue = this.value.toLowerCase();
            applyFilters();
        });

        // Mantener foco en búsqueda después de agregar productos
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.cart-section') && !e.target.closest('input')) {
                setTimeout(() => searchInput.focus(), 100);
            }
        });

        // Atajos de teclado globales
        document.addEventListener('keydown', function(e) {


            // F9: Pago exacto
            if (e.key === 'F9') {
                e.preventDefault();
                setExactCash();
                showKeyboardHint('Pago Exacto');
                return;
            }

            // F10: $10k
            if (e.key === 'F10') {
                e.preventDefault();
                addCash(10000);
                showKeyboardHint('$10,000');
                return;
            }

            // F11: $20k
            if (e.key === 'F11') {
                e.preventDefault();
                addCash(20000);
                showKeyboardHint('$20,000');
                return;
            }

            // F12: $50k
            if (e.key === 'F12') {
                e.preventDefault();
                addCash(50000);
                showKeyboardHint('$50,000');
                return;
            }

            // Enter: Cobrar (si está habilitado)
            if (e.key === 'Enter' && !e.target.matches('#searchInput')) {
                const btnPay = document.getElementById('btnPay');
                if (!btnPay.disabled) {
                    e.preventDefault();
                    btnPay.click();
                }
                return;
            }

            // Escape: Cancelar venta
            if (e.key === 'Escape') {
                e.preventDefault();
                if (cart.length > 0) {
                    Swal.fire({
                        title: '¿Cancelar venta?',
                        text: 'Se perderán todos los productos del carrito',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, cancelar',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cancelarVenta();
                            showKeyboardHint('Venta cancelada');
                        }
                    });
                }
                return;
            }

            // / (slash): Enfocar búsqueda
            if (e.key === '/' && !e.target.matches('input')) {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
                return;
            }
        });
    });

    function filterCategory(catId, btn) {
        activeCategory = catId;
        document.querySelectorAll('.category-btn').forEach(function(b) { b.classList.remove('active'); });
        btn.classList.add('active');
        applyFilters();
        setTimeout(function() { document.getElementById('searchInput').focus(); }, 100);
    }

    function addToCart(id, nombre, precio, stock, sigla) {
        id = id.toString();
        var existingItem = cart.find(function(item) { return item.id === id; });
        var currentQty = existingItem ? existingItem.cantidad : 0;
        
        if (currentQty + 1 > stock) {
            playSound(200, 0.2); // Sonido de error
            Swal.fire({ 
                icon: 'error', 
                title: 'Stock insuficiente', 
                toast: true, 
                position: 'top-end', 
                showConfirmButton: false, 
                timer: 2000,
                timerProgressBar: true
            });
            return;
        }

        // Sonido de éxito
        playSound(800, 0.1);

        if (existingItem) {
            existingItem.cantidad++;
            existingItem.subtotal = existingItem.cantidad * existingItem.precio;
        } else {
            cart.push({ 
                id: id, 
                nombre: nombre, 
                precio: parseFloat(precio), 
                cantidad: 1, 
                sigla: sigla, 
                stock: parseInt(stock),
                subtotal: parseFloat(precio),
                isNew: true
            });
        }
        renderCart();
        
        // Mostrar notificación toast
        Swal.fire({
            icon: 'success',
            title: 'Producto agregado',
            text: nombre,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
        });
    }

    function updateQuantityManual(id, newQty, maxStock) {
        newQty = parseInt(newQty);
        
        if (isNaN(newQty) || newQty < 1) {
            playSound(200, 0.2);
            Swal.fire({ 
                icon: 'warning', 
                title: 'Cantidad mínima: 1', 
                toast: true, 
                position: 'top-end', 
                showConfirmButton: false, 
                timer: 2000 
            });
            renderCart();
            return;
        }
        
        if (newQty > maxStock) {
            playSound(200, 0.2);
            Swal.fire({ 
                icon: 'error', 
                title: 'Stock insuficiente (máx: ' + maxStock + ')', 
                toast: true, 
                position: 'top-end', 
                showConfirmButton: false, 
                timer: 2000 
            });
            renderCart();
            return;
        }

        var item = cart.find(function(i) { return i.id === id; });
        if (item) {
            item.cantidad = newQty;
            item.subtotal = item.cantidad * item.precio;
            playSound(600, 0.1);
            renderCart();
        }
    }

    function removeFromCart(id) {
        playSound(400, 0.15);
        Swal.fire({
            title: '¿Eliminar producto?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = cart.filter(function(i) { return i.id !== id; });
                renderCart();
                Swal.fire({
                    icon: 'success',
                    title: 'Producto eliminado',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    }

    function renderCart() {
        var container = document.getElementById('cartList');
        container.innerHTML = '';
        total = 0;

        if (cart.length === 0) {
            document.getElementById('emptyCartMessage').style.display = 'block';
            document.getElementById('btnPay').disabled = true;
        } else {
            document.getElementById('emptyCartMessage').style.display = 'none';
            
            cart.forEach(function(item) {
                total += item.subtotal;
                var itemClass = item.isNew ? 'cart-item cart-item-new' : 'cart-item';
                item.isNew = false; // Resetear flag
                
                var tallaBadge = (item.sigla && item.sigla !== 'UND') ?
                    '<span class="badge ms-1" style="background:#f59e0b;color:#fff;font-size:0.65rem;">' + item.sigla + '</span>' : '';
                var row = '<div class="' + itemClass + '">' +
                    '<div class="flex-grow-1">' +
                        '<div class="fw-bold text-truncate" style="max-width: 140px;">' + item.nombre + tallaBadge + '</div>' +
                        '<div class="d-flex align-items-center gap-2 mt-1">' +
                            '<small class="text-muted">Cant:</small>' +
                            '<input type="number" class="form-control form-control-sm" style="width: 60px;" ' +
                                'value="' + item.cantidad + '" ' +
                                'min="1" max="' + item.stock + '" ' +
                                'onchange="updateQuantityManual(\'' + item.id + '\', this.value, ' + item.stock + ')" ' +
                                'onclick="this.select()">' +
                        '</div>' +
                        '<small class="text-muted">' + formatNumber(item.precio) + ' c/u</small>' +
                        '<input type="hidden" name="arrayidproducto[]" value="' + item.id + '">' +
                        '<input type="hidden" name="arraycantidad[]" value="' + item.cantidad + '">' +
                        '<input type="hidden" name="arrayprecioventa[]" value="' + item.precio + '">' +
                    '</div>' +
                    '<div class="text-end ms-2">' +
                        '<div class="fw-bold mb-2 text-primary">' + formatNumber(item.subtotal) + '</div>' +
                        '<button type="button" class="btn btn-sm btn-outline-danger px-2" onclick="removeFromCart(\'' + item.id + '\')" title="Eliminar">' +
                            '<i class="fa-solid fa-trash"></i>' +
                        '</button>' +
                    '</div>' +
                '</div>';
                container.insertAdjacentHTML('beforeend', row);
            });
        }
        
        document.getElementById('totalDisplay').innerText = formatNumber(total);
        document.getElementById('inputTotal').value = total;
        document.getElementById('inputSubtotal').value = total;

        document.getElementById('cartCount').innerText = cart.reduce(function(acc, item) { return acc + item.cantidad; }, 0);

        updateSmartCashButtons(total);
        calculateChange();
    }

    function setExactCash() {
        if(total === 0) return;
        document.getElementById('dinero_recibido').value = total;
        document.getElementById('dinero_recibido_display').value = formatNumber(total);
        playSound(600, 0.1);
        calculateChange();
    }

    function addCash(amount) {
        if(total === 0) return;
        document.getElementById('dinero_recibido').value = amount;
        document.getElementById('dinero_recibido_display').value = formatNumber(amount);
        playSound(600, 0.1);
        calculateChange();
    }

    // ── Sugerencias de cambio inteligentes ──────────────────────────
    function updateSmartCashButtons(currentTotal) {
        var container = document.getElementById('smartCashButtons');
        container.innerHTML = '';
        if (currentTotal <= 0) return;

        // Denominaciones COP de menor a mayor
        var denoms = [1000, 2000, 5000, 10000, 20000, 50000, 100000, 200000];
        var suggestions = [];

        for (var i = 0; i < denoms.length; i++) {
            var d = denoms[i];
            // Redondear al siguiente múltiplo de d por encima del total
            var candidate = Math.ceil(currentTotal / d) * d;
            if (candidate > currentTotal && suggestions.indexOf(candidate) === -1) {
                suggestions.push(candidate);
            }
            if (suggestions.length >= 3) break;
        }

        suggestions.forEach(function(amount) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'smart-cash-suggestion';
            btn.innerHTML = '$' + formatNumber(amount);
            btn.onclick = function() { addCash(amount); };
            container.appendChild(btn);
        });
    }

    // ── Modo de pago: Efectivo ───────────────────────────────────────
    function setModoEfectivo() {
        document.getElementById('metodo_pago_input').value = 'EFECTIVO';

        // Activar tab efectivo, desactivar los demás
        document.querySelectorAll('.pay-tab').forEach(function(t) {
            t.className = 'pay-tab';
        });
        document.getElementById('tabEfectivo').classList.add('active');

        // Mostrar sección efectivo, ocultar virtual
        document.getElementById('seccionEfectivo').style.display = '';
        document.getElementById('seccionVirtual').style.display = 'none';

        document.getElementById('btnPayLabel').textContent = 'COBRAR';
        calculateChange();
    }

    // ── Modo de pago: Virtual (Nequi / Daviplata / Tarjeta) ─────────
    function setModoVirtual(method, btn) {
        document.getElementById('metodo_pago_input').value = method;

        // Reset todos los tabs
        document.querySelectorAll('.pay-tab').forEach(function(t) {
            t.className = 'pay-tab';
        });

        var infoBox  = document.getElementById('virtualInfoBox');
        var infoText = document.getElementById('virtualInfoText');
        infoBox.className = 'virtual-info-box mb-2';
        var iconClass = 'fas fa-mobile-alt';

        if (method === 'NEQUI') {
            btn.classList.add('active', 'active-nequi');
            infoBox.classList.add('nequi');
            infoText.textContent = 'Pago por Nequi — Confirma la transferencia antes de cobrar';
        } else if (method === 'DAVIPLATA') {
            btn.classList.add('active', 'active-daviplata');
            infoBox.classList.add('daviplata');
            infoText.textContent = 'Pago por Daviplata — Confirma la transferencia antes de cobrar';
        } else if (method === 'TARJETA') {
            btn.classList.add('active', 'active-tarjeta');
            infoBox.classList.add('tarjeta');
            infoText.textContent = 'Pago con Tarjeta — Verifica el datáfono antes de cobrar';
            iconClass = 'fas fa-credit-card';
        }

        infoBox.querySelector('i').className = iconClass;

        document.getElementById('seccionEfectivo').style.display = 'none';
        document.getElementById('seccionVirtual').style.display = '';

        // En pagos virtuales: monto recibido = total, vuelto = 0
        document.getElementById('dinero_recibido').value = total;
        document.getElementById('vuelto').value = 0;
        document.getElementById('btnPayLabel').textContent = 'COBRAR — ' + method;

        if (total > 0) {
            document.getElementById('btnPay').disabled = false;
        }
    }

    function updateReceived(input) {
        var val = input.value.replace(/\D/g, '');
        var num = parseFloat(val);
        
        if(isNaN(num)) {
            document.getElementById('dinero_recibido').value = 0;
            input.value = '';
        } else {
            document.getElementById('dinero_recibido').value = num;
            input.value = formatNumber(num);
        }
        calculateChange();
    }

    function calculateChange() {
        var received = parseFloat(document.getElementById('dinero_recibido').value) || 0;
        
        if (received >= total && total > 0) {
            var change = received - total;
            document.getElementById('vuelto').value = change;
            document.getElementById('vuelto_display').value = formatNumber(change);
            document.getElementById('btnPay').disabled = false;
            playSound(1000, 0.1);
        } else {
            document.getElementById('vuelto').value = '';
            document.getElementById('vuelto_display').value = '';
            document.getElementById('btnPay').disabled = true;
        }
    }

    function cancelarVenta() {
        cart = [];
        document.getElementById('dinero_recibido').value = '';
        document.getElementById('dinero_recibido_display').value = '';
        document.getElementById('vuelto').value = '';
        document.getElementById('vuelto_display').value = '';
        setModoEfectivo(); // Resetear al modo efectivo
        renderCart();
        document.getElementById('searchInput').focus();
    }

    // Prevenir envío accidental del formulario
    document.getElementById('ventaForm').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Carrito vacío',
                text: 'Agrega productos antes de cobrar'
            });
            return false;
        }
        
        // Sonido de éxito al procesar venta
        playSound(1200, 0.3);
    });
</script>
@endpush

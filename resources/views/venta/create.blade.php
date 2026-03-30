@extends('layouts.app')

@section('title', 'POS — Nueva Venta')

@push('css')
<style>
/* ── POS layout reset ── */
body { overflow: hidden !important; }
main { padding: 0 !important; }

/* Ocultar botón IA en POS */
#aiChatBtn, #aiChatPanel { display: none !important; }

/* ── Sidebar slide-out transition para POS ── */
#layoutSidenav_nav {
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.35s ease !important;
}
#layoutSidenav_content {
    transition: padding-left 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                margin-left 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
body.pos-sidebar-hidden #layoutSidenav_nav {
    width: 0 !important;
    transform: translateX(-225px) !important;
    opacity: 0 !important;
    pointer-events: none !important;
    overflow: hidden !important;
}
/* Desktop (≥992px): el contenido usa padding-left */
@media (min-width: 992px) {
    body.pos-sidebar-hidden.sb-nav-fixed #layoutSidenav #layoutSidenav_content {
        padding-left: 0 !important;
        margin-left: 0 !important;
    }
}
/* Mobile (<992px): el contenido ya no usa padding-left */
@media (max-width: 991.98px) {
    body.pos-sidebar-hidden #layoutSidenav #layoutSidenav_content {
        margin-left: 0 !important;
        padding-left: 0 !important;
    }
}

/* ── POS wrapper: 65 / 35 ── */
.pos-wrapper {
    display: flex;
    height: calc(100vh - 56px);
    margin-top: 56px;
    overflow: hidden;
    background: var(--bg-primary);
}

/* ════════════════════════════
   LEFT — Products (65%)
════════════════════════════ */
.pos-products {
    flex: 1 1 65%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-width: 0;
}

/* Top bar: search + categories */
.pos-topbar {
    flex-shrink: 0;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-color);
    padding: 0.625rem 0.875rem 0;
    z-index: 5;
}

/* Search */
.pos-search-wrap {
    position: relative;
    margin-bottom: 0.5rem;
}

.pos-search-wrap .search-icon {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.85rem;
    pointer-events: none;
}

#searchInput {
    width: 100%;
    height: 40px;
    padding: 0 0.875rem 0 2.4rem;
    background: var(--input-bg);
    border: 1.5px solid var(--input-border);
    border-radius: 8px;
    color: var(--text-primary);
    font-size: 0.9rem;
    font-family: 'Inter', sans-serif;
    transition: border-color 0.2s ease;
    outline: none;
}
#searchInput:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(29,150,200,0.15); }
#searchInput::placeholder { color: var(--text-muted); }

/* Body: grid + alpha bar */
.pos-body {
    flex: 1;
    display: flex;
    overflow: hidden;
    position: relative;
}

/* Product grid */
.product-grid {
    flex: 1;
    overflow-y: auto;
    padding: 0.875rem 0.5rem 0.875rem 0.875rem;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(148px, 1fr));
    grid-auto-rows: min-content;
    gap: 0.625rem;
    align-content: start;
    scrollbar-width: thin;
    scrollbar-color: var(--border-color) transparent;
}

/* ── Product card ── */
.product-card {
    background: var(--card-bg);
    border: 1.5px solid var(--border-color);
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
    display: flex;
    flex-direction: column;
    user-select: none;
    position: relative;
}
.product-card:hover {
    transform: translateY(-2px);
    border-color: var(--accent);
    box-shadow: 0 6px 18px rgba(230,126,34,0.15);
}
.product-card.out-of-stock {
    opacity: 0.5;
    filter: grayscale(0.5);
    cursor: not-allowed;
}
.product-card.out-of-stock:hover { transform: none; border-color: var(--border-color); box-shadow: none; }

/* Add flash */
.product-card.added {
    border-color: var(--success) !important;
    box-shadow: 0 0 0 3px rgba(39,174,96,0.2) !important;
}

.card-img-wrap {
    width: 100%;
    aspect-ratio: 1;
    background: var(--hover-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    flex-shrink: 0;
}

.card-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-img-placeholder {
    font-size: 2rem;
    color: var(--text-muted);
    opacity: 0.4;
}

.stock-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    font-size: 0.62rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
    line-height: 1.5;
    pointer-events: none;
}

.stock-badge.ok      { background: rgba(39,174,96,0.15); color: #1a7a41; }
.stock-badge.low     { background: rgba(243,156,18,0.2);  color: #b45309; }
.stock-badge.out     { background: rgba(231,76,60,0.15);  color: #c0392b; }

[data-theme="dark"] .stock-badge.ok  { background: rgba(39,174,96,0.2); color: #4ade80; }
[data-theme="dark"] .stock-badge.low { background: rgba(243,156,18,0.2); color: #fbbf24; }
[data-theme="dark"] .stock-badge.out { background: rgba(231,76,60,0.2); color: #f87171; }

.card-body-sm {
    padding: 0.5rem 0.625rem 0.625rem;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.card-product-name {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
}

.card-product-price {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--accent);
    margin-top: auto;
}

/* ── Family card size picker ── */
.family-card { cursor: default; }
.family-card:hover { transform: none; box-shadow: var(--shadow-sm); }

.size-picker {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 4px;
}

.size-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    border: 1.5px solid var(--border-color);
    border-radius: 7px;
    background: var(--card-bg);
    cursor: pointer;
    padding: 3px 7px 2px;
    min-width: 36px;
    transition: border-color .15s, background .15s, transform .1s;
    line-height: 1.2;
}
.size-btn:hover:not(.size-out) {
    border-color: var(--accent);
    background: rgba(29,150,200,0.08);
    transform: scale(1.06);
}
.size-btn.size-out { opacity: 0.4; cursor: not-allowed; }
.size-btn.size-added { border-color: var(--success) !important; background: rgba(39,174,96,0.12) !important; }

.size-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--text-primary);
}
.size-stock {
    font-size: 0.6rem;
    font-weight: 600;
}
.size-stock.ok  { color: #1a7a41; }
.size-stock.low { color: #b45309; }
.size-stock.out { color: #c0392b; }

[data-theme="dark"] .size-stock.ok  { color: #4ade80; }
[data-theme="dark"] .size-stock.low { color: #fbbf24; }
[data-theme="dark"] .size-stock.out { color: #f87171; }

/* ── Alphabetical bar ── */
.alpha-bar {
    width: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.5rem 0;
    overflow-y: auto;
    scrollbar-width: none;
    background: transparent;
    flex-shrink: 0;
    gap: 1px;
    touch-action: none;
    user-select: none;
    z-index: 4;
}
.alpha-bar::-webkit-scrollbar { display: none; }

.alpha-letter {
    font-size: 0.55rem;
    font-weight: 700;
    color: var(--text-muted);
    line-height: 1;
    padding: 2px;
    border-radius: 3px;
    cursor: pointer;
    transition: color 0.15s ease, background 0.15s ease;
    width: 18px;
    text-align: center;
}
.alpha-letter:hover, .alpha-letter.has-products:hover {
    color: var(--accent);
    background: rgba(230,126,34,0.1);
}
.alpha-letter.has-products { color: var(--text-secondary); }
.alpha-letter.active-letter { color: var(--accent); background: rgba(230,126,34,0.15); }

/* Empty state */
.products-empty {
    grid-column: 1 / -1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
    color: var(--text-muted);
    text-align: center;
    gap: 0.75rem;
}
.products-empty i { font-size: 2.5rem; opacity: 0.3; }
.products-empty p { font-size: 0.875rem; margin: 0; }

/* Section separator (alpha) */
.product-letter-anchor {
    grid-column: 1 / -1;
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 0.25rem 0 0.1rem;
    border-bottom: 1px solid var(--border-color);
    margin-top: 0.25rem;
}

/* ════════════════════════════
   RIGHT — Cart (35%)
════════════════════════════ */
.pos-cart {
    flex: 0 0 35%;
    max-width: 380px;
    min-width: 280px;
    display: flex;
    flex-direction: column;
    background: var(--bg-secondary);
    border-left: 1px solid var(--border-color);
    overflow: hidden;
    min-height: 0;
}

/* Cart header */
.cart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.75rem;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-secondary);
    flex-shrink: 0;
}
.cart-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.cart-count-badge {
    background: var(--accent);
    color: #fff;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
    min-width: 22px;
    text-align: center;
}
.cart-clear-btn {
    font-size: 0.75rem;
    color: var(--danger);
    background: none;
    border: 1px solid rgba(231,76,60,0.2);
    border-radius: 6px;
    padding: 0.25rem 0.6rem;
    cursor: pointer;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    transition: background 0.15s ease;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.cart-clear-btn:hover { background: rgba(231,76,60,0.08); }

/* Cart items scroll */
.cart-items-wrap {
    flex: 1;
    overflow-y: auto;
    padding: 0.4rem;
    min-height: 60px;
    scrollbar-width: thin;
    scrollbar-color: var(--border-color) transparent;
}

/* Cart empty */
.cart-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: var(--text-muted);
    gap: 0.75rem;
    padding: 2rem;
    text-align: center;
}
.cart-empty i { font-size: 2.5rem; opacity: 0.25; }
.cart-empty p { font-size: 0.825rem; margin: 0; }

/* Cart item */
.cart-item {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.5rem 0.375rem;
    border-radius: 8px;
    transition: background 0.15s ease;
    border-bottom: 1px solid var(--border-color);
}
.cart-item:last-child { border-bottom: none; }
.cart-item:hover { background: var(--hover-bg); }

.cart-item-name {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-primary);
    flex: 1;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cart-item-price {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-family: 'JetBrains Mono', monospace;
    white-space: nowrap;
}

.cart-item-price-input {
    width: 90px;
    height: 24px;
    padding: 0 0.35rem;
    font-size: 0.72rem;
    font-family: 'JetBrains Mono', monospace;
    color: var(--accent);
    background: var(--input-bg);
    border: 1.5px solid var(--accent);
    border-radius: 5px;
    outline: none;
    text-align: right;
}
.cart-item-price-input:focus { box-shadow: 0 0 0 2px rgba(29,150,200,0.18); }

.cart-item-subtotal {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--text-primary);
    font-family: 'JetBrains Mono', monospace;
    white-space: nowrap;
    min-width: 60px;
    text-align: right;
}

/* Qty controls */
.qty-controls {
    display: flex;
    align-items: center;
    gap: 2px;
    flex-shrink: 0;
}
.qty-btn {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    border: 1.5px solid var(--border-color);
    background: var(--card-bg);
    color: var(--text-secondary);
    font-size: 0.8rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
    font-family: 'Inter', sans-serif;
    font-weight: 700;
    line-height: 1;
}
.qty-btn:hover { border-color: var(--accent); color: var(--accent); background: rgba(230,126,34,0.08); }
.qty-value {
    min-width: 28px;
    text-align: center;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-primary);
}

.cart-remove-btn {
    width: 24px;
    height: 24px;
    border-radius: 5px;
    border: none;
    background: none;
    color: var(--danger);
    cursor: pointer;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.5;
    transition: opacity 0.15s ease, background 0.15s ease;
    flex-shrink: 0;
}
.cart-remove-btn:hover { opacity: 1; background: rgba(231,76,60,0.1); }

/* Cart footer */
.cart-footer {
    flex-shrink: 0;
    border-top: 1px solid var(--border-color);
    background: var(--bg-secondary);
    padding: 0.45rem 0.625rem;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    overflow-y: auto;
    scrollbar-width: none;
}
.cart-footer::-webkit-scrollbar { display: none; }

/* Totals */
.cart-totals {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    padding: 0.15rem 0;
}
.total-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.82rem;
    color: var(--text-secondary);
}
.total-row.grand {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--text-primary);
    border-top: 1px solid var(--border-color);
    padding-top: 0.375rem;
    margin-top: 0.25rem;
}
.total-row.grand .total-amount {
    color: var(--accent);
    font-family: 'JetBrains Mono', monospace;
}
.total-amount { font-family: 'JetBrains Mono', monospace; font-weight: 600; }

/* Form fields */
.cart-select {
    width: 100%;
    height: 34px;
    padding: 0 0.6rem;
    background: var(--input-bg);
    border: 1.5px solid var(--input-border);
    border-radius: 8px;
    color: var(--text-primary);
    font-size: 0.85rem;
    font-family: 'Inter', sans-serif;
    outline: none;
    cursor: pointer;
    transition: border-color 0.2s ease;
}
.cart-select:focus { border-color: var(--accent); }

.cart-field-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 0.15rem;
    display: block;
}

/* Payment methods */
.payment-pills {
    display: flex;
    gap: 0.3rem;
    flex-wrap: wrap;
}
.pay-pill {
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    border: 1.5px solid var(--border-color);
    background: var(--input-bg);
    color: var(--text-secondary);
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    font-family: 'Inter', sans-serif;
}
.pay-pill:hover  { border-color: var(--accent); color: var(--accent); }
.pay-pill.active { background: var(--accent); border-color: var(--accent); color: #fff; }

/* Cash section */
.cash-section {
    display: none;
    flex-direction: column;
    gap: 0.375rem;
}
.cash-section.visible { display: flex; }

.cash-row {
    display: flex;
    gap: 0.375rem;
    align-items: center;
}
.cash-label {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--text-muted);
    min-width: 72px;
    flex-shrink: 0;
}
.cash-input {
    flex: 1;
    height: 32px;
    padding: 0 0.6rem;
    background: var(--input-bg);
    border: 1.5px solid var(--input-border);
    border-radius: 7px;
    color: var(--text-primary);
    font-size: 0.875rem;
    font-family: 'JetBrains Mono', monospace;
    font-weight: 600;
    outline: none;
    transition: border-color 0.2s ease;
}
.cash-input:focus { border-color: var(--accent); }
.cash-input.readonly {
    background: var(--hover-bg);
    cursor: default;
    color: var(--success);
}
.cash-suggestions-wrap {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.cash-sugg-label {
    font-size: 0.68rem;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.cash-suggestions {
    display: flex;
    gap: 0.25rem;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.cash-suggestions::-webkit-scrollbar { display: none; }
.cash-sugg {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    background: var(--input-bg);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.12s ease;
    font-family: 'JetBrains Mono', monospace;
    flex-shrink: 0;
    white-space: nowrap;
}
.cash-sugg:hover { border-color: var(--accent); color: var(--accent); background: rgba(230,126,34,0.07); }
.cash-sugg-exact { border-color: var(--accent); color: var(--accent); line-height: 1.2; padding: 3px 10px; }
.cash-sugg-exact small { font-size: 0.6rem; opacity: 0.85; font-weight: 600; }

/* Pay button */
.btn-pay {
    width: 100%;
    height: 44px;
    border: none;
    border-radius: 10px;
    background: var(--success);
    color: #fff;
    font-size: 1rem;
    font-weight: 800;
    font-family: 'Inter', sans-serif;
    letter-spacing: 0.03em;
    cursor: pointer;
    transition: background 0.18s ease, opacity 0.18s ease, transform 0.12s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    box-shadow: 0 4px 16px rgba(39,174,96,0.3);
}
.btn-pay:hover:not(:disabled) { background: #219A52; box-shadow: 0 6px 20px rgba(39,174,96,0.4); transform: translateY(-1px); }
.btn-pay:active:not(:disabled) { transform: translateY(0); }
.btn-pay:disabled { opacity: 0.4; cursor: not-allowed; box-shadow: none; }
.btn-pay.loading { opacity: 0.7; pointer-events: none; }

/* ════════════════════════════
   MOBILE
════════════════════════════ */
.mobile-bottom-bar {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 64px;
    background: var(--bg-sidebar);
    border-top: 1px solid rgba(255,255,255,0.08);
    z-index: 100;
    align-items: center;
    justify-content: space-between;
    padding: 0 1rem;
    gap: 0.75rem;
}
.mobile-total-info {
    display: flex;
    flex-direction: column;
    color: #fff;
}
.mobile-total-label { font-size: 0.65rem; opacity: 0.6; font-weight: 500; text-transform: uppercase; letter-spacing: 0.08em; }
.mobile-total-amount { font-size: 1.1rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; }
.btn-view-cart {
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 0.6rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-family: 'Inter', sans-serif;
    flex-shrink: 0;
    min-height: 44px;
}

/* Bottom sheet */
.cart-sheet-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 200;
    backdrop-filter: blur(2px);
}
.cart-sheet-overlay.open { display: block; }

.cart-sheet {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    max-height: 92dvh;
    background: var(--bg-secondary);
    border-radius: 16px 16px 0 0;
    z-index: 201;
    display: flex;
    flex-direction: column;
    transform: translateY(100%);
    transition: transform 0.32s cubic-bezier(0.32, 0.72, 0, 1);
    overflow: hidden;
}
.cart-sheet.open { transform: translateY(0); }

.cart-sheet-handle {
    width: 36px;
    height: 4px;
    background: var(--border-color);
    border-radius: 2px;
    margin: 0.625rem auto;
    flex-shrink: 0;
}
.cart-sheet-close {
    position: absolute;
    top: 0.5rem;
    right: 0.875rem;
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.1rem;
    cursor: pointer;
    padding: 0.375rem;
}

/* Responsive breakpoints */
@media (max-width: 767px) {
    .pos-wrapper { flex-direction: column; height: calc(100vh - 56px - 64px); margin-top: 56px; }
    .pos-products { flex: 1; }
    .pos-cart { display: none; }
    .mobile-bottom-bar { display: flex; }
    .product-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
    .alpha-bar { width: 16px; }
    .alpha-letter { font-size: 0.5rem; }
}

@media (min-width: 768px) {
    .cart-sheet, .cart-sheet-overlay, .mobile-bottom-bar { display: none !important; }
}

@media (min-width: 768px) and (max-width: 991px) {
    .pos-cart { flex: 0 0 42%; max-width: 320px; }
    .product-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); }
}

/* ── Barcode scanner mode ── */
.scan-row {
    display: flex;
    gap: 0.5rem;
    align-items: stretch;
    margin-bottom: 0.5rem;
}
.scan-row .pos-search-wrap {
    flex: 1;
    margin-bottom: 0;
    position: relative;
}
.scan-mode-btn {
    height: 40px;
    padding: 0 0.875rem;
    border-radius: 8px;
    border: 1.5px solid var(--input-border);
    background: var(--input-bg);
    color: var(--text-muted);
    cursor: pointer;
    transition: all 0.18s ease;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    white-space: nowrap;
    flex-shrink: 0;
}
.scan-mode-btn:hover { border-color: var(--accent); color: var(--accent); }
.scan-mode-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }
.scan-feedback {
    height: 14px;
    font-size: 0.7rem;
    font-weight: 600;
    text-align: center;
    margin-bottom: 0.15rem;
    margin-top: -0.3rem;
    opacity: 0;
    transition: opacity 0.2s;
    font-family: 'Inter', sans-serif;
}
.scan-feedback.show { opacity: 1; }
.scan-feedback.ok  { color: #27ae60; }
.scan-feedback.err { color: #e74c3c; }

/* ── Filter bar ── */
.pos-filters {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem 0.4rem;
    overflow-x: auto;
    scrollbar-width: none;
    border-top: 1px solid var(--border-color);
    background: var(--bg-secondary);
    flex-shrink: 0;
}
.pos-filters::-webkit-scrollbar { display: none; }
.pos-filter-select {
    height: 30px;
    padding: 0 0.6rem;
    font-size: 0.72rem;
    font-weight: 600;
    font-family: 'Inter', sans-serif;
    border: 1.5px solid var(--border-color);
    border-radius: 20px;
    background: var(--card-bg);
    color: var(--text-secondary);
    cursor: pointer;
    outline: none;
    white-space: nowrap;
    flex-shrink: 0;
    transition: border-color 0.18s, color 0.18s, background 0.18s;
    appearance: none;
    -webkit-appearance: none;
    padding-right: 1.4rem;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%2394a3b8'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
}
.pos-filter-select:focus,
.pos-filter-select.active-filter {
    border-color: var(--accent);
    color: var(--accent);
}
.pos-filter-clear {
    height: 30px;
    padding: 0 0.75rem;
    font-size: 0.72rem;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    border: 1.5px solid #e74c3c;
    border-radius: 20px;
    background: transparent;
    color: #e74c3c;
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
    transition: all 0.18s;
}
.pos-filter-clear:hover { background: #e74c3c; color: #fff; }

</style>
@endpush

@section('content')

{{-- Hidden form (submission) --}}
<form id="ventaForm" action="{{ route('ventas.store') }}" method="POST">
    @csrf
    <input type="hidden" id="f_cliente_id"       name="cliente_id">
    <input type="hidden" id="f_comprobante_id"    name="comprobante_id" value="{{ $comprobantes->first()?->id }}">
    <input type="hidden" id="f_metodo_pago"       name="metodo_pago"       value="EFECTIVO">
    <input type="hidden" id="f_subtotal"          name="subtotal"          value="0">
    <input type="hidden" id="f_total"             name="total"             value="0">
    <input type="hidden" id="f_monto_recibido"    name="monto_recibido"    value="0">
    <input type="hidden" id="f_vuelto_entregado"  name="vuelto_entregado"  value="0">
    {{-- Product arrays --}}
    <div id="productArrays"></div>
</form>

{{-- POS --}}
<div class="pos-wrapper" id="posWrapper">

    {{-- ══ LEFT: Products ══ --}}
    <div class="pos-products">

        {{-- Top bar --}}
        <div class="pos-topbar">
            {{-- Search + Scanner toggle --}}
            <div class="scan-row">
                <div class="pos-search-wrap">
                    <i class="fas fa-search search-icon" id="searchModeIcon"></i>
                    <input type="text" id="searchInput"
                           placeholder="Buscar por nombre o código... (Atajo: /)"
                           autocomplete="off" spellcheck="false">
                </div>
                <button type="button" class="scan-mode-btn" id="scanModeBtn"
                        title="Activar modo escáner de código de barras">
                    <i class="fas fa-barcode"></i>
                    <span class="d-none d-md-inline">Escanear</span>
                </button>
            </div>
            <div class="scan-feedback" id="scanFeedback"></div>
            {{-- Filter bar: categoría, talla, género, marca, origen --}}
            <div class="pos-filters" id="posFilters">
                {{-- Categoría --}}
                <select class="pos-filter-select" id="filterCategoria" title="Categoría">
                    <option value="">📁 Todas las categorías</option>
                    @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->caracteristica?->nombre ?? 'Sin nombre' }}</option>
                    @endforeach
                </select>

                {{-- Talla --}}
                <select class="pos-filter-select" id="filterTalla" title="Talla">
                    <option value="">📏 Todas las tallas</option>
                </select>

                {{-- Género --}}
                <select class="pos-filter-select" id="filterGenero" title="Género">
                    <option value="">👤 Género</option>
                    <option value="Hombre">👨 Hombre</option>
                    <option value="Mujer">👩 Mujer</option>
                    <option value="Unisex">🤝 Unisex</option>
                </select>

                {{-- Marca --}}
                <select class="pos-filter-select" id="filterMarca" title="Marca">
                    <option value="">🏷️ Todas las marcas</option>
                    @foreach($marcas as $marca)
                    <option value="{{ $marca->id }}">{{ $marca->caracteristica?->nombre ?? 'Sin nombre' }}</option>
                    @endforeach
                </select>

                {{-- Origen --}}
                <select class="pos-filter-select" id="filterOrigen" title="Origen">
                    <option value="">🌍 Nacional / Importada</option>
                    <option value="Nacional">🇨🇴 Nacional</option>
                    <option value="Importada">✈️ Importada</option>
                </select>

                {{-- Limpiar filtros --}}
                <button class="pos-filter-clear" id="btnClearFilters" title="Limpiar filtros" style="display:none">
                    <i class="fas fa-times"></i> Limpiar
                </button>
            </div>
        </div>

        {{-- Body: grid + alpha bar --}}
        <div class="pos-body">
            <div class="product-grid" id="productGrid">
                {{-- Rendered by JS --}}
            </div>
            <div class="alpha-bar" id="alphaBar"></div>
        </div>

    </div>

    {{-- ══ RIGHT: Cart ══ --}}
    <div class="pos-cart" id="cartPanel">

        {{-- Header --}}
        <div class="cart-header">
            <div class="cart-title">
                <i class="fas fa-shopping-bag" style="color:var(--accent);"></i>
                Carrito
                <span class="cart-count-badge" id="cartBadge">0</span>
            </div>
            <button class="cart-clear-btn" id="btnClearCart">
                <i class="fas fa-trash-alt"></i> Limpiar
            </button>
        </div>

        {{-- Items --}}
        <div class="cart-items-wrap" id="cartItemsWrap">
            <div class="cart-empty" id="cartEmpty">
                <i class="fas fa-shopping-bag"></i>
                <p>El carrito está vacío.<br>Selecciona un producto.</p>
            </div>
            <div id="cartItems"></div>
        </div>

        {{-- Footer --}}
        <div class="cart-footer">

            {{-- Total --}}
            <div class="total-row grand" style="padding: 0.2rem 0;">
                <span>TOTAL</span>
                <span class="total-amount" id="displayTotal">$0</span>
            </div>

            {{-- Método de pago --}}
            <div>
                <span class="cart-field-label">Método de pago</span>
                <div class="payment-pills" id="paymentPills">
                    <button type="button" class="pay-pill active" data-method="EFECTIVO">💵 Efectivo</button>
                    <button type="button" class="pay-pill" data-method="VENTA_DIGITAL">📲 Venta Digital</button>
                    <button type="button" class="pay-pill" data-method="FIADO">🤝 Fiado</button>
                </div>
            </div>

            {{-- Cash section --}}
            <div class="cash-section" id="cashSection">
                <div class="cash-suggestions" id="cashSuggestions"></div>
                <div class="cash-row">
                    <span class="cash-label">Recibido</span>
                    <input class="cash-input" id="montoRecibido" type="number" min="0" step="1000" placeholder="Monto...">
                </div>
                <div class="cash-row">
                    <span class="cash-label">Vuelto</span>
                    <input class="cash-input readonly" id="vueltoDisplay" type="text" readonly placeholder="—">
                </div>
            </div>

            {{-- Pay button --}}
            <button type="button" class="btn-pay" id="btnPagar" disabled>
                <i class="fas fa-check-circle"></i>
                <span id="btnPagarLabel">PAGAR</span>
            </button>

        </div>
    </div>

</div>

{{-- ══ MOBILE: bottom bar ══ --}}
<div class="mobile-bottom-bar">
    <div class="mobile-total-info">
        <span class="mobile-total-label">Total</span>
        <span class="mobile-total-amount" id="mobileTotalDisplay">$0</span>
    </div>
    <button class="btn-view-cart" id="btnViewCart">
        <i class="fas fa-shopping-bag"></i>
        Ver carrito
        <span class="cart-count-badge" id="mobileCartBadge">0</span>
    </button>
</div>

{{-- ══ MOBILE: bottom sheet ══ --}}
<div class="cart-sheet-overlay" id="sheetOverlay"></div>
<div class="cart-sheet" id="cartSheet">
    <div class="cart-sheet-handle"></div>
    <button class="cart-sheet-close" id="btnCloseSheet"><i class="fas fa-times"></i></button>
    <div class="cart-header">
        <div class="cart-title"><i class="fas fa-shopping-bag" style="color:var(--accent);"></i> Carrito</div>
    </div>
    <div class="cart-items-wrap" id="sheetCartItems" style="flex:1;overflow-y:auto;padding:0.5rem;"></div>
    <div class="cart-footer" id="sheetCartFooter"></div>
</div>

@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

/* ══════════════════════════════════════
   AUTO-HIDE SIDEBAR EN POS
══════════════════════════════════════ */
(function () {
    const body = document.body;
    // Guardar el estado previo del sidebar antes de ocultarlo
    const wasToggled = body.classList.contains('sb-sidenav-toggled');
    sessionStorage.setItem('pos_sidebar_was_toggled', wasToggled ? '1' : '0');

    // Pequeño delay para que la transición sea visible al entrar
    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            body.classList.add('pos-sidebar-hidden');
            // Alterar la clase nativa para que al interactuar concuerde con el cierre forzado
            if (window.innerWidth >= 992) {
                body.classList.add('sb-sidenav-toggled');
            } else {
                body.classList.remove('sb-sidenav-toggled');
            }
        });
    });

    // Restaurar el comportamiento nativo si se hace clic en la hamburguesa
    const sidebarToggle = document.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            if (body.classList.contains('pos-sidebar-hidden')) {
                body.classList.remove('pos-sidebar-hidden');
            }
        });
    }

    // Restaurar sidebar al salir de la página
    window.addEventListener('pagehide', restoreSidebar);
    window.addEventListener('beforeunload', restoreSidebar);

    function restoreSidebar() {
        body.classList.remove('pos-sidebar-hidden');
        const wasToggled = sessionStorage.getItem('pos_sidebar_was_toggled') === '1';
        if (wasToggled) body.classList.add('sb-sidenav-toggled');
        else body.classList.remove('sb-sidenav-toggled');
    }
})();

/* ══════════════════════════════════════
   DATA: Products from Blade
══════════════════════════════════════ */
@php
// Resolver URL de imagen: preferir la de la variante, si no la del producto
$allProductsData = $productos->map(function($p) {
    $imgPath = $p->variante_img ?: $p->img_path;
    $imgUrl  = '';
    if ($imgPath) {
        if (str_starts_with($imgPath, 'http')) {
            $imgUrl = $imgPath;
        } else {
            $cloudName = config('filesystems.disks.cloudinary.cloud_name')
                      ?: parse_url(env('CLOUDINARY_URL', ''), PHP_URL_HOST);
            $imgUrl = $cloudName
                ? "https://res.cloudinary.com/{$cloudName}/image/upload/{$imgPath}"
                : \Illuminate\Support\Facades\Storage::url($imgPath);
        }
    }
    // Etiqueta de variante para mostrar (talla / color)
    $varLabel = implode(' / ', array_filter([$p->sigla ?: null, $p->color ?: null])) ?: null;
    $productoId = $p->producto_uuid ?? $p->producto_id;
    return [
        'variante_id' => (string)$p->variante_id,
        'id'          => $productoId,      // producto_id (para pivot venta-producto)
        'nombre'      => $p->nombre,
        'codigo'      => $p->codigo ?? '',
        'precio'      => (float)($p->precio ?? 0),
        'stock'       => (int)($p->cantidad ?? 0),
        'img'         => $imgUrl,
        'categoria_id'=> $p->categoria_id,
        'talla'       => $p->sigla ?? '',
        'color'       => $p->color ?? '',
        'var_label'   => $varLabel,        // "M / Negro", "L", "Rojo", etc.
        'genero'      => $p->genero ?? '',
        'origen'      => $p->origen ?? '',
        'marca_id'    => $p->marca_id ?? '',
        'marca_nombre'=> $p->marca_nombre ?? '',
        'producto_id' => $productoId,      // alias explícito para agrupación
    ];
})->sortBy('nombre')->values();
@endphp
const allProducts = @json($allProductsData);
const isAdmin     = {{ $isAdmin ? 'true' : 'false' }};

/* ══════════════════════════════════════
   STATE
══════════════════════════════════════ */
let cart           = [];  // [{id, nombre, precio, cantidad, stock}]
@if(old('arrayidproducto'))
    @foreach(old('arrayidproducto') as $i => $id_val)
        (function() {
            const var_id = '{{ old('arrayvariante_id')[$i] ?? '' }}';
            const prod = allProducts.find(p => p.variante_id == var_id) || allProducts.find(p => p.id == '{{ $id_val }}');
            if (prod) {
                cart.push({
                    id: '{{ $id_val }}',
                    variante_id: var_id,
                    cantidad: parseInt('{{ old('arraycantidad')[$i] ?? 1 }}', 10),
                    precio: parseFloat('{{ old('arrayprecioventa')[$i] ?? 0 }}'),
                    nombre: prod.nombre,
                    stock: prod.stock,
                    img: prod.img,
                    var_label: prod.var_label
                });
            }
        })();
    @endforeach
@endif
let searchQuery    = '';
let paymentMethod  = 'EFECTIVO';
let debounceTimer  = null;
// Filtros adicionales
let filterCategoria = '';
let filterTalla    = '';
let filterGenero   = '';
let filterMarca    = '';
let filterOrigen   = '';

/* ══════════════════════════════════════
   FORMAT helpers
══════════════════════════════════════ */
function fmt(n) {
    return '$' + Math.round(n).toLocaleString('es-CO');
}

/* ══════════════════════════════════════
   PRODUCT RENDERING
══════════════════════════════════════ */
function filteredProducts() {
    let list = allProducts;
    if (filterCategoria) list = list.filter(p => p.categoria_id == filterCategoria);
    if (filterTalla)  list = list.filter(p => p.talla === filterTalla);
    if (filterGenero) list = list.filter(p => p.genero === filterGenero);
    if (filterMarca)  list = list.filter(p => p.marca_id === filterMarca);
    if (filterOrigen) {
        if (filterOrigen === 'Nacional')  list = list.filter(p => p.origen === 'Nacional');
        if (filterOrigen === 'Importada') list = list.filter(p => p.origen === 'Importada');
    }
    if (searchQuery) {
        const q = searchQuery.toLowerCase();
        list = list.filter(p =>
            p.nombre.toLowerCase().includes(q) ||
            (p.codigo && p.codigo.toLowerCase().includes(q))
        );
    }
    return list;
}

function updateClearFiltersBtn() {
    const hasFilter = filterCategoria || filterTalla || filterGenero || filterMarca || filterOrigen;
    const btn = document.getElementById('btnClearFilters');
    if (btn) btn.style.display = hasFilter ? 'inline-flex' : 'none';
    // Mark active
    ['filterCategoria','filterTalla','filterGenero','filterMarca','filterOrigen'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('active-filter', !!el.value);
    });
}

/** Group variantes by producto_id (one card per product, with size/color buttons) */
function buildFamilies(products) {
    const families = new Map();
    products.forEach(p => {
        const key = p.producto_id;
        if (!families.has(key)) families.set(key, []);
        families.get(key).push(p);
    });
    const result = [];
    families.forEach(variants => {
        // Sort by talla then color
        variants.sort((a, b) => {
            const ta = a.talla || 'ZZZ';
            const tb = b.talla || 'ZZZ';
            if (ta !== tb) return ta.localeCompare(tb);
            return (a.color || '').localeCompare(b.color || '');
        });
        result.push(variants);
    });
    return result;
}

function renderProducts() {
    const grid    = document.getElementById('productGrid');
    const alphaEl = document.getElementById('alphaBar');
    const products = filteredProducts();

    if (products.length === 0) {
        grid.innerHTML = `<div class="products-empty">
            <i class="fas fa-vest"></i>
            <p>No se encontraron productos</p>
        </div>`;
        alphaEl.innerHTML = '';
        return;
    }

    const families = buildFamilies(products);

    // Group families by first letter of base name
    const groups = {};
    families.forEach(variants => {
        const baseName = getBaseName(variants[0]);
        const letter = baseName.charAt(0).toUpperCase();
        if (!groups[letter]) groups[letter] = [];
        groups[letter].push(variants);
    });

    // Render grid with letter anchors
    let html = '';
    const letters = Object.keys(groups).sort();
    letters.forEach(letter => {
        html += `<div class="product-letter-anchor" id="anchor-${letter}" data-letter="${letter}">${letter}</div>`;
        groups[letter].forEach(variants => {
            html += variants.length > 1 ? renderFamilyCard(variants) : renderCard(variants[0]);
        });
    });
    grid.innerHTML = html;

    // Render alpha bar
    const allLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');
    alphaEl.innerHTML = allLetters.map(l => {
        const has = letters.includes(l);
        return `<div class="alpha-letter ${has ? 'has-products' : ''}"
                     data-letter="${l}">${l}</div>`;
    }).join('');

    // Single-product card click → add to cart
    grid.querySelectorAll('.product-card[data-product-id]').forEach(card => {
        card.addEventListener('click', function () {
            if (this.classList.contains('out-of-stock')) return;
            addToCart(this.dataset.productId);
            this.classList.add('added');
            setTimeout(() => this.classList.remove('added'), 600);
        });
    });

    // Family size-button click → add specific variant
    grid.querySelectorAll('.size-btn[data-variant-id]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (this.classList.contains('size-out')) return;
            addToCart(this.dataset.variantId);
            this.classList.add('size-added');
            setTimeout(() => this.classList.remove('size-added'), 500);
        });
    });

    // Alpha bar click
    alphaEl.querySelectorAll('.alpha-letter.has-products').forEach(el => {
        el.addEventListener('click', function () {
            scrollToLetter(this.dataset.letter);
        });
    });

    // Alpha bar touch drag
    setupAlphaDrag(alphaEl);
}

/** Base name for the product card (producto.nombre without any suffix) */
function getBaseName(p) {
    return p.nombre;
}

function renderCard(p) {
    const inCart    = cart.find(c => c.variante_id == p.variante_id)?.cantidad || 0;
    const available = p.stock - inCart;
    const outOfStock = available <= 0;

    let stockBadge = '';
    if (outOfStock)        stockBadge = `<span class="stock-badge out">Agotado</span>`;
    else if (p.stock <= 3) stockBadge = `<span class="stock-badge low">${p.stock} ud${p.stock!==1?'s':''}</span>`;
    else                   stockBadge = `<span class="stock-badge ok">${p.stock} uds</span>`;

    const imgHtml = p.img
        ? `<img src="${p.img}" alt="${p.nombre}" loading="lazy">`
        : `<div class="card-img-placeholder"><i class="fas fa-vest"></i></div>`;

    const labelHtml = p.var_label
        ? `<div class="card-product-variant">${p.var_label}</div>`
        : '';

    return `<div class="product-card ${outOfStock ? 'out-of-stock' : ''}"
                 data-product-id="${p.variante_id}"
                 data-nombre="${p.nombre.toLowerCase()}">
        <div class="card-img-wrap">
            ${imgHtml}
            ${stockBadge}
        </div>
        <div class="card-body-sm">
            <div class="card-product-name">${p.nombre}</div>
            ${labelHtml}
            <div class="card-product-price">${fmt(p.precio)}</div>
        </div>
    </div>`;
}

function renderFamilyCard(variants) {
    const main = variants[0];
    const baseName = getBaseName(main);

    const imgHtml = main.img
        ? `<img src="${main.img}" alt="${baseName}" loading="lazy">`
        : `<div class="card-img-placeholder"><i class="fas fa-vest"></i></div>`;

    const sizeBtns = variants.map(v => {
        const inCart   = cart.find(c => c.variante_id == v.variante_id)?.cantidad || 0;
        const avail    = v.stock - inCart;
        const out      = avail <= 0;
        const label    = v.var_label || v.talla || 'T.U.';
        const stockTip = out ? 'Agotado' : `${avail}u`;
        return `<button class="size-btn ${out ? 'size-out' : ''}" data-variant-id="${v.variante_id}" title="${label} — ${stockTip}">
            <span class="size-label">${label}</span>
            <span class="size-stock ${out ? 'out' : avail <= 3 ? 'low' : 'ok'}">${out ? '✕' : stockTip}</span>
        </button>`;
    }).join('');

    return `<div class="product-card family-card" data-nombre="${baseName.toLowerCase()}">
        <div class="card-img-wrap">
            ${imgHtml}
        </div>
        <div class="card-body-sm">
            <div class="card-product-name">${baseName}</div>
            <div class="card-product-price">${fmt(main.precio)}</div>
            <div class="size-picker">${sizeBtns}</div>
        </div>
    </div>`;
}

/* ══════════════════════════════════════
   ALPHA BAR scroll + touch drag
══════════════════════════════════════ */
function scrollToLetter(letter) {
    const anchor = document.getElementById(`anchor-${letter}`);
    if (!anchor) return;
    const grid = document.getElementById('productGrid');
    grid.scrollTo({ top: anchor.offsetTop - grid.scrollTop + grid.scrollTop, behavior: 'smooth' });
    anchor.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Highlight alpha letter
    document.querySelectorAll('.alpha-letter').forEach(el => el.classList.remove('active-letter'));
    const targetLetter = document.querySelector(`.alpha-letter[data-letter="${letter}"]`);
    if (targetLetter) targetLetter.classList.add('active-letter');
}

function setupAlphaDrag(bar) {
    let dragging = false;
    const getLetterAt = (y, rect) => {
        const items = bar.querySelectorAll('.alpha-letter.has-products');
        for (const item of items) {
            const r = item.getBoundingClientRect();
            if (y >= r.top && y <= r.bottom) return item.dataset.letter;
        }
        return null;
    };
    bar.addEventListener('touchstart', e => { dragging = true; }, { passive: true });
    bar.addEventListener('touchmove', e => {
        if (!dragging) return;
        const touch = e.touches[0];
        const letter = getLetterAt(touch.clientY);
        if (letter) scrollToLetter(letter);
    }, { passive: true });
    bar.addEventListener('touchend', () => { dragging = false; });
}

/* ══════════════════════════════════════
   CART LOGIC
══════════════════════════════════════ */
function addToCart(varianteId) {
    const product = allProducts.find(p => p.variante_id == varianteId);
    if (!product) return;

    const item = cart.find(c => c.variante_id == varianteId);
    const currentInCart = item ? item.cantidad : 0;
    if (currentInCart >= product.stock && product.stock > 0) return;

    const label = product.var_label ? `${product.nombre} (${product.var_label})` : product.nombre;

    if (item) {
        item.cantidad++;
    } else {
        cart.push({
            variante_id: product.variante_id,
            id:          product.id,          // producto_id para el pivot
            nombre:      label,
            precio:      product.precio,
            cantidad:    1,
            stock:       product.stock,
        });
    }
    renderCart();
    renderProducts();
    updateFormFields();
}

function changeQty(varianteId, delta) {
    const item = cart.find(c => c.variante_id == varianteId);
    if (!item) return;
    item.cantidad += delta;
    if (item.cantidad <= 0) cart = cart.filter(c => c.variante_id != varianteId);
    renderCart();
    renderProducts();
    updateFormFields();
}

function removeItem(varianteId) {
    const item = cart.find(c => c.variante_id == varianteId);
    if (!item) return;

    Swal.fire({
        title: '¿Eliminar producto?',
        html: `Vas a retirar <b>${item.nombre}</b> del carrito.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#E74C3C',
        cancelButtonColor: '#94A3B8',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        backdrop: `rgba(0,0,0,0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            cart = cart.filter(c => c.variante_id != varianteId);
            renderCart();
            renderProducts();
            updateFormFields();

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: 'Producto eliminado'
            });
        }
    });
}

function clearCart() {
    if (cart.length === 0) return;

    Swal.fire({
        title: '¿Vaciar carrito?',
        text: "Se perderán todos los productos seleccionados.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#E74C3C',
        cancelButtonColor: '#94A3B8',
        confirmButtonText: 'Sí, vaciar carito',
        cancelButtonText: 'Volver',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            cart = [];
            renderCart();
            renderProducts();
            updateFormFields();
            
            Swal.fire({
                title: 'Carrito vacío',
                icon: 'success',
                timer: 1000,
                showConfirmButton: false
            });
        }
    });
}

function getSubtotal() { return cart.reduce((s, i) => s + (i.precio * i.cantidad), 0); }

function updateItemPrice(varianteId, rawValue) {
    const newPrice = parseFloat(rawValue);
    if (isNaN(newPrice) || newPrice < 0) return;
    const item = cart.find(c => c.variante_id == varianteId);
    if (!item) return;
    item.precio = newPrice;
    // Recalcular totales y campos del form sin re-renderizar el carrito
    // (evita perder el foco del input)
    const subtotal = getSubtotal();
    const total    = getTotal();
    document.getElementById('displayTotal').textContent = fmt(total);
    const mobileTotal = document.getElementById('mobileTotalDisplay');
    if (mobileTotal) mobileTotal.textContent = fmt(total);
    // Actualizar el subtotal de esta fila específica
    const items = document.querySelectorAll('#cartItems .cart-item');
    const allItems = cart;
    const idx = allItems.findIndex(c => c.variante_id == varianteId);
    if (idx >= 0 && items[idx]) {
        const subtotalEl = items[idx].querySelector('.cart-item-subtotal');
        if (subtotalEl) subtotalEl.textContent = fmt(newPrice * item.cantidad);
    }
    updateFormFields();
}
function getTotal()    { return getSubtotal(); }

function renderCart() {
    const itemsEl  = document.getElementById('cartItems');
    const emptyEl  = document.getElementById('cartEmpty');
    const badgeEl  = document.getElementById('cartBadge');
    const mobileBadge = document.getElementById('mobileCartBadge');
    const totalCount  = cart.reduce((s, i) => s + i.cantidad, 0);

    badgeEl.textContent = totalCount;
    if (mobileBadge) mobileBadge.textContent = totalCount;

    const subtotal = getSubtotal();
    const total    = getTotal();

    document.getElementById('displayTotal').textContent = fmt(total);
    const mobileTotal = document.getElementById('mobileTotalDisplay');
    if (mobileTotal) mobileTotal.textContent = fmt(total);

    if (cart.length === 0) {
        emptyEl.style.display  = 'flex';
        itemsEl.style.display  = 'none';
        itemsEl.innerHTML      = '';
        updatePayButton();
        return;
    }

    emptyEl.style.display = 'none';
    itemsEl.style.display = 'block';

    itemsEl.innerHTML = cart.map(item => `
        <div class="cart-item">
            <div style="flex:1;min-width:0;">
                <div class="cart-item-name" title="${item.nombre}">${item.nombre}</div>
                ${isAdmin
                    ? `<input type="number" class="cart-item-price-input" min="0" step="0.01"
                              value="${item.precio}"
                              onchange="updateItemPrice('${item.variante_id}', this.value)"
                              title="Editar precio unitario">`
                    : `<div class="cart-item-price">${fmt(item.precio)} / ud</div>`
                }
            </div>
            <div class="qty-controls">
                <button class="qty-btn" onclick="changeQty('${item.variante_id}', -1)">−</button>
                <span class="qty-value">${item.cantidad}</span>
                <button class="qty-btn" onclick="changeQty('${item.variante_id}', +1)"
                        ${item.cantidad >= item.stock ? 'disabled style="opacity:0.3;cursor:not-allowed;"' : ''}>+</button>
            </div>
            <div class="cart-item-subtotal">${fmt(item.precio * item.cantidad)}</div>
            <button class="cart-remove-btn" onclick="removeItem('${item.variante_id}')">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `).join('');

    updateCashSection();
    updatePayButton();

    // Sync mobile sheet if open
    const sheetItems = document.getElementById('sheetCartItems');
    if (sheetItems) sheetItems.innerHTML = itemsEl.innerHTML;
}

/* ══════════════════════════════════════
   PAYMENT
══════════════════════════════════════ */
function setPaymentMethod(method) {
    paymentMethod = method;
    document.getElementById('f_metodo_pago').value = method;

    document.querySelectorAll('.pay-pill').forEach(p => {
        p.classList.toggle('active', p.dataset.method === method);
    });

    const cashSection = document.getElementById('cashSection');
    if (method === 'EFECTIVO') {
        cashSection.classList.add('visible');
        updateCashSection();
    } else {
        cashSection.classList.remove('visible');
        // For digital/fiado: recibido = total, vuelto = 0
        const total = getTotal();
        document.getElementById('f_monto_recibido').value   = total;
        document.getElementById('f_vuelto_entregado').value = 0;
    }

    updatePayButton();
}

function updateCashSection() {
    if (paymentMethod !== 'EFECTIVO') return;
    const total     = getTotal();
    const recibido  = parseFloat(document.getElementById('montoRecibido').value) || 0;
    const vuelto    = recibido > 0 ? Math.max(0, recibido - total) : 0;

    document.getElementById('vueltoDisplay').value        = recibido > 0 ? fmt(vuelto) : '';
    document.getElementById('f_monto_recibido').value     = recibido || total;
    document.getElementById('f_vuelto_entregado').value   = vuelto;

    // Cash suggestions — billetes colombianos + montos redondos inteligentes
    const sugg = document.getElementById('cashSuggestions');
    const steps = [5000, 10000, 20000, 50000, 100000];
    const roundUps = steps.map(r => {
        let n = Math.ceil(total / r) * r;
        return n === total && total > 0 ? n + r : n;
    });
    
    // Si el total es muy alto sumamos también algunos billetes fijos mayores
    const baseBills = [50000, 100000, 150000, 200000];
    
    let all = [...new Set([...baseBills, ...roundUps])];
    all = all.filter(v => v > total).sort((a, b) => a - b).slice(0, 4); // máximo 4 sugerencias
    
    const exactSugg = `<button class="cash-sugg cash-sugg-exact" onclick="setCash(${total})">Exacto<br><small>${fmt(total)}</small></button>`;
    sugg.innerHTML = exactSugg + all.map(v =>
        `<button class="cash-sugg" onclick="setCash(${v})">${fmt(v)}</button>`
    ).join('');
}

window.setCash = function(amount) {
    document.getElementById('montoRecibido').value = amount;
    updateCashSection();
    updatePayButton();
};

document.getElementById('montoRecibido').addEventListener('input', function() {
    updateCashSection();
    updatePayButton();
});

function updatePayButton() {
    const btn      = document.getElementById('btnPagar');
    const label    = document.getElementById('btnPagarLabel');
    const total    = getTotal();
    const hasItems = cart.length > 0;
    let   cashOk   = true;

    if (paymentMethod === 'EFECTIVO') {
        const recibido = parseFloat(document.getElementById('montoRecibido').value) || 0;
        cashOk = recibido >= total && total > 0;
        if (!cashOk && hasItems) {
            label.textContent = recibido < total
                ? `Faltan ${fmt(total - recibido)}`
                : 'PAGAR';
        }
    }

    const canPay = hasItems && (paymentMethod !== 'EFECTIVO' || cashOk) && total > 0;
    btn.disabled = !canPay;
    if (canPay) label.textContent = `PAGAR ${fmt(total)}`;
}

document.getElementById('btnPagar').addEventListener('click', function (e) {
    e.preventDefault();
    const btn = this;
    if (btn.disabled) return;
    if (cart.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Atención', text: 'El carrito está vacío.' });
        return;
    }

    // Asegurar que los campos ocultos estén actualizados
    updateFormFields();

    // Loading state
    btn.disabled = true;
    btn.classList.add('loading');
    const label = document.getElementById('btnPagarLabel');
    const originalLabel = label.textContent;
    label.textContent = 'Procesando...';

    const form = document.getElementById('ventaForm');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(async response => {
        const data = await response.json();
        if (response.ok) {
            // Success!
            // 1. Descontar stock manualmente en JS para actualización instantánea
            cart.forEach(item => {
                const p = allProducts.find(x => x.variante_id == item.variante_id);
                if (p) p.stock -= item.cantidad;
            });

            // 2. Limpiar carrito y resetear UI
            cart = [];
            document.getElementById('montoRecibido').value = '';
            document.getElementById('searchInput').value = '';
            searchQuery = '';

            renderCart();
            renderProducts();
            updateFormFields();

            // 3. Notificación VIP
            Swal.fire({
                icon: 'success',
                title: '¡Venta registrada con éxito!',
                text: 'La transacción se procesó correctamente.',
                confirmButtonText: 'Siguiente venta',
                confirmButtonColor: '#1D96C8',
                timer: 2000,
                timerProgressBar: true,
                showClass: { popup: 'animate__animated animate__fadeInDown' },
                hideClass: { popup: 'animate__animated animate__fadeOutUp' }
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({ icon: 'success', title: 'Venta completada' });

        } else {
            // Error de validación o servidor
            throw new Error(data.error || data.message || 'Error desconocido al procesar la venta.');
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error en la venta',
            text: error.message,
            confirmButtonColor: '#E74C3C'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.classList.remove('loading');
        label.textContent = originalLabel;
        updatePayButton();
    });
});

/* ══════════════════════════════════════
   FORM SUBMISSION
══════════════════════════════════════ */
function updateFormFields() {
    const subtotal = getSubtotal();
    const total    = getTotal();
    document.getElementById('f_subtotal').value = subtotal;
    document.getElementById('f_total').value    = total;

    // Build product arrays
    const container = document.getElementById('productArrays');
    container.innerHTML = '';
    cart.forEach(item => {
        container.innerHTML += `
            <input type="hidden" name="arrayidproducto[]"    value="${item.id}">
            <input type="hidden" name="arrayvariante_id[]"   value="${item.variante_id}">
            <input type="hidden" name="arraycantidad[]"      value="${item.cantidad}">
            <input type="hidden" name="arrayprecioventa[]"   value="${item.precio}">
        `;
    });

    // Digital: set recibido = total
    if (paymentMethod !== 'EFECTIVO') {
        document.getElementById('f_monto_recibido').value   = total;
        document.getElementById('f_vuelto_entregado').value = 0;
    }
    updateCashSection();
    updatePayButton();
}

document.getElementById('btnPagar').addEventListener('click', function () {
    if (this.disabled) return;

    if (cart.length === 0) { alert('El carrito está vacío.'); return; }

    document.getElementById('f_cliente_id').value = '';

    // Asegurar que los campos ocultos estén actualizados antes de enviar
    updateFormFields();

    // Loading state
    this.disabled = true;
    this.classList.add('loading');
    document.getElementById('btnPagarLabel').textContent = 'Procesando...';

    document.getElementById('ventaForm').submit();
});

/* ══════════════════════════════════════
   EVENT LISTENERS
══════════════════════════════════════ */
// Search with debounce (disabled in scanner mode)
document.getElementById('searchInput').addEventListener('input', function () {
    if (scannerMode) return;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        searchQuery = this.value.trim();
        renderProducts();
    }, 300);
});

// Filtros selectores
['filterCategoria','filterTalla','filterGenero','filterMarca','filterOrigen'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('change', function () {
        filterCategoria = document.getElementById('filterCategoria').value;
        filterTalla     = document.getElementById('filterTalla').value;
        filterGenero    = document.getElementById('filterGenero').value;
        filterMarca     = document.getElementById('filterMarca').value;
        filterOrigen    = document.getElementById('filterOrigen').value;
        updateClearFiltersBtn();
        renderProducts();
    });
});

// Limpiar todos los filtros adicionales
document.getElementById('btnClearFilters').addEventListener('click', function () {
    filterCategoria = filterTalla = filterGenero = filterMarca = filterOrigen = '';
    ['filterCategoria','filterTalla','filterGenero','filterMarca','filterOrigen'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.value = ''; el.classList.remove('active-filter'); }
    });
    this.style.display = 'none';
    renderProducts();
});

// Payment pills
document.getElementById('paymentPills').addEventListener('click', function (e) {
    const pill = e.target.closest('.pay-pill');
    if (!pill) return;
    setPaymentMethod(pill.dataset.method);
});

// Clear cart
document.getElementById('btnClearCart').addEventListener('click', clearCart);

// Mobile sheet
document.getElementById('btnViewCart').addEventListener('click', openSheet);
document.getElementById('btnCloseSheet').addEventListener('click', closeSheet);
document.getElementById('sheetOverlay').addEventListener('click', closeSheet);

function openSheet() {
    document.getElementById('sheetOverlay').classList.add('open');
    document.getElementById('cartSheet').classList.add('open');
    // Clone footer into sheet
    const footerSrc = document.querySelector('.pos-cart .cart-footer');
    const sheetFooter = document.getElementById('sheetCartFooter');
    sheetFooter.innerHTML = footerSrc.innerHTML;
    // Re-apply JS to cloned elements (simple: just re-render)
    renderCart();
}
function closeSheet() {
    document.getElementById('sheetOverlay').classList.remove('open');
    document.getElementById('cartSheet').classList.remove('open');
}

/* ══════════════════════════════════════
   KEYBOARD SHORTCUTS
══════════════════════════════════════ */
document.addEventListener('keydown', function (e) {
    if (e.key === '/' && !e.target.matches('input, textarea, select')) {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    if (e.key === 'Escape') {
        if (document.getElementById('cartSheet').classList.contains('open')) {
            closeSheet();
        } else if (document.activeElement === document.getElementById('searchInput')) {
            document.getElementById('searchInput').value = '';
            searchQuery = '';
            renderProducts();
        }
    }
    // F9 = exacto
    if (e.key === 'F9') {
        e.preventDefault();
        if (paymentMethod === 'EFECTIVO') setCash(getTotal());
    }
    // F10 = 10k, F11 = 20k, F12 = 50k
    if (e.key === 'F10') { e.preventDefault(); setCash(10000); }
    if (e.key === 'F11') { e.preventDefault(); setCash(20000); }
    if (e.key === 'F12') { e.preventDefault(); setCash(50000); }
});

/* ══════════════════════════════════════
   BARCODE SCANNER MODE
══════════════════════════════════════ */
let scannerMode   = false;
const scanModeBtn   = document.getElementById('scanModeBtn');
const scanFeedback  = document.getElementById('scanFeedback');
const searchModeIcon = document.getElementById('searchModeIcon');
const searchInputEl  = document.getElementById('searchInput');

scanModeBtn.addEventListener('click', function () {
    scannerMode = !scannerMode;
    this.classList.toggle('active', scannerMode);

    if (scannerMode) {
        searchInputEl.placeholder = 'Escanea el código de barras aquí...';
        searchInputEl.value       = '';
        searchModeIcon.className  = 'fas fa-barcode search-icon';
        searchQuery               = '';
        renderProducts();
        searchInputEl.focus();
        showScanFeedback('Modo escáner activo — escanea el código', 'ok', 2500);
    } else {
        searchInputEl.placeholder = 'Buscar por nombre o código... (Atajo: /)';
        searchModeIcon.className  = 'fas fa-search search-icon';
        hideScanFeedback();
    }
});

function showScanFeedback(msg, type, duration) {
    scanFeedback.textContent = msg;
    scanFeedback.className   = 'scan-feedback show ' + type;
    if (duration > 0) setTimeout(hideScanFeedback, duration);
}
function hideScanFeedback() {
    scanFeedback.className = 'scan-feedback';
}

// Handle barcode Enter in scanner mode
searchInputEl.addEventListener('keydown', function (e) {
    if (!scannerMode || e.key !== 'Enter') return;
    e.preventDefault();

    const code = this.value.trim();
    this.value  = '';
    searchQuery = '';
    if (!code) return;

    const product = allProducts.find(
        p => p.codigo && p.codigo.toLowerCase() === code.toLowerCase()
    );

    if (!product) {
        showScanFeedback('Código no encontrado: ' + code, 'err', 2200);
        return;
    }

    const inCart  = cart.find(c => c.variante_id == product.variante_id)?.cantidad || 0;
    if (product.stock > 0 && inCart >= product.stock) {
        showScanFeedback(product.nombre + ' — Stock agotado en carrito', 'err', 2200);
        return;
    }
    if (product.stock <= 0) {
        showScanFeedback(product.nombre + ' — Sin stock disponible', 'err', 2200);
        return;
    }

    addToCart(product.variante_id);
    showScanFeedback('+ ' + product.nombre, 'ok', 1800);

    // Flash card if visible
    const card = document.querySelector(`.product-card[data-product-id="${product.variante_id}"]`);
    if (card) {
        card.classList.add('added');
        setTimeout(() => card.classList.remove('added'), 600);
    }
});

/* ══════════════════════════════════════
   INIT
══════════════════════════════════════ */
// Expose functions globally for inline onclick
window.addToCart    = addToCart;
window.changeQty    = changeQty;
window.removeItem   = removeItem;

// f_comprobante_id is pre-set in the hidden input via Blade

// Llenar select de tallas dinámicamente
(function initTallas() {
    const filterTalla = document.getElementById('filterTalla');
    if (!filterTalla) return;
    const tallas = new Set();
    allProducts.forEach(p => {
        if (p.talla) tallas.add(p.talla);
    });
    // Ordenar (básico)
    const sortedTallas = Array.from(tallas).sort((a, b) => a.localeCompare(b));
    sortedTallas.forEach(t => {
        const opt = document.createElement('option');
        opt.value = t;
        opt.textContent = t;
        filterTalla.appendChild(opt);
    });
})();

// Set initial payment method
setPaymentMethod('{{ old('metodo_pago', 'EFECTIVO') }}');

// Initial render
renderProducts();
renderCart();

// Confirmación de venta realizada
@if(session('success'))
Swal.fire({
    icon: 'success',
    title: '¡Venta registrada con éxito!',
    text: '{{ session('success') }}',
    confirmButtonText: 'Siguiente venta',
    confirmButtonColor: '#1D96C8',
    timer: 2500,
    timerProgressBar: true,
    showClass: { popup: 'animate__animated animate__fadeInDown' },
    hideClass: { popup: 'animate__animated animate__fadeOutUp' }
});

const ToastSuccess = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
ToastSuccess.fire({
    icon: 'success',
    title: 'Transacción completada'
});
@endif

@if($errors->any())
Swal.fire({
    icon: 'warning',
    title: 'Error de validación',
    html: '{!! implode("<br>", $errors->all()) !!}',
    confirmButtonColor: '#E74C3C',
});
@endif

@if(session('error'))
Swal.fire({
    icon: 'error',
    title: 'Error',
    text: '{{ session('error') }}',
    confirmButtonColor: '#E74C3C',
}).then(function() {
    renderProducts();
    renderCart();
});
@endif

});
</script>
@endpush

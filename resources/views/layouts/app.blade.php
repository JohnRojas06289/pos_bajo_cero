<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" id="htmlRoot">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Bajo Cero — Punto de Venta" />
    <meta name="author" content="Bajo Cero" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') | Bajo Cero</title>

    <!-- Favicon ❄️ -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>❄️</text></svg>">

    <!-- Google Fonts: Inter + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @stack('css-datatable')
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/pos-theme.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/fontawesome.js') }}" crossorigin="anonymous"></script>
    @stack('css')

    <style>
    /* ── GLOBAL POLISH ─────────────────────────────────── */
    html { scroll-behavior: smooth; }

    /* Custom scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(29,150,200,0.4); border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #1D96C8; }

    /* Fade-in on main content */
    main > * { animation: fadeInUp 0.3s ease both; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── AI CHAT WIDGET ───────────────────────────────── */
    #aiChatBtn {
        position: fixed;
        bottom: 24px;
        right: 24px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #1D96C8;
        border: none;
        color: #fff;
        font-size: 1.3rem;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(29,150,200,0.5);
        z-index: 1060;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        animation: pulse-ai 2.5s infinite;
    }
    #aiChatBtn:hover { transform: scale(1.1); box-shadow: 0 6px 28px rgba(29,150,200,0.65); animation: none; }
    @keyframes pulse-ai {
        0%,100% { box-shadow: 0 4px 20px rgba(29,150,200,0.5); }
        50%      { box-shadow: 0 4px 32px rgba(29,150,200,0.8); }
    }

    #aiChatPanel {
        position: fixed;
        bottom: 92px;
        right: 24px;
        width: 380px;
        max-height: 560px;
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #e2e8f0);
        border-radius: 16px;
        box-shadow: 0 16px 48px rgba(0,0,0,0.18);
        display: flex;
        flex-direction: column;
        z-index: 1055;
        overflow: hidden;
        opacity: 0;
        transform: translateY(16px) scale(0.96);
        pointer-events: none;
        transition: opacity 0.22s ease, transform 0.22s ease;
    }
    #aiChatPanel.open {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: all;
    }

    .ai-header {
        padding: 0.875rem 1rem;
        background: linear-gradient(135deg, #1D96C8, #1275A0);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .ai-header-title { font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem; }
    .ai-header-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: #fff;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        transition: background 0.15s;
    }
    .ai-header-close:hover { background: rgba(255,255,255,0.35); }

    .ai-messages {
        flex: 1;
        overflow-y: auto;
        padding: 0.875rem;
        display: flex;
        flex-direction: column;
        gap: 0.625rem;
        min-height: 220px;
    }

    .ai-bubble {
        max-width: 82%;
        padding: 0.55rem 0.8rem;
        border-radius: 12px;
        font-size: 0.83rem;
        line-height: 1.5;
        word-break: break-word;
    }
    .ai-bubble.user {
        align-self: flex-end;
        background: #1D96C8;
        color: #fff;
        border-bottom-right-radius: 3px;
    }
    .ai-bubble.bot {
        align-self: flex-start;
        background: var(--bg-secondary, #f1f5f9);
        color: var(--text-primary, #111);
        border-bottom-left-radius: 3px;
    }
    .ai-bubble.typing {
        padding: 0.6rem 1rem;
    }
    .ai-dots { display: flex; gap: 4px; align-items: center; }
    .ai-dot  { width: 6px; height: 6px; background: var(--text-muted, #94a3b8); border-radius: 50%; animation: dotBounce 1s infinite; }
    .ai-dot:nth-child(2) { animation-delay: 0.15s; }
    .ai-dot:nth-child(3) { animation-delay: 0.30s; }
    @keyframes dotBounce { 0%,80%,100% { transform: translateY(0); } 40% { transform: translateY(-5px); } }

    .ai-chips {
        padding: 0.5rem 0.875rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        border-top: 1px solid var(--border-color, #e2e8f0);
        flex-shrink: 0;
    }
    .ai-chip {
        font-size: 0.73rem;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        border: 1.5px solid var(--border-color, #e2e8f0);
        background: var(--card-bg, #fff);
        color: var(--text-secondary, #64748b);
        cursor: pointer;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .ai-chip:hover { border-color: #1D96C8; color: #1275A0; background: rgba(29,150,200,0.07); }

    .ai-input-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 0.875rem;
        border-top: 1px solid var(--border-color, #e2e8f0);
        flex-shrink: 0;
    }
    .ai-input {
        flex: 1;
        border: 1.5px solid var(--input-border, #e2e8f0);
        border-radius: 24px;
        padding: 0.45rem 1rem;
        font-size: 0.83rem;
        background: var(--input-bg, #fff);
        color: var(--text-primary, #111);
        outline: none;
        transition: border-color 0.2s;
    }
    .ai-input:focus { border-color: #1D96C8; }
    .ai-send {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #1D96C8;
        border: none;
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
        transition: background 0.15s, transform 0.15s;
    }
    .ai-send:hover { background: #D35400; transform: scale(1.08); }
    .ai-send:disabled { background: #ccc; cursor: not-allowed; transform: none; }

    @media (max-width: 480px) {
        #aiChatPanel { width: calc(100vw - 16px); right: 8px; bottom: 88px; max-height: 70vh; }
        #aiChatBtn   { bottom: 16px; right: 16px; }
    }

    /* ── BUTTON LOADING STATE ──────────────────────────── */
    .btn-loading { position: relative; pointer-events: none; opacity: 0.8; }
    .btn-loading::after {
        content: '';
        display: inline-block;
        width: 14px; height: 14px;
        border: 2px solid rgba(255,255,255,0.5);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
        margin-left: 8px;
        vertical-align: middle;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    </style>

    <script>
        // Aplicar tema ANTES de renderizar (evita flash)
        (function () {
            const saved = localStorage.getItem('jacket-theme') || 'light';
            document.documentElement.setAttribute('data-theme', saved);
        })();
    </script>
</head>

<body class="sb-nav-fixed">

    @include('layouts.include.navigation-header')

    <div id="layoutSidenav">
        @include('layouts.include.navigation-menu')
        <div id="layoutSidenav_content">
            @include('layouts.partials.alert')
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         AI CHAT WIDGET
    ════════════════════════════════════════ --}}
    <button id="aiChatBtn" title="Asistente IA">
        <i class="fas fa-comments" id="aiChatIcon"></i>
    </button>

    <div id="aiChatPanel">
        <div class="ai-header">
            <div class="ai-header-title">
                <span>🧥</span> Asistente Bajo Cero
            </div>
            <button class="ai-header-close" id="aiChatClose"><i class="fas fa-times"></i></button>
        </div>
        <div class="ai-messages" id="aiMessages">
            <div class="ai-bubble bot">
                ¡Hola! Soy tu asistente del almacén. Puedo ayudarte con ventas, inventario y el sistema. ¿En qué te ayudo?
            </div>
        </div>
        <div class="ai-chips" id="aiChips">
            @if(auth()->user()?->can('ver-panel'))
                <button class="ai-chip" data-msg="¿Cuánto vendimos hoy?">💰 Ventas hoy</button>
                <button class="ai-chip" data-msg="¿Qué productos tienen stock bajo?">⚠️ Stock bajo</button>
                <button class="ai-chip" data-msg="¿Cuántas reservas están pendientes de atender?">📅 Reservas pendientes</button>
                <button class="ai-chip" data-msg="Resumen del día">📊 Resumen</button>
                <button class="ai-chip" data-msg="¿Cuál es el producto más vendido esta semana?">🏆 Más vendido</button>
            @else
                <button class="ai-chip" data-msg="¿Cómo registro una venta?">🛒 Cómo vender</button>
                <button class="ai-chip" data-msg="¿Cómo abro una caja?">🗄️ Abrir caja</button>
                <button class="ai-chip" data-msg="¿Cuánto llevo vendido hoy?">💵 Mi venta hoy</button>
                <button class="ai-chip" data-msg="¿Hay chaquetas con stock bajo?">📦 Stock bajo</button>
            @endif
        </div>
        <div class="ai-input-row">
            <input class="ai-input" type="text" id="aiInput" placeholder="Escribe tu pregunta..." maxlength="500" autocomplete="off">
            <button class="ai-send" id="aiSend"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        // ── Notificaciones ──
        const notificationIcon = document.getElementById('notificationsDropdown');
        if (notificationIcon) {
            notificationIcon.addEventListener('click', function () {
                fetch("{{ route('notifications.markAsRead') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const badge = notificationIcon.querySelector('.badge');
                        if (badge) badge.remove();
                    }
                })
                .catch(e => console.error('Error notificaciones:', e));
            });
        }

        // ── Theme Toggle ──
        const themeToggle = document.getElementById('themeToggle');
        const htmlRoot    = document.getElementById('htmlRoot');

        function applyTheme(theme) {
            htmlRoot.setAttribute('data-theme', theme);
            localStorage.setItem('jacket-theme', theme);
            if (themeToggle) themeToggle.checked = (theme === 'dark');
            const icon = document.getElementById('themeToggleIcon');
            if (icon) {
                icon.className = theme === 'dark'
                    ? 'fas fa-moon toggle-icon'
                    : 'fas fa-sun toggle-icon';
            }
            const label = document.getElementById('themeToggleLabel');
            if (label) label.textContent = theme === 'dark' ? 'Tema oscuro' : 'Tema claro';
        }

        const savedTheme = localStorage.getItem('jacket-theme') || 'light';
        applyTheme(savedTheme);

        if (themeToggle) {
            themeToggle.addEventListener('change', function () {
                applyTheme(this.checked ? 'dark' : 'light');
            });
        }

        // ── Sidebar: cerrar en móvil al hacer click en enlace o en el overlay ──
        const navLinks = document.querySelectorAll('#layoutSidenav_nav .nav-link:not([data-bs-toggle])');
        navLinks.forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) {
                    document.body.classList.remove('sb-sidenav-toggled');
                }
            });
        });

        // Cerrar sidebar al tocar el overlay (área oscura) en móvil
        const sidenavContent = document.getElementById('layoutSidenav_content');
        if (sidenavContent) {
            sidenavContent.addEventListener('click', function () {
                if (window.innerWidth < 992 && document.body.classList.contains('sb-sidenav-toggled')) {
                    document.body.classList.remove('sb-sidenav-toggled');
                }
            });
        }

        // ── Counter animation for KPI values ──
        function animateEl(el, target, prefix, suffix) {
            const dur = 1100;
            const start = performance.now();
            function step(now) {
                const p   = Math.min((now - start) / dur, 1);
                const eased = 1 - Math.pow(1 - p, 3); // ease-out cubic
                const val = eased * target;
                const formatted = Number.isInteger(target)
                    ? Math.round(val).toLocaleString('es-CO')
                    : val.toFixed(1);
                el.textContent = prefix + formatted + suffix;
                if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }

        function animateCounters() {
            // data-count elements
            document.querySelectorAll('[data-count]:not([data-animated])').forEach(el => {
                el.dataset.animated = '1';
                const target = parseFloat(el.dataset.count.replace(/[^0-9.]/g, ''));
                if (!isNaN(target)) animateEl(el, target, el.dataset.prefix || '', el.dataset.suffix || '');
            });
            // Auto-detect .db-kpi-value elements with numeric content
            document.querySelectorAll('.db-kpi-value:not([data-animated])').forEach(el => {
                el.dataset.animated = '1';
                const raw = el.textContent.trim();
                const prefix = raw.startsWith('$') ? '$' : '';
                const numStr = raw.replace(/[^0-9]/g, '');
                const target = parseInt(numStr, 10);
                if (!isNaN(target) && target > 0) animateEl(el, target, prefix, '');
            });
        }

        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver(entries => {
                entries.forEach(e => { if (e.isIntersecting) { animateCounters(); obs.disconnect(); } });
            }, { threshold: 0.05 });
            const first = document.querySelector('[data-count], .db-kpi-value');
            if (first) obs.observe(first);
        } else {
            setTimeout(animateCounters, 200);
        }

        // ── Global form submit loading state ──
        document.querySelectorAll('form:not([data-no-loading])').forEach(form => {
            form.addEventListener('submit', function () {
                const btn = this.querySelector('[type="submit"]');
                if (btn && !btn.classList.contains('btn-loading')) {
                    btn.classList.add('btn-loading');
                    const original = btn.innerHTML;
                    btn.disabled = true;
                    // Reset after 8s as safety net
                    setTimeout(() => {
                        btn.classList.remove('btn-loading');
                        btn.disabled = false;
                        btn.innerHTML = original;
                    }, 8000);
                }
            });
        });

        // ══════════════════════════════════════
        //  AI CHAT WIDGET
        // ══════════════════════════════════════
        const chatBtn    = document.getElementById('aiChatBtn');
        const chatPanel  = document.getElementById('aiChatPanel');
        const chatClose  = document.getElementById('aiChatClose');
        const chatInput  = document.getElementById('aiInput');
        const chatSend   = document.getElementById('aiSend');
        const messagesEl = document.getElementById('aiMessages');
        const chips      = document.querySelectorAll('.ai-chip');
        const CHAT_KEY   = 'jacket_ai_history';
        const CHAT_ROUTE = "{{ route('agente.ia.chat') }}";
        const CSRF       = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Load persisted messages
        function loadHistory() {
            try {
                const saved = sessionStorage.getItem(CHAT_KEY);
                if (saved) {
                    const msgs = JSON.parse(saved);
                    // Clear default greeting if we have history
                    messagesEl.innerHTML = '';
                    msgs.forEach(m => appendBubble(m.role, m.text, false));
                }
            } catch(e) {}
        }

        function saveHistory() {
            try {
                const bubbles = messagesEl.querySelectorAll('.ai-bubble:not(.typing)');
                const msgs = [];
                bubbles.forEach(b => {
                    const role = b.classList.contains('user') ? 'user' : 'bot';
                    msgs.push({ role, text: b.textContent });
                });
                if (msgs.length > 30) msgs.splice(0, msgs.length - 30);
                sessionStorage.setItem(CHAT_KEY, JSON.stringify(msgs));
            } catch(e) {}
        }

        function appendBubble(role, text, save = true) {
            const div = document.createElement('div');
            div.className = 'ai-bubble ' + role;
            div.textContent = text;
            messagesEl.appendChild(div);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            if (save) saveHistory();
            return div;
        }

        function showTyping() {
            const div = document.createElement('div');
            div.className = 'ai-bubble bot typing';
            div.id = 'aiTyping';
            div.innerHTML = '<div class="ai-dots"><div class="ai-dot"></div><div class="ai-dot"></div><div class="ai-dot"></div></div>';
            messagesEl.appendChild(div);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            return div;
        }

        async function sendMessage(text) {
            if (!text.trim() || chatSend.disabled) return;
            appendBubble('user', text);
            chatInput.value = '';
            chatSend.disabled = true;
            const typing = showTyping();

            try {
                const res = await fetch(CHAT_ROUTE, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: text })
                });
                const data = await res.json();
                typing.remove();
                appendBubble('bot', data.reply || data.error || 'Sin respuesta.');
            } catch(e) {
                typing.remove();
                appendBubble('bot', 'Error de conexión. Verifica tu red.');
            } finally {
                chatSend.disabled = false;
                chatInput.focus();
            }
        }

        // Toggle panel
        chatBtn.addEventListener('click', () => {
            const isOpen = chatPanel.classList.toggle('open');
            chatBtn.querySelector('i').className = isOpen ? 'fas fa-times' : 'fas fa-comments';
            if (isOpen) {
                loadHistory();
                chatInput.focus();
            }
        });

        chatClose.addEventListener('click', () => {
            chatPanel.classList.remove('open');
            chatBtn.querySelector('i').className = 'fas fa-comments';
        });

        // Send on button click
        chatSend.addEventListener('click', () => sendMessage(chatInput.value));

        // Send on Enter
        chatInput.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage(chatInput.value);
            }
        });

        // Chips — send message but keep panel open
        chips.forEach(chip => {
            chip.addEventListener('click', () => {
                sendMessage(chip.dataset.msg);
            });
        });

    });
    </script>

    @stack('js')

</body>
</html>

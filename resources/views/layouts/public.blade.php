<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="@yield('meta_description', 'Bajo Cero — Ropa urbana de montaña. Chaquetas, gorras y prendas para desafiar el frío en Colombia.')" />
    <meta name="author" content="Bajo Cero" />
    <meta property="og:title"       content="@yield('title', 'Bajo Cero | Ropa Urbana de Montaña')" />
    <meta property="og:description" content="@yield('meta_description', 'Bajo Cero — Ropa urbana de montaña. Chaquetas, gorras y prendas para desafiar el frío en Colombia.')" />
    <meta property="og:type"        content="website" />
    <meta property="og:image"       content="{{ asset('images/logo-bajo-cero.png') }}" />
    <meta name="twitter:card"       content="summary_large_image" />
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bajo-cero.png') }}">
    <title>@yield('title', 'Bajo Cero | Ropa Urbana de Montaña')</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Custom CSS (cache-busted) -->
    <link href="{{ asset('css/bajocero.css') }}?v={{ time() }}" rel="stylesheet" />
    @stack('css')
    <script>
        // Init Theme
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar-custom fixed-top">
        <div class="nav-container">
            <a class="navbar-brand" href="{{ route('home') }}" style="display:flex;align-items:center;gap:0.5rem;">
                <img src="{{ asset('images/logo-bajo-cero.png') }}" alt="Bajo Cero" style="height:36px;width:auto;object-fit:contain;">
                <span>Bajo<span style="color: var(--primary-color);">Cero</span></span>
            </a>

            <div class="nav-actions">
                <!-- Theme Toggle -->
                <button class="theme-toggle-btn" id="themeToggle" title="Cambiar Tema">
                    <i class="fas fa-moon"></i>
                </button>

                <!-- Cart -->
                <a href="#" class="cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-badge">0</span>
                </a>

                <!-- Hamburger Toggle (always visible) -->
                <button class="hamburger-btn" id="hamburgerBtn" type="button" aria-label="Abrir menú">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Backdrop -->
    <div id="menuBackdrop" class="menu-backdrop"></div>

    <!-- Slide Menu Panel -->
    <div id="slideMenu" class="slide-menu">
        <div class="slide-menu-header">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <img src="{{ asset('images/logo-bajo-cero.png') }}" alt="Bajo Cero" style="height:30px;width:auto;object-fit:contain;filter:var(--logo-filter);">
                <span style="color: var(--primary-color); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; font-size: 1.1rem;">
                    Bajo<span style="color: var(--text-color);">Cero</span>
                </span>
            </div>
            <button id="menuCloseBtn" class="btn-close-menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="slide-menu-body">
            <ul class="navbar-nav-custom">
                <li><a class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fas fa-home me-2"></i>Inicio</a></li>
                <li><a class="nav-link-custom {{ request()->routeIs('collection') ? 'active' : '' }}" href="{{ route('collection') }}"><i class="fas fa-th-large me-2"></i>Colección</a></li>
                <li><a class="nav-link-custom {{ request()->routeIs('reservar.*') ? 'active' : '' }}" href="{{ route('reservar.index') }}"><i class="fas fa-calendar-check me-2"></i>Reservar</a></li>
                <li><a class="nav-link-custom {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}"><i class="fas fa-envelope me-2"></i>Contacto</a></li>
                <li><a class="nav-link-custom {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}"><i class="fas fa-info-circle me-2"></i>Nosotros</a></li>
            </ul>
            <hr style="border-color: var(--card-border); margin: 0.75rem 0;">
            <a href="{{ route('login.index') }}" class="btn-login d-block text-center" style="font-size:0.75rem;">
                <i class="fas fa-sign-in-alt me-2"></i> Acceder al panel
            </a>
        </div>
    </div>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="container px-5">
            <div class="row gx-5">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <div class="footer-heading">Bajo Cero</div>
                    <p class="text-muted mb-4">Ropa urbana inspirada en la montaña. Chaquetas, gorras y prendas para quienes desafían el frío. Estilo, calidad y actitud colombiana.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-tiktok fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-5 mb-lg-0">
                    <div class="footer-heading">Explorar</div>
                    <div class="footer-links">
                        <a href="{{ route('home') }}">Inicio</a>
                        <a href="{{ route('collection') }}">Catálogo</a>
                        <a href="{{ route('about') }}">Nosotros</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mb-5 mb-lg-0">
                    <div class="footer-heading">Contacto</div>
                    <div class="footer-links">
                        <div class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i> Bogotá, Colombia</div>
                        <div class="mb-2"><i class="fab fa-whatsapp me-2 text-success"></i> +57 300 123 4567</div>
                        <div class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i> contacto@bajocero.co</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="footer-heading">Pagos Seguros</div>
                    <p class="text-muted small">Procesamos tus pagos con la máxima seguridad.</p>
                    <div class="d-flex gap-3 text-white fs-4">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5 pt-4" style="border-top: 1px solid rgba(255,255,255,0.08);">
                <small style="color: rgba(255,255,255,0.3); font-size: 0.75rem;">&copy; 2026 Bajo Cero. Todos los derechos reservados.</small>
            </div>
        </div>
    </footer>

    <!-- Whatsapp Floating Button -->
    <a href="https://wa.me/{{ env('WHATSAPP_NUMERO','573001234567') }}" target="_blank" title="Escríbenos por WhatsApp" class="wsp-float"
       style="position:fixed;bottom:24px;right:24px;background:#25d366;color:white;width:52px;height:52px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;z-index:1000;transition:transform 0.2s ease,box-shadow 0.2s ease;"
       onmouseenter="this.style.transform='scale(1.08)'"
       onmouseleave="this.style.transform='scale(1)'">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
    
    <script>
        // Theme Toggle
        (function() {
            const themeToggleBtn = document.getElementById('themeToggle');
            const themeIcon = themeToggleBtn.querySelector('i');
            const rootElement = document.documentElement;

            function updateIcon(theme) {
                if (theme === 'light') {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                } else {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
            }

            updateIcon(localStorage.getItem('theme') || 'dark');

            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = rootElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                rootElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcon(newTheme);
            });
        })();

        // Snow effect on hero
        (function() {
            const hero = document.querySelector('.hero-section');
            if (!hero) return;
            const wrap = document.createElement('div');
            wrap.className = 'snow-wrap';
            hero.prepend(wrap);
            const symbols = ['❄', '❅', '❆', '·', '*'];
            for (let i = 0; i < 22; i++) {
                const s = document.createElement('span');
                s.className = 'snowflake-pub';
                s.textContent = symbols[Math.floor(Math.random() * symbols.length)];
                s.style.left     = Math.random() * 100 + '%';
                s.style.fontSize = (Math.random() * 14 + 8) + 'px';
                s.style.opacity  = (Math.random() * 0.4 + 0.2).toString();
                s.style.animationDuration = (Math.random() * 8 + 5) + 's';
                s.style.animationDelay    = (Math.random() * 8) + 's';
                wrap.appendChild(s);
            }
        })();

        // Scroll-triggered fade-in-up
        (function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); } });
            }, { threshold: 0.12 });
            document.querySelectorAll('.fade-in-up').forEach(el => observer.observe(el));
        })();

        // Slide Menu Toggle (pure JS)
        (function() {
            const openBtn = document.getElementById('hamburgerBtn');
            const closeBtn = document.getElementById('menuCloseBtn');
            const backdrop = document.getElementById('menuBackdrop');
            const menu = document.getElementById('slideMenu');

            function open() {
                if (menu) menu.classList.add('open');
                if (backdrop) backdrop.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
            function close() {
                if (menu) menu.classList.remove('open');
                if (backdrop) backdrop.classList.remove('open');
                document.body.style.overflow = '';
            }

            if (openBtn) openBtn.addEventListener('click', (e) => { e.preventDefault(); open(); });
            if (closeBtn) closeBtn.addEventListener('click', (e) => { e.preventDefault(); close(); });
            if (backdrop) backdrop.addEventListener('click', close);
            document.addEventListener('keydown', (e) => { if(e.key === 'Escape') close(); });
        })();
    </script>
</body>
</html>

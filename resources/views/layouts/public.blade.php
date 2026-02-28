<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Bajo Cero - Estilo Urbano" />
    <meta name="author" content="Bajo Cero" />
    <title>@yield('title', 'Bajo Cero | Estilo Urbano')</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Custom CSS (cache-busted) -->
    <link href="{{ asset('css/bajocero.css') }}?v={{ time() }}" rel="stylesheet" />
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
            <a class="navbar-brand" href="{{ route('home') }}">
                Bajo<span style="color: var(--text-color);">Cero</span>
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
                <i class="fas fa-snowflake" style="color: var(--primary-color); font-size: 1rem; opacity: 0.8;"></i>
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
                    <p class="text-muted mb-4">Redefiniendo el estilo urbano. Prendas diseñadas para quienes no temen destacar. Calidad premium, diseño exclusivo.</p>
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
                        <div class="mb-2"><i class="fas fa-envelope me-2 text-primary"></i> contacto@bajocero.com</div>
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
    <a href="https://wa.me/573001234567" target="_blank" title="Escríbenos por WhatsApp"
       style="position:fixed;bottom:24px;right:24px;background:#25d366;color:white;width:52px;height:52px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;z-index:1000;box-shadow:0 4px 16px rgba(37,211,102,0.35);transition:transform 0.2s ease,box-shadow 0.2s ease;"
       onmouseenter="this.style.transform='scale(1.08)';this.style.boxShadow='0 6px 20px rgba(37,211,102,0.45)'"
       onmouseleave="this.style.transform='scale(1)';this.style.boxShadow='0 4px 16px rgba(37,211,102,0.35)'">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
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

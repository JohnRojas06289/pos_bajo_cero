<!DOCTYPE html>
<html lang="es" style="overflow-x: hidden;">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Bajo Cero - Estilo Urbano" />
    <meta name="author" content="Bajo Cero" />
    <title>@yield('title', 'Bajo Cero | Estilo Urbano')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="{{ asset('css/bajocero.css') }}?v={{ time() }}" rel="stylesheet" />
    <script>
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>
    <style>
        /* CRITICAL: Slide menu inline styles — cannot be overridden by external CSS or caching */
        #slideMenu {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 300px !important;
            max-width: 85vw !important;
            background-color: #111827 !important;
            z-index: 9999 !important;
            transform: translateX(100%) !important;
            transition: transform 0.35s ease !important;
            overflow-y: auto !important;
            box-shadow: -4px 0 20px rgba(0,0,0,0.3) !important;
            border-left: 1px solid #333 !important;
            display: block !important;
            visibility: visible !important;
        }
        [data-theme="light"] #slideMenu {
            background-color: #ffffff !important;
        }
        #slideMenu.open {
            transform: translateX(0%) !important;
        }
        #menuBackdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(0,0,0,0.5) !important;
            z-index: 9998 !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: opacity 0.3s ease, visibility 0.3s ease !important;
            display: block !important;
        }
        #menuBackdrop.open {
            opacity: 1 !important;
            visibility: visible !important;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav style="position:fixed;top:0;left:0;right:0;z-index:1050;background-color:var(--navbar-bg);border-bottom:1px solid var(--card-border);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);">
        <div style="display:flex;flex-wrap:nowrap;align-items:center;justify-content:space-between;padding:0.75rem 1rem;">
            <a class="navbar-brand" href="{{ route('home') }}" style="flex-shrink:0;text-decoration:none;font-weight:800;font-size:1.4rem;color:var(--primary-color);text-transform:uppercase;letter-spacing:2px;">
                Bajo<span style="color:var(--text-color);">Cero</span>
            </a>
            <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                <!-- Theme Toggle -->
                <button class="theme-toggle-btn" id="themeToggle" title="Cambiar Tema">
                    <i class="fas fa-moon"></i>
                </button>
                <!-- Cart -->
                <a href="#" class="cart-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-badge">0</span>
                </a>
                <!-- Hamburger -->
                <button id="menuToggleBtn" type="button" aria-label="Abrir menú"
                    style="display:flex;visibility:visible;opacity:1;background:transparent;border:2px solid var(--primary-color);color:var(--primary-color);padding:0.45rem 0.6rem;border-radius:8px;cursor:pointer;font-size:1.1rem;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Backdrop -->
    <div id="menuBackdrop"></div>

    <!-- Slide Menu Panel -->
    <div id="slideMenu">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid var(--card-border);">
            <span style="color:var(--primary-color);font-weight:800;text-transform:uppercase;letter-spacing:2px;font-size:1.25rem;">
                Bajo<span style="color:var(--text-color);">Cero</span>
            </span>
            <button id="menuCloseBtn" style="background:transparent;border:1px solid var(--card-border);color:var(--text-muted);padding:0.4rem 0.6rem;border-radius:8px;cursor:pointer;font-size:1rem;">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div style="padding:1.5rem;">
            <ul style="list-style:none;padding:0;margin:0;">
                <li style="margin-bottom:0.25rem;"><a class="nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><i class="fas fa-home me-2"></i>Inicio</a></li>
                <li style="margin-bottom:0.25rem;"><a class="nav-link-custom {{ request()->routeIs('collection') ? 'active' : '' }}" href="{{ route('collection') }}"><i class="fas fa-th-large me-2"></i>Colección</a></li>
                <li style="margin-bottom:0.25rem;"><a class="nav-link-custom {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}"><i class="fas fa-envelope me-2"></i>Contacto</a></li>
                <li style="margin-bottom:0.25rem;"><a class="nav-link-custom {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}"><i class="fas fa-info-circle me-2"></i>Nosotros</a></li>
            </ul>
            <hr style="border-color:var(--card-border);">
            <a href="{{ route('login.index') }}" class="btn-login d-block text-center">
                <i class="fas fa-user me-2"></i> Login
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
            <div class="text-center mt-5 pt-4 border-top border-secondary">
                <small class="text-muted">&copy; 2026 Bajo Cero. Todos los derechos reservados.</small>
            </div>
        </div>
    </footer>

    <!-- Whatsapp Floating Button -->
    <a href="https://wa.me/573001234567" target="_blank" style="position:fixed;bottom:20px;right:20px;background-color:#25d366;color:white;width:60px;height:60px;border-radius:50%;text-align:center;line-height:60px;font-size:30px;z-index:1000;box-shadow:0 4px 10px rgba(0,0,0,0.5);">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Theme Toggle
        (function(){
            var btn = document.getElementById('themeToggle');
            var icon = btn.querySelector('i');
            var root = document.documentElement;
            function setIcon(t) {
                icon.className = t === 'light' ? 'fas fa-sun' : 'fas fa-moon';
            }
            setIcon(localStorage.getItem('theme') || 'dark');
            btn.addEventListener('click', function() {
                var cur = root.getAttribute('data-theme');
                var next = cur === 'dark' ? 'light' : 'dark';
                root.setAttribute('data-theme', next);
                localStorage.setItem('theme', next);
                setIcon(next);
            });
        })();

        // Slide Menu Toggle (pure JS)
        (function(){
            var openBtn = document.getElementById('menuToggleBtn');
            var closeBtn = document.getElementById('menuCloseBtn');
            var backdrop = document.getElementById('menuBackdrop');
            var menu = document.getElementById('slideMenu');

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

            if (openBtn) openBtn.addEventListener('click', function(e){ e.preventDefault(); open(); });
            if (closeBtn) closeBtn.addEventListener('click', function(e){ e.preventDefault(); close(); });
            if (backdrop) backdrop.addEventListener('click', close);
            document.addEventListener('keydown', function(e){ if(e.key==='Escape') close(); });
        })();
    </script>
</body>
</html>

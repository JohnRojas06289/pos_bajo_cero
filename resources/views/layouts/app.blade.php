<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" id="htmlRoot">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Jacket Store — Punto de Venta" />
    <meta name="author" content="Jacket Store" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jacket Store | @yield('title')</title>

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

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
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

            // Inicializar desde localStorage
            const savedTheme = localStorage.getItem('jacket-theme') || 'light';
            applyTheme(savedTheme);

            if (themeToggle) {
                themeToggle.addEventListener('change', function () {
                    applyTheme(this.checked ? 'dark' : 'light');
                });
            }

            // ── Sidebar: cerrar en móvil al hacer click en enlace ──
            const navLinks = document.querySelectorAll('#layoutSidenav_nav .nav-link:not([data-bs-toggle])');
            navLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        document.body.classList.remove('sb-sidenav-toggled');
                    }
                });
            });

        });
    </script>

    @stack('js')

</body>
</html>

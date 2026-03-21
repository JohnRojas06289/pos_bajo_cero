<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Bajo Cero — Punto de Venta" />
    <title>Bajo Cero — Iniciar Sesión</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --glacier:  #1D96C8;
            --mountain: #1B4F72;
            --deep:     #060E1C;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow: hidden;
            position: relative;
            background: var(--deep);
        }

        /* Mountain gradient background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 70% at 50% 110%, rgba(27,79,114,0.9) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 0% 0%,   rgba(29,150,200,0.15) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 100% 0%, rgba(29,150,200,0.10) 0%, transparent 45%),
                linear-gradient(180deg, #03080F 0%, #060E1C 40%, #0A1628 100%);
            pointer-events: none;
            z-index: 0;
        }

        /* Mountain silhouette */
        body::after {
            content: '';
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 45vh;
            background:
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%230A1628' fill-opacity='0.95' d='M0,320L60,288L120,256L180,224L240,192L300,160L360,192L420,224L480,192L540,160L600,128L660,96L720,64L780,96L840,128L900,160L960,128L1020,96L1080,128L1140,160L1200,192L1260,224L1320,256L1380,288L1440,320Z'/%3E%3C/svg%3E") bottom/cover no-repeat,
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23060E1C' fill-opacity='0.8' d='M0,320L80,304L160,288L240,272L320,256L400,240L480,224L560,208L640,160L720,128L800,160L880,200L960,224L1040,240L1120,256L1200,272L1280,288L1360,304L1440,320Z'/%3E%3C/svg%3E") bottom/cover no-repeat;
            pointer-events: none;
            z-index: 0;
        }

        /* Snow particles */
        .snow-container {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .snowflake {
            position: absolute;
            top: -10px;
            color: rgba(255,255,255,0.6);
            font-size: 1em;
            animation: fall linear infinite;
        }

        @keyframes fall {
            0%   { top: -10px; opacity: 1; }
            100% { top: 100vh; opacity: 0.2; }
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        /* ── Brand ── */
        .brand-area {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .brand-logo {
            width: 90px;
            height: 90px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            filter: drop-shadow(0 4px 24px rgba(29,150,200,0.35));
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-name {
            font-size: 2rem;
            font-weight: 800;
            color: #F8FAFC;
            letter-spacing: -0.03em;
            line-height: 1;
            margin: 0;
        }

        .brand-name .accent { color: var(--glacier); }

        .brand-tagline {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.35);
            font-weight: 600;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            margin-top: 0.5rem;
        }

        /* ── Card ── */
        .login-card {
            background: rgba(10, 22, 40, 0.82);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255,255,255,0.07);
            border-top-color: rgba(29,150,200,0.25);
            border-radius: 20px;
            padding: 2rem 2rem 1.75rem;
            box-shadow: 0 32px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(29,150,200,0.08);
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #F1F5F9;
            margin-bottom: 0.2rem;
        }

        .card-subtitle {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.32);
            margin-bottom: 1.75rem;
        }

        /* ── Form ── */
        .form-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: rgba(255,255,255,0.42);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
            display: block;
        }

        .form-control {
            background: rgba(6, 14, 28, 0.75) !important;
            border: 1.5px solid rgba(255,255,255,0.08) !important;
            border-radius: 10px !important;
            color: #F1F5F9 !important;
            padding: 0.7rem 0.9rem !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            height: 44px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }

        .form-control::placeholder { color: rgba(255,255,255,0.18) !important; }

        .form-control:focus {
            border-color: var(--glacier) !important;
            box-shadow: 0 0 0 3px rgba(29,150,200,0.18) !important;
            outline: none !important;
        }

        .pw-group { position: relative; }
        .pw-group .form-control { padding-right: 2.75rem !important; }

        .pw-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255,255,255,0.22);
            cursor: pointer;
            padding: 0;
            line-height: 1;
            transition: color 0.2s;
            z-index: 5;
            font-size: 0.85rem;
        }
        .pw-toggle:hover { color: rgba(255,255,255,0.55); }

        .field-group { margin-bottom: 1rem; }

        /* ── Submit ── */
        .btn-login {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, var(--glacier) 0%, var(--mountain) 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: opacity 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 18px rgba(29,150,200,0.38);
            margin-top: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn-login:hover {
            opacity: 0.92;
            box-shadow: 0 6px 24px rgba(29,150,200,0.5);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); opacity: 1; }

        /* ── Error ── */
        .alert-error {
            background: rgba(231,76,60,0.1);
            border: 1px solid rgba(231,76,60,0.2);
            border-left: 3px solid #E74C3C;
            border-radius: 8px;
            color: #fca5a5;
            font-size: 0.84rem;
            padding: 0.65rem 0.875rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* ── Footer ── */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        .login-footer a {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.22);
            text-decoration: none;
            transition: color 0.2s;
        }
        .login-footer a:hover { color: var(--glacier); }

        .card-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin: 1.5rem 0 0;
        }

        /* Ice dividers */
        .ice-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(29,150,200,0.12);
            border: 1px solid rgba(29,150,200,0.2);
            color: var(--glacier);
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 999px;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 480px) {
            .login-wrapper { max-width: 100%; }
            .login-card { padding: 1.5rem; border-radius: 16px; }
            .brand-logo { width: 72px; height: 72px; }
        }
    </style>
</head>

<body>
    <!-- Snow effect -->
    <div class="snow-container" id="snowContainer"></div>

    <div class="login-wrapper">

        <!-- Brand -->
        <div class="brand-area">
            <div class="brand-logo">
                <img src="{{ asset('images/logo-bajo-cero.png') }}" alt="Bajo Cero">
            </div>
            <div class="ice-badge"><i class="fas fa-snowflake"></i> Sistema POS</div>
            <h1 class="brand-name">Bajo<span class="accent">Cero</span></h1>
            <p class="brand-tagline">Punto de Venta · Colombia</p>
        </div>

        <!-- Card -->
        <div class="login-card">
            <p class="card-title">Iniciar sesión</p>
            <p class="card-subtitle">Ingresa tus credenciales para continuar</p>

            @if ($errors->any())
                @foreach ($errors->all() as $item)
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle flex-shrink-0"></i>{{ $item }}
                </div>
                @endforeach
            @endif

            <form action="{{ route('login.login') }}" method="post">
                @csrf

                <!-- Email -->
                <div class="field-group">
                    <label for="inputEmail" class="form-label">Correo electrónico</label>
                    <input autofocus autocomplete="email" class="form-control"
                           name="email" id="inputEmail" type="email"
                           placeholder="correo@ejemplo.com" />
                </div>

                <!-- Password -->
                <div class="field-group">
                    <label for="inputPassword" class="form-label">Contraseña</label>
                    <div class="pw-group">
                        <input class="form-control" name="password" id="inputPassword"
                               type="password" placeholder="••••••••" />
                        <button class="pw-toggle" type="button" id="togglePassword" title="Mostrar contraseña">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button class="btn-login" type="submit">
                    <i class="fas fa-mountain"></i>
                    Entrar al sistema
                </button>
            </form>

            <hr class="card-divider">
        </div>

        <div class="login-footer">
            <a href="{{ route('home') }}">
                <i class="fas fa-arrow-left me-1"></i>Volver a la tienda
            </a>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('inputPassword');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        // Snow effect
        (function() {
            const container = document.getElementById('snowContainer');
            const symbols = ['❄', '❅', '❆', '·', '*'];
            for (let i = 0; i < 28; i++) {
                const s = document.createElement('span');
                s.className = 'snowflake';
                s.textContent = symbols[Math.floor(Math.random() * symbols.length)];
                s.style.left   = Math.random() * 100 + 'vw';
                s.style.fontSize = (Math.random() * 14 + 8) + 'px';
                s.style.opacity = (Math.random() * 0.4 + 0.2).toString();
                s.style.animationDuration = (Math.random() * 8 + 6) + 's';
                s.style.animationDelay    = (Math.random() * 8) + 's';
                container.appendChild(s);
            }
        })();
    </script>
</body>
</html>

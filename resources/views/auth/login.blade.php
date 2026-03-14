<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Jacket Store — Punto de Venta" />
    <title>Jacket Store — Iniciar Sesión</title>

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
            --accent:   #E67E22;
            --primary:  #2C3E50;
            --bg-body:  #0F0F1A;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg-body);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        /* Geometric background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 70% 60% at 0% 0%,   rgba(44,62,80,0.6)  0%, transparent 55%),
                radial-gradient(ellipse 60% 50% at 100% 100%, rgba(230,126,34,0.12) 0%, transparent 55%),
                radial-gradient(ellipse 40% 40% at 50% 50%,  rgba(26,26,46,0.8)  0%, transparent 80%);
            pointer-events: none;
            z-index: 0;
        }

        /* Subtle grid pattern */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
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

        .brand-icon {
            width: 68px;
            height: 68px;
            background: linear-gradient(135deg, var(--primary) 0%, #1A252F 100%);
            border: 1px solid rgba(230,126,34,0.35);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.9rem;
            color: var(--accent);
            margin-bottom: 1rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4), 0 0 0 1px rgba(230,126,34,0.15);
        }

        .brand-name {
            font-size: 1.8rem;
            font-weight: 800;
            color: #F8FAFC;
            letter-spacing: -0.03em;
            line-height: 1;
            margin: 0;
        }

        .brand-name .accent { color: var(--accent); }

        .brand-tagline {
            font-size: 0.72rem;
            color: rgba(255,255,255,0.35);
            font-weight: 600;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            margin-top: 0.5rem;
        }

        /* ── Card ── */
        .login-card {
            background: rgba(22, 22, 38, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.07);
            border-top-color: rgba(230,126,34,0.2);
            border-radius: 20px;
            padding: 2rem 2rem 1.75rem;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5);
        }

        .card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #F1F5F9;
            margin-bottom: 0.2rem;
        }

        .card-subtitle {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.35);
            margin-bottom: 1.75rem;
        }

        /* ── Form ── */
        .form-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: rgba(255,255,255,0.45);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
            display: block;
        }

        .form-control {
            background: rgba(10, 10, 20, 0.7) !important;
            border: 1.5px solid rgba(255,255,255,0.09) !important;
            border-radius: 10px !important;
            color: #F1F5F9 !important;
            padding: 0.7rem 0.9rem !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            height: 44px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }

        .form-control::placeholder { color: rgba(255,255,255,0.2) !important; }

        .form-control:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(230,126,34,0.15) !important;
            outline: none !important;
            background: rgba(10, 10, 20, 0.9) !important;
        }

        /* Password */
        .pw-group { position: relative; }
        .pw-group .form-control { padding-right: 2.75rem !important; }

        .pw-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255,255,255,0.25);
            cursor: pointer;
            padding: 0;
            line-height: 1;
            transition: color 0.2s;
            z-index: 5;
            font-size: 0.85rem;
        }

        .pw-toggle:hover { color: rgba(255,255,255,0.6); }

        .field-group { margin-bottom: 1rem; }

        /* ── Submit ── */
        .btn-login {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, var(--accent) 0%, #D35400 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: opacity 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 18px rgba(230,126,34,0.4);
            margin-top: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            opacity: 0.92;
            box-shadow: 0 6px 24px rgba(230,126,34,0.5);
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

        /* ── Footer link ── */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-footer a {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.25);
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-footer a:hover { color: rgba(255,255,255,0.5); }

        /* Divider */
        .card-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.07);
            margin: 1.5rem 0 0;
        }

        @media (max-width: 480px) {
            .login-wrapper { max-width: 100%; }
            .login-card { padding: 1.5rem; border-radius: 16px; }
            .brand-icon { width: 56px; height: 56px; font-size: 1.5rem; }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">

        <!-- Brand -->
        <div class="brand-area">
            <div class="brand-icon">
                <i class="fas fa-vest"></i>
            </div>
            <h1 class="brand-name">Jacket<span class="accent">Store</span></h1>
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
                           placeholder="correo@ejemplo.com" value="ventas@gmail.com" />
                </div>

                <!-- Password -->
                <div class="field-group">
                    <label for="inputPassword" class="form-label">Contraseña</label>
                    <div class="pw-group">
                        <input class="form-control" name="password" id="inputPassword"
                               type="password" placeholder="••••••••" value="12345678" />
                        <button class="pw-toggle" type="button" id="togglePassword" title="Mostrar contraseña">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <button class="btn-login" type="submit">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    Entrar al sistema
                </button>
            </form>

            <hr class="card-divider">
        </div>

        <div class="login-footer">
            <a href="{{ route('home') }}">
                <i class="fas fa-arrow-left me-1"></i>Volver al inicio
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
    </script>
</body>
</html>

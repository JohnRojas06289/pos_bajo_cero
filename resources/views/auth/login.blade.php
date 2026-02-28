<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistema POS - Bajo Cero" />
    <title>Bajo Cero — Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Subtle background pattern */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 80% at 20% 0%, rgba(37,99,235,0.15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 60% at 80% 100%, rgba(245,158,11,0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }

        /* Brand */
        .brand-area {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: #93c5fd;
            margin-bottom: 1rem;
            box-shadow: 0 8px 24px rgba(37,99,235,0.3);
        }

        .brand-name {
            font-size: 1.875rem;
            font-weight: 800;
            color: #f8fafc;
            letter-spacing: -0.03em;
            margin: 0;
            line-height: 1;
        }

        .brand-name span {
            color: #60a5fa;
        }

        .brand-tagline {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 0.35rem;
        }

        /* Card */
        .login-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 24px 60px rgba(0,0,0,0.4);
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 0.25rem;
        }

        .card-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 1.75rem;
        }

        /* Form */
        .form-label {
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            color: #94a3b8 !important;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 0.4rem !important;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.7) !important;
            border: 1.5px solid rgba(255,255,255,0.1) !important;
            border-radius: 10px !important;
            color: #f1f5f9 !important;
            padding: 0.65rem 0.875rem !important;
            font-size: 0.925rem !important;
            font-weight: 500 !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }

        .form-control::placeholder { color: #475569 !important; }

        .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15) !important;
            outline: none !important;
            background: rgba(15, 23, 42, 0.9) !important;
        }

        /* Password group */
        .pw-group {
            position: relative;
        }

        .pw-group .form-control {
            padding-right: 2.75rem !important;
        }

        .pw-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #475569;
            cursor: pointer;
            padding: 0.25rem;
            line-height: 1;
            transition: color 0.2s;
            z-index: 5;
        }

        .pw-toggle:hover { color: #94a3b8; }

        /* Submit button */
        .btn-login {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 700;
            font-size: 0.925rem;
            letter-spacing: 0.02em;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(37,99,235,0.35);
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 6px 20px rgba(37,99,235,0.45);
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Error alert */
        .alert-error {
            background: rgba(220,38,38,0.1);
            border: 1px solid rgba(220,38,38,0.25);
            border-left: 3px solid #dc2626;
            border-radius: 8px;
            color: #fca5a5;
            font-size: 0.845rem;
            padding: 0.65rem 0.875rem;
            margin-bottom: 1.25rem;
        }

        /* Footer link */
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-footer a {
            font-size: 0.8rem;
            color: #475569;
            text-decoration: none;
            transition: color 0.2s;
        }

        .login-footer a:hover { color: #64748b; }

        /* Field group spacing */
        .field-group {
            margin-bottom: 1.125rem;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <!-- Brand -->
        <div class="brand-area">
            <div class="brand-icon">
                <i class="fas fa-snowflake"></i>
            </div>
            <h1 class="brand-name">Bajo<span>Cero</span></h1>
            <p class="brand-tagline">Sistema POS</p>
        </div>

        <!-- Card -->
        <div class="login-card">
            <p class="card-title">Iniciar sesión</p>
            <p class="card-subtitle">Ingresa tus credenciales para continuar</p>

            @if ($errors->any())
            @foreach ($errors->all() as $item)
            <div class="alert-error">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $item }}
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
                    <i class="fas fa-arrow-right-to-bracket me-2"></i>Entrar al sistema
                </button>
            </form>
        </div>

        <div class="login-footer">
            <a href="{{ route('home') }}">
                <i class="fas fa-arrow-left me-1"></i>Volver al inicio
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('inputPassword');
            const icon = document.getElementById('eyeIcon');
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

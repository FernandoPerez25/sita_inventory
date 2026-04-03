<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — SITA Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Source+Code+Pro:wght@400;500&family=Hind:wght@300;400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --bg: #0a0e17;
            --surface: #111827;
            --border: #1e2d45;
            --accent: #f59e0b;
            --text: #e2e8f0;
            --muted: #64748b;
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Hind', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(30, 45, 69, 0.4) 1px, transparent 1px),
                linear-gradient(90deg, rgba(30, 45, 69, 0.4) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* Glow effect */
        body::after {
            content: '';
            position: fixed;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 400px;
            background: radial-gradient(ellipse, rgba(245, 158, 11, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            padding: 1rem;
            animation: fadeIn 0.4s ease both;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: var(--accent);
            color: #000;
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.4rem;
            font-weight: 700;
            clip-path: polygon(10% 0%, 90% 0%, 100% 10%, 100% 90%, 90% 100%, 10% 100%, 0% 90%, 0% 10%);
            margin-bottom: 0.75rem;
        }

        .brand-name {
            font-family: 'Rajdhani', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--text);
        }

        .brand-sub {
            font-family: 'Source Code Pro', monospace;
            font-size: 0.68rem;
            color: var(--muted);
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-top: 0.15rem;
        }

        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }

        .login-card-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .login-card-title {
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text);
        }

        .login-card-subtitle {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 0.15rem;
        }

        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-label {
            display: block;
            font-family: 'Source Code Pro', monospace;
            font-size: 0.68rem;
            font-weight: 500;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.6rem 0.85rem;
            font-size: 0.9rem;
            border-radius: 4px;
            font-family: 'Hind', sans-serif;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.12);
        }

        .form-error {
            color: var(--danger);
            font-size: 0.75rem;
            margin-top: 0.3rem;
            font-family: 'Source Code Pro', monospace;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .form-check input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: var(--accent);
            cursor: pointer;
        }

        .form-check label {
            font-size: 0.82rem;
            color: var(--muted);
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 0.7rem;
            background: var(--accent);
            color: #000;
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
        }

        .btn-login:hover {
            background: #fbbf24;
        }

        .btn-login:active {
            transform: scale(0.99);
        }

        .error-box {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-left: 3px solid var(--danger);
            border-radius: 4px;
            padding: 0.7rem 0.9rem;
            color: #f87171;
            font-size: 0.82rem;
            margin-bottom: 1.25rem;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-family: 'Source Code Pro', monospace;
            font-size: 0.65rem;
            color: var(--muted);
            letter-spacing: 0.05em;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        {{-- Brand --}}
        <div class="login-brand">
            <div class="brand-badge">SI</div>
            <div class="brand-name">SITA INVENTORY</div>
            <div class="brand-sub">Sistema de Control de Inventario</div>
        </div>

        {{-- Card --}}
        <div class="login-card">
            <div class="login-card-header">
                <div class="login-card-title">Iniciar Sesión</div>
                <div class="login-card-subtitle">Accede con tus credenciales asignadas</div>
            </div>

            {{-- Error general --}}
            @if($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ url('/login') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="username">Usuario</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        value="{{ old('username') }}"
                        placeholder="username o correo"
                        autocomplete="username"
                        autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="••••••••"
                        autocomplete="current-password">
                </div>

                <!-- <div class="form-check">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Mantener sesión iniciada</label>
                </div> -->

                <button type="submit" class="btn-login">Ingresar al Sistema</button>
            </form>
        </div>

        <div class="login-footer">
            SITA INVENTORY SYSTEM &nbsp;·&nbsp; {{ date('Y') }} &nbsp;·&nbsp; v1.0.0
        </div>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SITA Inventory')</title>

    <!-- Fonts: Rajdhani (display) + Source Code Pro (mono data) + Hind (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Source+Code+Pro:wght@400;500&family=Hind:wght@300;400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sita-bg: #0a0e17;
            --sita-surface: #111827;
            --sita-surface2: #1a2236;
            --sita-border: #1e2d45;
            --sita-primary: #1d4ed8;
            --sita-accent: #f59e0b;
            --sita-accent2: #fbbf24;
            --sita-text: #e2e8f0;
            --sita-muted: #64748b;
            --sita-success: #10b981;
            --sita-danger: #ef4444;
            --sita-warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Hind', sans-serif;
            background-color: var(--sita-bg);
            color: var(--sita-text);
            min-height: 100vh;
        }

        .font-display {
            font-family: 'Rajdhani', sans-serif;
        }

        .font-mono {
            font-family: 'Source Code Pro', monospace;
        }

        /* Sidebar */
        #sidebar {
            background: var(--sita-surface);
            border-right: 1px solid var(--sita-border);
            width: 260px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 40;
            transition: transform 0.25s ease;
        }

        .sidebar-logo {
            padding: 1.5rem;
            border-bottom: 1px solid var(--sita-border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-badge {
            background: var(--sita-accent);
            color: #000;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            clip-path: polygon(10% 0%, 90% 0%, 100% 10%, 100% 90%, 90% 100%, 10% 100%, 0% 90%, 0% 10%);
        }

        .nav-section-label {
            font-family: 'Source Code Pro', monospace;
            font-size: 0.65rem;
            letter-spacing: 0.15em;
            color: var(--sita-muted);
            padding: 1.25rem 1.25rem 0.4rem;
            text-transform: uppercase;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.25rem;
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 400;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.15s ease;
            margin: 0.1rem 0;
        }

        .nav-item:hover {
            color: var(--sita-text);
            background: var(--sita-surface2);
            border-left-color: var(--sita-border);
        }

        .nav-item.active {
            color: var(--sita-accent);
            background: rgba(245, 158, 11, 0.08);
            border-left-color: var(--sita-accent);
            font-weight: 500;
        }

        .nav-item svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            opacity: 0.7;
        }

        .nav-item.active svg {
            opacity: 1;
        }

        /* Main content */
        #main {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        #topbar {
            height: 56px;
            background: var(--sita-surface);
            border-bottom: 1px solid var(--sita-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        /* Page content */
        .page-content {
            padding: 1.75rem 2rem;
            flex: 1;
        }

        /* Page header */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.75rem;
            gap: 1rem;
        }

        .page-title {
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--sita-text);
            line-height: 1.1;
        }

        .page-subtitle {
            font-size: 0.82rem;
            color: var(--sita-muted);
            margin-top: 0.2rem;
            font-family: 'Source Code Pro', monospace;
        }

        /* Cards */
        .card {
            background: var(--sita-surface);
            border: 1px solid var(--sita-border);
            border-radius: 6px;
        }

        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--sita-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 4px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            font-family: 'Hind', sans-serif;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--sita-primary);
            color: #fff;
            border-color: var(--sita-primary);
        }

        .btn-primary:hover {
            background: #1e40af;
        }

        .btn-accent {
            background: var(--sita-accent);
            color: #000;
            border-color: var(--sita-accent);
            font-weight: 600;
        }

        .btn-accent:hover {
            background: var(--sita-accent2);
        }

        .btn-ghost {
            background: transparent;
            color: var(--sita-text);
            border-color: var(--sita-border);
        }

        .btn-ghost:hover {
            background: var(--sita-surface2);
        }

        .btn-danger {
            background: transparent;
            color: var(--sita-danger);
            border-color: var(--sita-danger);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .btn-sm {
            padding: 0.3rem 0.7rem;
            font-size: 0.78rem;
        }

        /* Form controls */
        .form-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--sita-muted);
            margin-bottom: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-family: 'Source Code Pro', monospace;
        }

        .form-control {
            width: 100%;
            background: var(--sita-bg);
            border: 1px solid var(--sita-border);
            color: var(--sita-text);
            padding: 0.5rem 0.75rem;
            font-size: 0.88rem;
            border-radius: 4px;
            font-family: 'Hind', sans-serif;
            transition: border-color 0.15s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--sita-accent);
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.15);
        }

        .form-control::placeholder {
            color: var(--sita-muted);
        }

        select.form-control {
            cursor: pointer;
        }

        .form-error {
            color: var(--sita-danger);
            font-size: 0.78rem;
            margin-top: 0.3rem;
        }

        /* Table */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 6px;
            border: 1px solid var(--sita-border);
        }

        table.sita-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .sita-table thead th {
            background: var(--sita-surface2);
            color: var(--sita-muted);
            font-family: 'Source Code Pro', monospace;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.7rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--sita-border);
            white-space: nowrap;
        }

        .sita-table tbody tr {
            border-bottom: 1px solid rgba(30, 45, 69, 0.6);
            transition: background 0.1s ease;
        }

        .sita-table tbody tr:hover {
            background: rgba(26, 34, 54, 0.7);
        }

        .sita-table tbody tr:last-child {
            border-bottom: none;
        }

        .sita-table tbody td {
            padding: 0.7rem 1rem;
            color: var(--sita-text);
            vertical-align: middle;
        }

        .sita-table .td-mono {
            font-family: 'Source Code Pro', monospace;
            font-size: 0.8rem;
            color: #93c5fd;
        }

        /* Status badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.6rem;
            border-radius: 3px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-family: 'Source Code Pro', monospace;
        }

        .badge-instalado {
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-spare {
            background: rgba(59, 130, 246, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .badge-bodega {
            background: rgba(148, 163, 184, 0.1);
            color: #94a3b8;
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .badge-danado {
            background: rgba(239, 68, 68, 0.12);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .badge-gap {
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        /* Alerts */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 4px;
            font-size: 0.86rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            border-left: 3px solid;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.08);
            border-color: var(--sita-success);
            color: #34d399;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.08);
            border-color: var(--sita-danger);
            color: #f87171;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.08);
            border-color: var(--sita-warning);
            color: #fbbf24;
        }

        /* Grid layout helpers */
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.25rem;
        }

        /* Scanline texture overlay for atmosphere */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: repeating-linear-gradient(0deg,
                    transparent,
                    transparent 2px,
                    rgba(0, 0, 0, 0.03) 2px,
                    rgba(0, 0, 0, 0.03) 4px);
            pointer-events: none;
            z-index: 9999;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--sita-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--sita-border);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--sita-muted);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.3s ease both;
        }

        .delay-1 {
            animation-delay: 0.05s;
        }

        .delay-2 {
            animation-delay: 0.10s;
        }

        .delay-3 {
            animation-delay: 0.15s;
        }

        .delay-4 {
            animation-delay: 0.20s;
        }
    </style>

    @stack('styles')
</head>

<body class="h-full">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside id="sidebar">
        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="logo-badge">SI</div>
            <div>
                <div class="font-display" style="font-size:1.1rem;font-weight:700;color:var(--sita-text);line-height:1.1">SITA</div>
                <div style="font-size:0.65rem;color:var(--sita-muted);font-family:'Source Code Pro',monospace;letter-spacing:0.1em">INVENTORY v1.0</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav style="flex:1;overflow-y:auto;padding-bottom:1rem">

            <div class="nav-section-label">Principal</div>

            <a href="{{ route('dashboard') }}"
                class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('inventario.index') }}"
                class="nav-item {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" />
                </svg>
                Inventario
            </a>

            @if(auth()->user()->puedeEditar())
            <a href="{{ route('importar.index') }}"
                class="nav-item {{ request()->routeIs('importar.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                </svg>
                Carga Masiva
            </a>
            @endif

            <a href="{{ route('reportes.index') }}"
                class="nav-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Reportes
            </a>

            @if(auth()->user()->esAdmin())
            <div class="nav-section-label" style="margin-top:0.5rem">Administración</div>

            <a href="{{ route('catalogos.index') }}"
                class="nav-item {{ request()->routeIs('catalogos.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Catálogos
            </a>

            <a href="{{ route('usuarios.index') }}"
                class="nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Usuarios
            </a>
            @endif
        </nav>

        {{-- User info --}}
        <div style="border-top:1px solid var(--sita-border);padding:1rem 1.25rem;display:flex;align-items:center;gap:0.75rem">
            <div style="width:32px;height:32px;background:var(--sita-surface2);border:1px solid var(--sita-border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Rajdhani',sans-serif;font-weight:700;font-size:0.85rem;color:var(--sita-accent);flex-shrink:0">
                {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
            </div>
            <div style="min-width:0;flex:1">
                <div style="font-size:0.82rem;font-weight:500;color:var(--sita-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ auth()->user()->nombre_completo }}
                </div>
                <div style="font-size:0.68rem;color:var(--sita-muted);font-family:'Source Code Pro',monospace;text-transform:uppercase">
                    {{ auth()->user()->rol->nombre }}
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Cerrar sesión"
                    style="background:none;border:none;cursor:pointer;color:var(--sita-muted);padding:0.25rem;transition:color 0.15s">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══ MAIN ═══ --}}
    <div id="main">

        {{-- Topbar --}}
        <header id="topbar">
            <div style="display:flex;align-items:center;gap:0.75rem">
                {{-- Breadcrumb --}}
                <span style="font-family:'Source Code Pro',monospace;font-size:0.75rem;color:var(--sita-muted)">
                    @yield('breadcrumb', 'SITA / Dashboard')
                </span>
            </div>
            <div style="display:flex;align-items:center;gap:1rem">
                {{-- Live indicator --}}
                <div style="display:flex;align-items:center;gap:0.4rem;font-family:'Source Code Pro',monospace;font-size:0.7rem;color:var(--sita-success)">
                    <span style="width:6px;height:6px;background:var(--sita-success);border-radius:50%;display:inline-block;animation:pulse 2s infinite"></span>
                    SISTEMA ACTIVO
                </div>
                {{-- Current date --}}
                <span id="clock" style="font-family:'Source Code Pro',monospace;font-size:0.75rem;color:var(--sita-muted)"></span>
            </div>
        </header>

        {{-- Page --}}
        <main class="page-content">
            {{-- Flash messages --}}
            @if(session('success'))
            <div class="alert alert-success animate-in">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-error animate-in">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ session('error') }}
            </div>
            @endif
            @if(session('warning'))
            <div class="alert alert-warning animate-in">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ session('warning') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Reloj en tiempo real
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleString('es-MX', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
        updateClock();
        setInterval(updateClock, 1000);

        // CSRF para fetch/ajax
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    </script>

    @stack('scripts')
</body>

</html>
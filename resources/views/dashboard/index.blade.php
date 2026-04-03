@extends('layouts.app')

@section('title', 'Dashboard — SITA Inventory')
@section('breadcrumb', 'SITA / Dashboard')

@section('content')
<div class="page-header animate-in">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">// Resumen general del inventario</p>
    </div>
    <a href="{{ route('inventario.create') }}" class="btn btn-accent">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        Nuevo Dispositivo
    </a>
</div>

{{-- ── KPI Cards ────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.75rem">

    <div class="card animate-in delay-1" style="border-top:3px solid var(--sita-accent)">
        <div class="card-body" style="padding:1.25rem">
            <div style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.5rem">Total Dispositivos</div>
            <div style="font-family:'Rajdhani',sans-serif;font-size:2.2rem;font-weight:700;color:var(--sita-text);line-height:1">
                {{ number_format($totalDispositivos) }}
            </div>
            <div style="font-size:0.75rem;color:var(--sita-muted);margin-top:0.4rem">En todos los sitios</div>
        </div>
    </div>

    @foreach($porStatus->take(3) as $st)
    @php
    $colorMap = ['Instalado'=>'#10b981','Spare'=>'#3b82f6','Bodega'=>'#64748b','Dañado'=>'#ef4444','GAP'=>'#f59e0b'];
    $color = $colorMap[$st->status] ?? '#64748b';
    @endphp
    <div class="card animate-in delay-{{ $loop->iteration + 1 }}" style="border-top:3px solid {{ $color }}">
        <div class="card-body" style="padding:1.25rem">
            <div style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.5rem">{{ $st->status }}</div>
            <div style="font-family:'Rajdhani',sans-serif;font-size:2.2rem;font-weight:700;color:{{ $color }};line-height:1">
                {{ number_format($st->total) }}
            </div>
            <div style="font-size:0.75rem;color:var(--sita-muted);margin-top:0.4rem">
                {{ round($st->total / max($totalDispositivos, 1) * 100, 1) }}% del total
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Charts Row ───────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.75rem">

    {{-- Por Sitio --}}
    <div class="card animate-in delay-2">
        <div class="card-header">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:600;font-size:0.95rem">Dispositivos por Sitio</span>
            <span style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted)">POR AEROPUERTO</span>
        </div>
        <div class="card-body" style="padding:1.25rem">
            @foreach($porSitio as $sitio)
            @php $pct = round($sitio->total / max($totalDispositivos, 1) * 100, 1); @endphp
            <div style="margin-bottom:0.9rem">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem">
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        <span style="font-family:'Source Code Pro',monospace;font-size:0.75rem;font-weight:600;color:var(--sita-accent);background:rgba(245,158,11,0.1);padding:0.1rem 0.4rem;border-radius:2px">{{ $sitio->clave }}</span>
                        <span style="font-size:0.82rem;color:var(--sita-text)">{{ $sitio->nombre_sitio }}</span>
                    </div>
                    <span style="font-family:'Source Code Pro',monospace;font-size:0.75rem;color:var(--sita-muted)">{{ $sitio->total }}</span>
                </div>
                <div style="height:5px;background:var(--sita-border);border-radius:3px;overflow:hidden">
                    <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#1d4ed8,#3b82f6);border-radius:3px;transition:width 1s ease"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Por Dispositivo --}}
    <div class="card animate-in delay-3">
        <div class="card-header">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:600;font-size:0.95rem">Tipos de Dispositivo</span>
            <span style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted)">TOP TIPOS</span>
        </div>
        <div class="card-body" style="padding:1.25rem">
            @foreach($porDispositivo->take(8) as $disp)
            @php $pct = round($disp->total / max($totalDispositivos, 1) * 100, 1); @endphp
            <div style="margin-bottom:0.9rem">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.3rem">
                    <span style="font-family:'Source Code Pro',monospace;font-size:0.78rem;color:var(--sita-text)">{{ $disp->tipo }}</span>
                    <span style="font-family:'Source Code Pro',monospace;font-size:0.75rem;color:var(--sita-muted)">{{ $disp->total }} ({{ $pct }}%)</span>
                </div>
                <div style="height:5px;background:var(--sita-border);border-radius:3px;overflow:hidden">
                    <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#f59e0b,#fbbf24);border-radius:3px"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Últimos registros ─────────────────────────────── --}}
<div class="card animate-in delay-4">
    <div class="card-header">
        <span style="font-family:'Rajdhani',sans-serif;font-weight:600;font-size:0.95rem">Últimos Registros</span>
        <a href="{{ route('inventario.index') }}" class="btn btn-ghost btn-sm">Ver todo →</a>
    </div>
    <div class="table-wrapper" style="border:none;border-radius:0">
        <table class="sita-table">
            <thead>
                <tr>
                    <th>Serial</th>
                    <th>Dispositivo</th>
                    <th>Sitio / Ubicación</th>
                    <th>Marca / Modelo</th>
                    <th>Status</th>
                    <th>Registrado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ultimosRegistros as $item)
                <tr>
                    <td>
                        <a href="{{ route('inventario.show', $item) }}"
                            style="color:#93c5fd;font-family:'Source Code Pro',monospace;font-size:0.8rem;text-decoration:none">
                            {{ $item->serial_number }}
                        </a>
                    </td>
                    <td style="font-family:'Source Code Pro',monospace;font-size:0.78rem">{{ $item->dispositivo->tipo }}</td>
                    <td>
                        <span style="font-size:0.8rem">{{ $item->sitio->clave }}</span>
                        <span style="color:var(--sita-muted);font-size:0.75rem"> / {{ $item->ubicacion->nombre }}</span>
                    </td>
                    <td style="font-size:0.82rem">{{ $item->marca->nombre }} {{ $item->modelo->numero_modelo }}</td>
                    <td>
                        @php
                        $badgeMap = ['Instalado'=>'badge-instalado','Spare'=>'badge-spare','Bodega'=>'badge-bodega','Dañado'=>'badge-danado','GAP'=>'badge-gap'];
                        $badge = $badgeMap[$item->status->nombre] ?? 'badge-bodega';
                        @endphp
                        <span class="badge {{ $badge }}">{{ $item->status->nombre }}</span>
                    </td>
                    <td style="font-size:0.78rem;color:var(--sita-muted)">{{ $item->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
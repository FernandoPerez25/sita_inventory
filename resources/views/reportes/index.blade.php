@extends('layouts.app')

@section('title', 'Reportes — SITA')
@section('breadcrumb', 'SITA / Reportes')

@section('content')
<div class="page-header animate-in">
    <div>
        <h1 class="page-title">Reportes</h1>
        <p class="page-subtitle">// Estadísticas y exportación del inventario</p>
    </div>
</div>

{{-- ── KPIs ─────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;margin-bottom:1.75rem">
    <div class="card animate-in delay-1" style="border-top:3px solid var(--sita-accent)">
        <div class="card-body" style="padding:1.1rem">
            <div style="font-family:'Source Code Pro',monospace;font-size:0.62rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.4rem">Total</div>
            <div style="font-family:'Rajdhani',sans-serif;font-size:2rem;font-weight:700;color:var(--sita-text)">{{ number_format($totalDispositivos) }}</div>
        </div>
    </div>
    @foreach($porStatus as $st)
    @php
    $colorMap = ['Instalado'=>'#10b981','Spare'=>'#3b82f6','Bodega'=>'#64748b','Dañado'=>'#ef4444','GAP'=>'#f59e0b'];
    $color = $colorMap[$st->status] ?? '#64748b';
    @endphp
    <div class="card animate-in delay-{{ $loop->iteration + 1 }}" style="border-top:3px solid {{ $color }}">
        <div class="card-body" style="padding:1.1rem">
            <div style="font-family:'Source Code Pro',monospace;font-size:0.62rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.4rem">{{ $st->status }}</div>
            <div style="font-family:'Rajdhani',sans-serif;font-size:2rem;font-weight:700;color:{{ $color }}">{{ number_format($st->total) }}</div>
            <div style="font-size:0.72rem;color:var(--sita-muted)">{{ round($st->total / max($totalDispositivos,1) * 100, 1) }}%</div>
        </div>
    </div>
    @endforeach
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.75rem">

    {{-- Tabla por sitio --}}
    <div class="card animate-in delay-2">
        <div class="card-header">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Resumen por Sitio</span>
        </div>
        <div class="table-wrapper" style="border:none;border-radius:0">
            <table class="sita-table">
                <thead>
                    <tr>
                        <th>Sitio</th>
                        <th>Nombre</th>
                        <th style="text-align:right">Dispositivos</th>
                        <th style="text-align:right">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($porSitio as $s)
                    <tr>
                        <td><span class="badge badge-gap font-mono">{{ $s->clave }}</span></td>
                        <td style="font-size:0.85rem">{{ $s->nombre_sitio }}</td>
                        <td style="text-align:right;font-family:'Source Code Pro',monospace;font-size:0.82rem">{{ number_format($s->total) }}</td>
                        <td style="text-align:right;font-size:0.78rem;color:var(--sita-muted)">{{ round($s->total / max($totalDispositivos,1) * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Tabla por dispositivo --}}
    <div class="card animate-in delay-3">
        <div class="card-header">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Resumen por Dispositivo</span>
        </div>
        <div class="table-wrapper" style="border:none;border-radius:0">
            <table class="sita-table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th style="text-align:right">Total</th>
                        <th style="text-align:right">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($porDispositivo as $d)
                    <tr>
                        <td style="font-family:'Source Code Pro',monospace;font-size:0.82rem;color:#c4b5fd">{{ $d->tipo }}</td>
                        <td style="text-align:right;font-family:'Source Code Pro',monospace;font-size:0.82rem">{{ number_format($d->total) }}</td>
                        <td style="text-align:right;font-size:0.78rem;color:var(--sita-muted)">{{ round($d->total / max($totalDispositivos,1) * 100, 1) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Exportar ─────────────────────────────────────────── --}}
<div class="card animate-in delay-4">
    <div class="card-header">
        <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Exportar Inventario</span>
        <span style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted)">XLSX CON FORMATO SITA</span>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reportes.exportar') }}"
            style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end">

            <div style="min-width:160px">
                <label class="form-label">Sitio</label>
                <select name="sitio" class="form-control">
                    <option value="">Todos</option>
                    @foreach($sitios as $s)
                    <option value="{{ $s->id }}">{{ $s->clave }} — {{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width:150px">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Todos</option>
                    @foreach($statuses as $st)
                    <option value="{{ $st->id }}">{{ $st->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width:150px">
                <label class="form-label">Dispositivo</label>
                <select name="dispositivo" class="form-control">
                    <option value="">Todos</option>
                    @foreach($dispositivos as $d)
                    <option value="{{ $d->id }}">{{ $d->tipo }}</option>
                    @endforeach
                </select>
            </div>

            <div style="min-width:180px">
                <label class="form-label">Buscar texto</label>
                <input type="text" name="q" class="form-control" placeholder="Serial, Asset Tag...">
            </div>

            <button type="submit" class="btn btn-accent">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Exportar Excel
            </button>
        </form>
    </div>
</div>
@endsection
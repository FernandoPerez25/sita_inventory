@extends('layouts.app')

@section('title', 'Inventario — SITA')
@section('breadcrumb', 'SITA / Inventario')

@section('content')
<div class="page-header animate-in">
    <div>
        <h1 class="page-title">Inventario</h1>
        <p class="page-subtitle">// {{ number_format($inventario->total()) }} dispositivos registrados</p>
    </div>
    <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
        @if(auth()->user()->puedeEditar())
        <a href="{{ route('importar.index') }}" class="btn btn-ghost">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
            </svg>
            Carga Masiva
        </a>
        <a href="{{ route('inventario.create') }}" class="btn btn-accent">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Dispositivo
        </a>
        @endif
        <a href="{{ route('reportes.exportar', request()->query()) }}" class="btn btn-ghost">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Exportar Excel
        </a>
    </div>
</div>

{{-- ── Filtros ───────────────────────────────────────── --}}
<div class="card animate-in delay-1" style="margin-bottom:1.25rem">
    <div class="card-body" style="padding:1rem 1.25rem">
        <form method="GET" action="{{ route('inventario.index') }}"
            style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end">

            <div style="flex:1;min-width:200px">
                <label class="form-label">Buscar</label>
                <input type="text" name="q" value="{{ request('q') }}"
                    class="form-control" placeholder="Serial, Asset Tag, Nodename, PO#...">
            </div>

            <div style="min-width:150px">
                <label class="form-label">Sitio</label>
                <select name="sitio" class="form-control">
                    <option value="">Todos los sitios</option>
                    @foreach($sitios as $sitio)
                    <option value="{{ $sitio->id }}" {{ request('sitio') == $sitio->id ? 'selected' : '' }}>
                        {{ $sitio->clave }} — {{ $sitio->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div style="min-width:150px">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">Todos los status</option>
                    @foreach($statuses as $st)
                    <option value="{{ $st->id }}" {{ request('status') == $st->id ? 'selected' : '' }}>
                        {{ $st->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div style="min-width:150px">
                <label class="form-label">Dispositivo</label>
                <select name="dispositivo" class="form-control">
                    <option value="">Todos los tipos</option>
                    @foreach($dispositivos as $d)
                    <option value="{{ $d->id }}" {{ request('dispositivo') == $d->id ? 'selected' : '' }}>
                        {{ $d->tipo }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex;gap:0.5rem">
                <button type="submit" class="btn btn-primary">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Filtrar
                </button>
                @if(request()->hasAny(['q','sitio','status','dispositivo']))
                <a href="{{ route('inventario.index') }}" class="btn btn-ghost">Limpiar</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── Tabla ────────────────────────────────────────── --}}
<div class="card animate-in delay-2">
    <div class="table-wrapper" style="border:none;border-radius:6px">
        <table class="sita-table">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Serial Number</th>
                    <th>SITA Asset Tag</th>
                    <th>Dispositivo</th>
                    <th>Marca / Modelo</th>
                    <th>Sitio</th>
                    <th>Ubicación</th>
                    <th>Nodename</th>
                    <th>Status</th>
                    <th style="text-align:right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventario as $item)
                <tr>
                    <td style="color:var(--sita-muted);font-family:'Source Code Pro',monospace;font-size:0.72rem">{{ $item->id }}</td>

                    <td>
                        <a href="{{ route('inventario.show', $item) }}"
                            style="color:#93c5fd;font-family:'Source Code Pro',monospace;font-size:0.82rem;text-decoration:none;font-weight:500">
                            {{ $item->serial_number }}
                        </a>
                    </td>

                    <td class="td-mono" style="font-size:0.78rem">{{ $item->sita_asset_tag ?? '—' }}</td>

                    <td>
                        <span style="font-family:'Source Code Pro',monospace;font-size:0.78rem;color:#c4b5fd">
                            {{ $item->dispositivo->tipo }}
                        </span>
                    </td>

                    <td style="font-size:0.82rem">
                        <span style="color:var(--sita-text)">{{ $item->marca->nombre }}</span>
                        <span style="color:var(--sita-muted);font-size:0.75rem"> {{ $item->modelo->numero_modelo }}</span>
                    </td>

                    <td>
                        <span style="font-family:'Source Code Pro',monospace;font-size:0.78rem;background:rgba(245,158,11,0.1);color:var(--sita-accent);padding:0.1rem 0.4rem;border-radius:2px">
                            {{ $item->sitio->clave }}
                        </span>
                    </td>

                    <td style="font-size:0.82rem;color:var(--sita-muted)">{{ $item->ubicacion->nombre }}</td>

                    <td class="td-mono" style="font-size:0.75rem">{{ $item->nodename ?? '—' }}</td>

                    <td>
                        @php
                        $badgeMap = ['Instalado'=>'badge-instalado','Spare'=>'badge-spare','Bodega'=>'badge-bodega','Dañado'=>'badge-danado','GAP'=>'badge-gap'];
                        $badge = $badgeMap[$item->status->nombre] ?? 'badge-bodega';
                        @endphp
                        <span class="badge {{ $badge }}">{{ $item->status->nombre }}</span>
                    </td>

                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('inventario.show', $item) }}" class="btn btn-ghost btn-sm" title="Ver">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        @if(auth()->user()->puedeEditar())
                        <a href="{{ route('inventario.edit', $item) }}" class="btn btn-ghost btn-sm" title="Editar">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        @endif
                        <a href="{{ route('inventario.qr', $item) }}" class="btn btn-ghost btn-sm" title="Ver QR">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:3rem;color:var(--sita-muted)">
                        <div style="font-family:'Source Code Pro',monospace;font-size:0.8rem">
                            // No se encontraron dispositivos con los filtros aplicados
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($inventario->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--sita-border);display:flex;align-items:center;justify-content:space-between">
        <span style="font-family:'Source Code Pro',monospace;font-size:0.72rem;color:var(--sita-muted)">
            Mostrando {{ $inventario->firstItem() }}–{{ $inventario->lastItem() }} de {{ number_format($inventario->total()) }}
        </span>
        <div style="display:flex;gap:0.3rem">
            @if($inventario->onFirstPage())
            <span class="btn btn-ghost btn-sm" style="opacity:0.3;cursor:default">← Anterior</span>
            @else
            <a href="{{ $inventario->previousPageUrl() }}" class="btn btn-ghost btn-sm">← Anterior</a>
            @endif

            @if($inventario->hasMorePages())
            <a href="{{ $inventario->nextPageUrl() }}" class="btn btn-ghost btn-sm">Siguiente →</a>
            @else
            <span class="btn btn-ghost btn-sm" style="opacity:0.3;cursor:default">Siguiente →</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
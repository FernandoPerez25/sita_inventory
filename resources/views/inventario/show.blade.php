@extends('layouts.app')

@section('title', 'Dispositivo '.$inventario->serial_number.' — SITA')
@section('breadcrumb', 'SITA / Inventario / '.$inventario->serial_number)

@section('content')
<div class="page-header animate-in">
    <div>
        <h1 class="page-title">{{ $inventario->dispositivo->tipo }} — {{ $inventario->serial_number }}</h1>
        <p class="page-subtitle">// Ficha de dispositivo · ID #{{ $inventario->id }}</p>
    </div>
    <div style="display:flex;gap:0.5rem">
        @if(auth()->user()->puedeEditar())
        <a href="{{ route('inventario.edit', $inventario) }}" class="btn btn-ghost">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Editar
        </a>
        @endif
        <a href="{{ route('inventario.index') }}" class="btn btn-ghost">← Volver</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:1.25rem;align-items:start">

    {{-- ── Columna principal ── --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem">

        {{-- Datos generales --}}
        <div class="card animate-in delay-1">
            <div class="card-header">
                <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Información General</span>
                @php
                $badgeMap = ['Instalado'=>'badge-instalado','Spare'=>'badge-spare','Bodega'=>'badge-bodega','Dañado'=>'badge-danado','GAP'=>'badge-gap'];
                $badge = $badgeMap[$inventario->status->nombre] ?? 'badge-bodega';
                @endphp
                <span class="badge {{ $badge }}" style="font-size:0.78rem;padding:0.3rem 0.75rem">
                    ● {{ $inventario->status->nombre }}
                </span>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem">

                    @php
                    $campos = [
                    ['label' => 'Tipo de Dispositivo', 'value' => $inventario->dispositivo->tipo, 'mono' => true, 'color' => '#c4b5fd'],
                    ['label' => 'Marca', 'value' => $inventario->marca->nombre, 'mono' => false, 'color' => null],
                    ['label' => 'Modelo', 'value' => $inventario->modelo->numero_modelo, 'mono' => true, 'color' => null],
                    ['label' => 'Serial Number', 'value' => $inventario->serial_number, 'mono' => true, 'color' => '#93c5fd'],
                    ['label' => 'SITA Asset Tag', 'value' => $inventario->sita_asset_tag ?? '—', 'mono' => true, 'color' => '#93c5fd'],
                    ['label' => 'Nodename', 'value' => $inventario->nodename ?? '—', 'mono' => true, 'color' => null],
                    ['label' => 'Sitio', 'value' => $inventario->sitio->clave.' — '.$inventario->sitio->nombre, 'mono' => false, 'color' => null],
                    ['label' => 'Ubicación', 'value' => $inventario->ubicacion->nombre, 'mono' => false, 'color' => null],
                    ['label' => 'PO #', 'value' => $inventario->po_number ?? '—', 'mono' => true, 'color' => null],
                    ['label' => 'GAP Active', 'value' => $inventario->gap_active ?? '—', 'mono' => true, 'color' => null],
                    ];
                    @endphp

                    @foreach($campos as $campo)
                    <div>
                        <div style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.3rem">
                            {{ $campo['label'] }}
                        </div>
                        <div style="font-size:0.88rem;{{ $campo['mono'] ? "font-family:'Source Code Pro',monospace;" : '' }}{{ $campo['color'] ? 'color:'.$campo['color'].';' : 'color:var(--sita-text);' }}">
                            {{ $campo['value'] }}
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($inventario->comentarios)
                <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--sita-border)">
                    <div style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.4rem">Comentarios</div>
                    <p style="font-size:0.85rem;color:var(--sita-text);line-height:1.5">{{ $inventario->comentarios }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Historial de cambios --}}
        <div class="card animate-in delay-2">
            <div class="card-header">
                <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Historial de Cambios</span>
                <span style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted)">
                    {{ $inventario->historial->count() }} registro(s)
                </span>
            </div>
            @if($inventario->historial->count() > 0)
            <div class="table-wrapper" style="border:none;border-radius:0">
                <table class="sita-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Campo</th>
                            <th>Valor anterior</th>
                            <th>Valor nuevo</th>
                            <th>Usuario</th>
                            <th>Origen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventario->historial as $h)
                        <tr>
                            <td style="font-family:'Source Code Pro',monospace;font-size:0.75rem;white-space:nowrap">
                                {{ $h->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td style="font-family:'Source Code Pro',monospace;font-size:0.75rem;color:#c4b5fd">
                                {{ $h->campo_modificado }}
                            </td>
                            <td style="font-size:0.8rem;color:var(--sita-muted)">{{ $h->valor_anterior ?? '—' }}</td>
                            <td style="font-size:0.8rem;color:var(--sita-success)">{{ $h->valor_nuevo ?? '—' }}</td>
                            <td style="font-size:0.8rem">{{ $h->usuario->nombre_completo ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $h->origen === 'movil' ? 'badge-spare' : 'badge-bodega' }}">
                                    {{ $h->origen }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="card-body" style="text-align:center;padding:2rem;color:var(--sita-muted)">
                <div style="font-family:'Source Code Pro',monospace;font-size:0.78rem">
                    // Sin cambios registrados
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Columna QR ── --}}
    <div style="display:flex;flex-direction:column;gap:1.25rem">

        {{-- QR Code --}}
        <div class="card animate-in delay-1" style="border-color:rgba(245,158,11,0.25)">
            <div class="card-header" style="border-color:rgba(245,158,11,0.15)">
                <span style="font-family:'Rajdhani',sans-serif;font-weight:600;color:var(--sita-accent)">Código QR</span>
            </div>
            <div class="card-body" style="text-align:center;padding:1.5rem">
                @if($inventario->qr_code)
                <div style="background:#fff;padding:12px;border-radius:6px;display:inline-block;margin-bottom:1rem">
                    {!! $imagenQr ?? '<p style="color:#000;font-size:0.8rem;padding:2rem">Generando QR...</p>' !!}
                </div>
                <div style="font-family:'Source Code Pro',monospace;font-size:0.7rem;color:var(--sita-muted);word-break:break-all;margin-bottom:1rem">
                    {{ $inventario->qr_code }}
                </div>
                <a href="{{ route('inventario.qr', $inventario) }}" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir QR
                </a>
                @else
                <p style="color:var(--sita-muted);font-size:0.82rem;margin-bottom:1rem">QR no generado</p>
                <form method="POST" action="{{ route('inventario.qr', $inventario) }}">
                    @csrf
                    <button type="submit" class="btn btn-accent btn-sm" style="width:100%;justify-content:center">
                        Generar QR
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Auditoría --}}
        <div class="card animate-in delay-2">
            <div class="card-header">
                <span style="font-family:'Rajdhani',sans-serif;font-weight:600;font-size:0.88rem">Auditoría</span>
            </div>
            <div class="card-body" style="padding:1rem;font-size:0.78rem;line-height:2;color:var(--sita-muted)">
                <div>Registrado por</div>
                <div style="color:var(--sita-text);margin-bottom:0.5rem">{{ $inventario->usuarioRegistro->nombre_completo ?? '—' }}</div>
                <div>Fecha registro</div>
                <div style="color:var(--sita-text);font-family:'Source Code Pro',monospace;font-size:0.75rem;margin-bottom:0.5rem">{{ $inventario->created_at->format('d/m/Y H:i:s') }}</div>
                <div>Última modificación</div>
                <div style="color:var(--sita-text);font-family:'Source Code Pro',monospace;font-size:0.75rem">{{ $inventario->updated_at->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>

        {{-- Acciones peligrosas --}}
        @if(auth()->user()->esAdmin())
        <div class="card animate-in delay-3" style="border-color:rgba(239,68,68,0.2)">
            <div class="card-body" style="padding:1rem">
                <div style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-danger);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.75rem">Zona de Peligro</div>
                <form method="POST" action="{{ route('inventario.destroy', $inventario) }}"
                    onsubmit="return confirm('¿Seguro que deseas eliminar este dispositivo? Esta acción no se puede deshacer.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar Dispositivo
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mostrar QR inline si hay imagen base64
    @if(isset($imagenQr) && $imagenQr)
    const qrContainer = document.querySelector('.card .card-body div[style*="background:#fff"]');
    if (qrContainer) {
        const img = document.createElement('img');
        img.src = 'data:image/png;base64,{{ base64_encode($imagenQr) }}';
        img.style.cssText = 'width:200px;height:200px;display:block';
        qrContainer.innerHTML = '';
        qrContainer.appendChild(img);
    }
    @endif
</script>
@endpush
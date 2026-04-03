@extends('layouts.app')

@section('title', 'Catálogos — SITA')
@section('breadcrumb', 'SITA / Catálogos')

@section('content')
<div class="page-header animate-in">
    <div>
        <h1 class="page-title">Catálogos</h1>
        <p class="page-subtitle">// Gestión de tablas de referencia</p>
    </div>
</div>

{{-- Tabs de catálogos --}}
<div style="display:flex;gap:0;margin-bottom:1.5rem;border-bottom:1px solid var(--sita-border);flex-wrap:wrap" class="animate-in delay-1">
    @php
    $tabs = [
    ['id'=>'sitios', 'label'=>'Sitios'],
    ['id'=>'ubicaciones', 'label'=>'Ubicaciones'],
    ['id'=>'dispositivos', 'label'=>'Dispositivos'],
    ['id'=>'marcas', 'label'=>'Marcas'],
    ['id'=>'modelos', 'label'=>'Modelos'],
    ['id'=>'status', 'label'=>'Status'],
    ];
    $activeTab = request('tab', 'sitios');
    @endphp

    @foreach($tabs as $tab)
    <a href="?tab={{ $tab['id'] }}"
        style="padding:0.6rem 1.1rem;text-decoration:none;font-family:'Rajdhani',sans-serif;font-size:0.92rem;font-weight:600;letter-spacing:0.04em;border-bottom:2px solid {{ $activeTab === $tab['id'] ? 'var(--sita-accent)' : 'transparent' }};color:{{ $activeTab === $tab['id'] ? 'var(--sita-accent)' : 'var(--sita-muted)' }};transition:all 0.15s">
        {{ $tab['label'] }}
    </a>
    @endforeach
</div>

{{-- ══ SITIOS ══ --}}
@if($activeTab === 'sitios')
<div style="display:grid;grid-template-columns:320px 1fr;gap:1.25rem" class="animate-in">
    <div class="card">
        <div class="card-header">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Agregar Sitio</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('catalogos.sitios.store') }}">
                @csrf
                <div style="margin-bottom:1rem">
                    <label class="form-label">Clave (3 letras) *</label>
                    <input type="text" name="clave" class="form-control font-mono" maxlength="3" required
                        placeholder="Ej: GDL" style="text-transform:uppercase" value="{{ old('clave') }}">
                    @error('clave') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div style="margin-bottom:1.25rem">
                    <label class="form-label">Nombre completo *</label>
                    <input type="text" name="nombre" class="form-control" required
                        placeholder="Ej: Guadalajara" value="{{ old('nombre') }}">
                    @error('nombre') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center">
                    + Agregar Sitio
                </button>
            </form>
            <div style="margin-top:0.75rem;padding:0.75rem;background:rgba(245,158,11,0.06);border-radius:4px;font-size:0.75rem;color:var(--sita-muted)">
                💡 Al crear un sitio se generan automáticamente las 5 ubicaciones estándar: CHECK-IN, GATE, COREROOM, KIOSCO, BODEGA
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Sitios registrados</span>
            <span style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted)">{{ $sitios->count() }} sitios</span>
        </div>
        <div class="table-wrapper" style="border:none;border-radius:0">
            <table class="sita-table">
                <thead>
                    <tr>
                        <th>Clave</th>
                        <th>Nombre</th>
                        <th>Estado</th>
                        <th>Ubicaciones</th>
                        <th style="text-align:right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sitios as $sitio)
                    <tr>
                        <td><span class="badge badge-gap font-mono">{{ $sitio->clave }}</span></td>
                        <td style="font-size:0.88rem">{{ $sitio->nombre }}</td>
                        <td><span class="badge {{ $sitio->activo ? 'badge-instalado' : 'badge-danado' }}">{{ $sitio->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td style="font-family:'Source Code Pro',monospace;font-size:0.78rem;color:var(--sita-muted)">
                            {{ $sitio->ubicaciones->count() }} ubicaciones
                        </td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('catalogos.sitios.update', $sitio) }}" style="display:inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="activo" value="{{ $sitio->activo ? 0 : 1 }}">
                                <input type="hidden" name="nombre" value="{{ $sitio->nombre }}">
                                <button type="submit" class="btn btn-ghost btn-sm">
                                    {{ $sitio->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ══ UBICACIONES ══ --}}
@if($activeTab === 'ubicaciones')
<div style="display:grid;grid-template-columns:320px 1fr;gap:1.25rem" class="animate-in">
    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Nueva Ubicación</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('catalogos.ubicaciones.store') }}">
                @csrf
                <div style="margin-bottom:1rem">
                    <label class="form-label">Sitio *</label>
                    <select name="id_sitio" class="form-control" required>
                        <option value="">— Seleccionar sitio —</option>
                        @foreach($sitios as $sitio)
                        <option value="{{ $sitio->id }}">{{ $sitio->clave }} — {{ $sitio->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:1.25rem">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Ej: SALA VIP">
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center">+ Agregar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Ubicaciones</span></div>
        <div class="table-wrapper" style="border:none;border-radius:0">
            <table class="sita-table">
                <thead>
                    <tr>
                        <th>Sitio</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th style="text-align:right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ubicaciones as $ub)
                    <tr>
                        <td><span class="badge badge-gap font-mono">{{ $ub->sitio->clave }}</span></td>
                        <td class="td-mono" style="font-size:0.82rem">{{ $ub->nombre }}</td>
                        <td><span class="badge {{ $ub->activo ? 'badge-instalado' : 'badge-danado' }}">{{ $ub->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('catalogos.ubicaciones.update', $ub) }}" style="display:inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="activo" value="{{ $ub->activo ? 0 : 1 }}">
                                <input type="hidden" name="nombre" value="{{ $ub->nombre }}">
                                <button type="submit" class="btn btn-ghost btn-sm">{{ $ub->activo ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ══ DISPOSITIVOS ══ --}}
@if($activeTab === 'dispositivos')
<div style="display:grid;grid-template-columns:320px 1fr;gap:1.25rem" class="animate-in">
    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Nuevo Tipo</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('catalogos.dispositivos.store') }}">
                @csrf
                <div style="margin-bottom:1rem">
                    <label class="form-label">Tipo (sigla) *</label>
                    <input type="text" name="tipo" class="form-control font-mono" required placeholder="Ej: GPU" style="text-transform:uppercase">
                </div>
                <div style="margin-bottom:1.25rem">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" placeholder="Descripción del dispositivo">
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center">+ Agregar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Tipos de Dispositivo</span></div>
        <div class="table-wrapper" style="border:none;border-radius:0">
            <table class="sita-table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th style="text-align:right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dispositivos as $d)
                    <tr>
                        <td><span style="font-family:'Source Code Pro',monospace;font-size:0.82rem;color:#c4b5fd">{{ $d->tipo }}</span></td>
                        <td style="font-size:0.82rem;color:var(--sita-muted)">{{ $d->descripcion ?? '—' }}</td>
                        <td><span class="badge {{ $d->activo ? 'badge-instalado' : 'badge-danado' }}">{{ $d->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('catalogos.dispositivos.update', $d) }}" style="display:inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="activo" value="{{ $d->activo ? 0 : 1 }}">
                                <button type="submit" class="btn btn-ghost btn-sm">{{ $d->activo ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ══ MARCAS ══ --}}
@if($activeTab === 'marcas')
<div style="display:grid;grid-template-columns:320px 1fr;gap:1.25rem" class="animate-in">
    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Nueva Marca</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('catalogos.marcas.store') }}">
                @csrf
                <div style="margin-bottom:1.25rem">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Ej: LENOVO" style="text-transform:uppercase">
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center">+ Agregar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Marcas</span></div>
        <div class="table-wrapper" style="border:none;border-radius:0">
            <table class="sita-table">
                <thead>
                    <tr>
                        <th>Marca</th>
                        <th>Modelos</th>
                        <th>Estado</th>
                        <th style="text-align:right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marcas as $m)
                    <tr>
                        <td style="font-size:0.88rem;font-weight:500">{{ $m->nombre }}</td>
                        <td style="font-family:'Source Code Pro',monospace;font-size:0.78rem;color:var(--sita-muted)">{{ $m->modelos_count }} modelos</td>
                        <td><span class="badge {{ $m->activo ? 'badge-instalado' : 'badge-danado' }}">{{ $m->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('catalogos.marcas.update', $m) }}" style="display:inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="activo" value="{{ $m->activo ? 0 : 1 }}">
                                <button type="submit" class="btn btn-ghost btn-sm">{{ $m->activo ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ══ MODELOS ══ --}}
@if($activeTab === 'modelos')
<div style="display:grid;grid-template-columns:320px 1fr;gap:1.25rem" class="animate-in">
    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Nuevo Modelo</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('catalogos.modelos.store') }}">
                @csrf
                <div style="margin-bottom:1rem">
                    <label class="form-label">Marca *</label>
                    <select name="id_marca" class="form-control" required>
                        <option value="">— Seleccionar marca —</option>
                        @foreach($marcas as $m)
                        <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:1.25rem">
                    <label class="form-label">Número de modelo *</label>
                    <input type="text" name="numero_modelo" class="form-control font-mono" required placeholder="Ej: T14S GEN 4" style="text-transform:uppercase">
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center">+ Agregar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Modelos</span></div>
        <div class="table-wrapper" style="border:none;border-radius:0">
            <table class="sita-table">
                <thead>
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Estado</th>
                        <th style="text-align:right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modelos as $mo)
                    <tr>
                        <td style="font-size:0.82rem;color:var(--sita-muted)">{{ $mo->marca->nombre }}</td>
                        <td class="td-mono" style="font-size:0.82rem">{{ $mo->numero_modelo }}</td>
                        <td><span class="badge {{ $mo->activo ? 'badge-instalado' : 'badge-danado' }}">{{ $mo->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('catalogos.modelos.update', $mo) }}" style="display:inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="activo" value="{{ $mo->activo ? 0 : 1 }}">
                                <button type="submit" class="btn btn-ghost btn-sm">{{ $mo->activo ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ══ STATUS ══ --}}
@if($activeTab === 'status')
<div style="display:grid;grid-template-columns:320px 1fr;gap:1.25rem" class="animate-in">
    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Nuevo Status</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('catalogos.status.store') }}">
                @csrf
                <div style="margin-bottom:1rem">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Ej: En Reparación">
                </div>
                <div style="margin-bottom:1.25rem">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="descripcion" class="form-control" placeholder="Descripción del status">
                </div>
                <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center">+ Agregar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-family:'Rajdhani',sans-serif;font-weight:600">Status</span></div>
        <div class="table-wrapper" style="border:none;border-radius:0">
            <table class="sita-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th style="text-align:right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statuses as $st)
                    @php
                    $badgeMap = ['Instalado'=>'badge-instalado','Spare'=>'badge-spare','Bodega'=>'badge-bodega','Dañado'=>'badge-danado','GAP'=>'badge-gap'];
                    $badge = $badgeMap[$st->nombre] ?? 'badge-bodega';
                    @endphp
                    <tr>
                        <td><span class="badge {{ $badge }}">{{ $st->nombre }}</span></td>
                        <td style="font-size:0.82rem;color:var(--sita-muted)">{{ $st->descripcion ?? '—' }}</td>
                        <td><span class="badge {{ $st->activo ? 'badge-instalado' : 'badge-danado' }}">{{ $st->activo ? 'Activo' : 'Inactivo' }}</span></td>
                        <td style="text-align:right">
                            <form method="POST" action="{{ route('catalogos.status.update', $st) }}" style="display:inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="activo" value="{{ $st->activo ? 0 : 1 }}">
                                <button type="submit" class="btn btn-ghost btn-sm">{{ $st->activo ? 'Desactivar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
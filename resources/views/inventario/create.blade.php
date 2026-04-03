@extends('layouts.app')

@section('title', isset($inventario) ? 'Editar Dispositivo — SITA' : 'Nuevo Dispositivo — SITA')
@section('breadcrumb', isset($inventario) ? 'SITA / Inventario / Editar' : 'SITA / Inventario / Nuevo')

@section('content')
@php $editando = isset($inventario); @endphp

<div class="page-header animate-in">
    <div>
        <h1 class="page-title">{{ $editando ? 'Editar Dispositivo' : 'Nuevo Dispositivo' }}</h1>
        <p class="page-subtitle">{{ $editando ? '// Modificar registro: '.$inventario->serial_number : '// Registrar equipo individual' }}</p>
    </div>
    <a href="{{ route('inventario.index') }}" class="btn btn-ghost">← Volver</a>
</div>

<form method="POST"
    action="{{ $editando ? route('inventario.update', $inventario) : route('inventario.store') }}"
    id="form-inventario">
    @csrf
    @if($editando) @method('PUT') @endif

    <div style="display:grid;grid-template-columns:1fr 340px;gap:1.25rem;align-items:start">

        {{-- ── Columna principal ── --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem">

            {{-- Ubicación --}}
            <div class="card animate-in delay-1">
                <div class="card-header">
                    <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Ubicación del Equipo</span>
                    <span style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted)">LOCALIZACIÓN</span>
                </div>
                <div class="card-body">
                    <div class="form-grid-2">
                        <div>
                            <label class="form-label" for="id_sitio">Sitio *</label>
                            <select name="id_sitio" id="id_sitio" class="form-control" required>
                                <option value="">— Seleccionar sitio —</option>
                                @foreach($sitios as $sitio)
                                <option value="{{ $sitio->id }}"
                                    {{ old('id_sitio', $editando ? $inventario->id_sitio : '') == $sitio->id ? 'selected' : '' }}>
                                    {{ $sitio->clave }} — {{ $sitio->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_sitio') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="id_ubicacion">Ubicación *</label>
                            <select name="id_ubicacion" id="id_ubicacion" class="form-control" required>
                                <option value="">— Primero selecciona un sitio —</option>
                                @if($editando)
                                @foreach($ubicaciones->where('id_sitio', $inventario->id_sitio) as $ub)
                                <option value="{{ $ub->id }}" {{ $inventario->id_ubicacion == $ub->id ? 'selected' : '' }}>
                                    {{ $ub->nombre }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            @error('id_ubicacion') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dispositivo --}}
            <div class="card animate-in delay-2">
                <div class="card-header">
                    <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Tipo de Dispositivo</span>
                    <span style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted)">HARDWARE</span>
                </div>
                <div class="card-body">
                    <div class="form-grid-3">
                        <div>
                            <label class="form-label" for="id_dispositivo">Tipo *</label>
                            <select name="id_dispositivo" id="id_dispositivo" class="form-control" required>
                                <option value="">— Tipo —</option>
                                @foreach($dispositivos as $d)
                                <option value="{{ $d->id }}"
                                    {{ old('id_dispositivo', $editando ? $inventario->id_dispositivo : '') == $d->id ? 'selected' : '' }}>
                                    {{ $d->tipo }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_dispositivo') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="id_marca">Marca *</label>
                            <select name="id_marca" id="id_marca" class="form-control" required>
                                <option value="">— Marca —</option>
                                @foreach($marcas as $m)
                                <option value="{{ $m->id }}"
                                    {{ old('id_marca', $editando ? $inventario->id_marca : '') == $m->id ? 'selected' : '' }}>
                                    {{ $m->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_marca') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label" for="id_modelo">Modelo *</label>
                            <select name="id_modelo" id="id_modelo" class="form-control" required>
                                <option value="">— Primero selecciona marca —</option>
                                @if($editando)
                                @foreach($modelos->where('id_marca', $inventario->id_marca) as $mo)
                                <option value="{{ $mo->id }}" {{ $inventario->id_modelo == $mo->id ? 'selected' : '' }}>
                                    {{ $mo->numero_modelo }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            @error('id_modelo') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Identificadores --}}
            <div class="card animate-in delay-3">
                <div class="card-header">
                    <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Identificadores</span>
                    <span style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted)">DATOS ÚNICOS</span>
                </div>
                <div class="card-body">
                    <div class="form-grid-2" style="margin-bottom:1.25rem">
                        <div>
                            <label class="form-label" for="serial_number">Serial Number *</label>
                            <input type="text" name="serial_number" id="serial_number"
                                class="form-control font-mono"
                                value="{{ old('serial_number', $editando ? $inventario->serial_number : '') }}"
                                placeholder="Ej: MA048147" required>
                            @error('serial_number') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label" for="sita_asset_tag">SITA Asset Tag</label>
                            <input type="text" name="sita_asset_tag" id="sita_asset_tag"
                                class="form-control font-mono"
                                value="{{ old('sita_asset_tag', $editando ? $inventario->sita_asset_tag : '') }}"
                                placeholder="Ej: XS30050000">
                            @error('sita_asset_tag') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-grid-3">
                        <div>
                            <label class="form-label" for="nodename">Nodename</label>
                            <input type="text" name="nodename" id="nodename"
                                class="form-control font-mono"
                                value="{{ old('nodename', $editando ? $inventario->nodename : '') }}"
                                placeholder="Ej: AGU1CKB001">
                            @error('nodename') <p class="form-error">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label" for="po_number">PO #</label>
                            <input type="text" name="po_number" id="po_number"
                                class="form-control font-mono"
                                value="{{ old('po_number', $editando ? $inventario->po_number : '') }}"
                                placeholder="Ej: PO-2026-001">
                        </div>
                        <div>
                            <label class="form-label" for="gap_active">GAP Active</label>
                            <input type="text" name="gap_active" id="gap_active"
                                class="form-control font-mono"
                                value="{{ old('gap_active', $editando ? $inventario->gap_active : '') }}"
                                placeholder="N/A">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Comentarios --}}
            <div class="card animate-in delay-4">
                <div class="card-header">
                    <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Comentarios</span>
                </div>
                <div class="card-body">
                    <textarea name="comentarios" id="comentarios" rows="3"
                        class="form-control"
                        placeholder="Notas adicionales sobre el dispositivo...">{{ old('comentarios', $editando ? $inventario->comentarios : '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Columna derecha ── --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem">

            {{-- Status --}}
            <div class="card animate-in delay-1">
                <div class="card-header">
                    <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Status</span>
                </div>
                <div class="card-body">
                    <label class="form-label" for="id_status">Status Actual *</label>
                    <select name="id_status" id="id_status" class="form-control" required>
                        <option value="">— Seleccionar —</option>
                        @foreach($statuses as $st)
                        <option value="{{ $st->id }}"
                            {{ old('id_status', $editando ? $inventario->id_status : '') == $st->id ? 'selected' : '' }}>
                            {{ $st->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_status') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="card animate-in delay-2">
                <div class="card-body" style="display:flex;flex-direction:column;gap:0.6rem">
                    <button type="submit" class="btn btn-accent" style="width:100%;justify-content:center;padding:0.6rem">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $editando ? 'Guardar Cambios' : 'Registrar Dispositivo' }}
                    </button>
                    <a href="{{ route('inventario.index') }}" class="btn btn-ghost" style="width:100%;justify-content:center">
                        Cancelar
                    </a>
                </div>
            </div>

            {{-- Info (solo edición) --}}
            @if($editando)
            <div class="card animate-in delay-3" style="border-color:rgba(245,158,11,0.2)">
                <div class="card-body" style="padding:1rem">
                    <div style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.6rem">Info del Registro</div>
                    <div style="font-size:0.78rem;color:var(--sita-muted);line-height:1.8">
                        <div>ID: <span style="color:var(--sita-text)">#{{ $inventario->id }}</span></div>
                        <div>QR: <span style="color:#93c5fd;font-family:'Source Code Pro',monospace">{{ $inventario->qr_code ?? 'Pendiente' }}</span></div>
                        <div>Creado: <span style="color:var(--sita-text)">{{ $inventario->created_at->format('d/m/Y H:i') }}</span></div>
                        <div>Por: <span style="color:var(--sita-text)">{{ $inventario->usuarioRegistro->nombre_completo ?? '—' }}</span></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // ── Selects dinámicos: Sitio → Ubicaciones, Marca → Modelos ──

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Sitio → Ubicaciones
    document.getElementById('id_sitio').addEventListener('change', async function() {
        const idSitio = this.value;
        const selectUb = document.getElementById('id_ubicacion');

        selectUb.innerHTML = '<option value="">Cargando...</option>';
        selectUb.disabled = true;

        if (!idSitio) {
            selectUb.innerHTML = '<option value="">— Primero selecciona un sitio —</option>';
            selectUb.disabled = false;
            return;
        }

        try {
            const res = await fetch(`/catalogos/ubicaciones-por-sitio/${idSitio}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await res.json();

            selectUb.innerHTML = '<option value="">— Seleccionar ubicación —</option>';
            data.forEach(ub => {
                selectUb.innerHTML += `<option value="${ub.id}">${ub.nombre}</option>`;
            });
            selectUb.disabled = false;
        } catch (e) {
            selectUb.innerHTML = '<option value="">Error al cargar</option>';
            selectUb.disabled = false;
        }
    });

    // Marca → Modelos
    document.getElementById('id_marca').addEventListener('change', async function() {
        const idMarca = this.value;
        const selectMo = document.getElementById('id_modelo');

        selectMo.innerHTML = '<option value="">Cargando...</option>';
        selectMo.disabled = true;

        if (!idMarca) {
            selectMo.innerHTML = '<option value="">— Primero selecciona una marca —</option>';
            selectMo.disabled = false;
            return;
        }

        try {
            const res = await fetch(`/catalogos/modelos-por-marca/${idMarca}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await res.json();

            selectMo.innerHTML = '<option value="">— Seleccionar modelo —</option>';
            data.forEach(mo => {
                selectMo.innerHTML += `<option value="${mo.id}">${mo.numero_modelo}</option>`;
            });
            selectMo.disabled = false;
        } catch (e) {
            selectMo.innerHTML = '<option value="">Error al cargar</option>';
            selectMo.disabled = false;
        }
    });
</script>
@endpush
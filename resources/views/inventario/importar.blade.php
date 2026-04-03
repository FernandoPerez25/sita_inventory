@extends('layouts.app')

@section('title', 'Carga Masiva — SITA')
@section('breadcrumb', 'SITA / Carga Masiva')

@section('content')
<div class="page-header animate-in">
    <div>
        <h1 class="page-title">Carga Masiva</h1>
        <p class="page-subtitle">// Registro múltiple de dispositivos</p>
    </div>
    <a href="{{ route('importar.plantilla') }}" class="btn btn-ghost">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Descargar Plantilla
    </a>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:0;margin-bottom:1.5rem;border-bottom:1px solid var(--sita-border)" class="animate-in delay-1">
    <button onclick="showTab('tab-excel')" id="btn-tab-excel"
        style="padding:0.6rem 1.25rem;background:none;border:none;border-bottom:2px solid var(--sita-accent);color:var(--sita-accent);font-family:'Rajdhani',sans-serif;font-size:0.95rem;font-weight:600;cursor:pointer;letter-spacing:0.04em">
        📥 Importar Excel / CSV
    </button>
    <button onclick="showTab('tab-form')" id="btn-tab-form"
        style="padding:0.6rem 1.25rem;background:none;border:none;border-bottom:2px solid transparent;color:var(--sita-muted);font-family:'Rajdhani',sans-serif;font-size:0.95rem;font-weight:600;cursor:pointer;letter-spacing:0.04em">
        📋 Formulario Multi-fila
    </button>
    <button onclick="showTab('tab-historial')" id="btn-tab-historial"
        style="padding:0.6rem 1.25rem;background:none;border:none;border-bottom:2px solid transparent;color:var(--sita-muted);font-family:'Rajdhani',sans-serif;font-size:0.95rem;font-weight:600;cursor:pointer;letter-spacing:0.04em">
        🕓 Historial de Importaciones
    </button>
</div>

{{-- ══ TAB: EXCEL ══ --}}
<div id="tab-excel">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">

        {{-- Upload --}}
        <div class="card animate-in delay-1">
            <div class="card-header">
                <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Subir Archivo</span>
                <span style="font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted)">XLSX · XLS · CSV</span>
            </div>
            <div class="card-body">
                @if(session('errores'))
                <div class="alert alert-warning" style="margin-bottom:1rem">
                    <div>
                        <div style="font-weight:600;margin-bottom:0.4rem">Importación con errores en las siguientes filas:</div>
                        @foreach(session('errores') as $err)
                        <div style="font-family:'Source Code Pro',monospace;font-size:0.75rem">
                            Fila {{ $err['fila'] ?? '?' }}: {{ $err['mensaje'] ?? $err }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('importar.store') }}" enctype="multipart/form-data" id="form-excel">
                    @csrf

                    {{-- Drop zone --}}
                    <div id="drop-zone"
                        style="border:2px dashed var(--sita-border);border-radius:6px;padding:2.5rem 1rem;text-align:center;cursor:pointer;transition:all 0.2s ease;margin-bottom:1.25rem"
                        onclick="document.getElementById('archivo').click()"
                        ondragover="event.preventDefault();this.style.borderColor='var(--sita-accent)'"
                        ondragleave="this.style.borderColor='var(--sita-border)'"
                        ondrop="handleDrop(event)">
                        <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--sita-muted);margin:0 auto 0.75rem">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <div id="drop-text" style="font-size:0.88rem;color:var(--sita-muted)">
                            Arrastra tu archivo aquí o <span style="color:var(--sita-accent)">haz clic para seleccionar</span>
                        </div>
                        <div style="font-family:'Source Code Pro',monospace;font-size:0.68rem;color:var(--sita-muted);margin-top:0.4rem">
                            .xlsx · .xls · .csv — Máx. 10 MB
                        </div>
                    </div>

                    <input type="file" name="archivo" id="archivo" accept=".xlsx,.xls,.csv"
                        style="display:none" onchange="onFileSelected(this)">

                    <button type="submit" class="btn btn-accent" id="btn-subir"
                        style="width:100%;justify-content:center" disabled>
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                        </svg>
                        Importar Archivo
                    </button>
                </form>
            </div>
        </div>

        {{-- Instrucciones --}}
        <div class="card animate-in delay-2">
            <div class="card-header">
                <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Formato Requerido</span>
            </div>
            <div class="card-body">
                <div style="font-family:'Source Code Pro',monospace;font-size:0.7rem;color:var(--sita-muted);margin-bottom:1rem;text-transform:uppercase;letter-spacing:0.1em">
                    Columnas esperadas en orden:
                </div>
                <div style="display:flex;flex-direction:column;gap:0.35rem">
                    @php
                    $columnas = [
                    ['col'=>'A', 'nombre'=>'SITE', 'desc'=>'Clave del sitio (AGU, GDL...)', 'req'=>true],
                    ['col'=>'B', 'nombre'=>'LOCATION', 'desc'=>'Nombre de ubicación', 'req'=>true],
                    ['col'=>'C', 'nombre'=>'Dispositivo', 'desc'=>'Tipo (ATB, BTP, CPU...)', 'req'=>true],
                    ['col'=>'D', 'nombre'=>'Marca', 'desc'=>'Nombre de la marca', 'req'=>true],
                    ['col'=>'E', 'nombre'=>'Model Number', 'desc'=>'Número de modelo', 'req'=>true],
                    ['col'=>'F', 'nombre'=>'Serial Number', 'desc'=>'Número de serie único', 'req'=>true],
                    ['col'=>'G', 'nombre'=>'SITA Asset Tag','desc'=>'Tag de activo SITA', 'req'=>false],
                    ['col'=>'H', 'nombre'=>'PO #', 'desc'=>'Número de orden de compra', 'req'=>false],
                    ['col'=>'I', 'nombre'=>'GAP ACTIVE', 'desc'=>'Estado GAP', 'req'=>false],
                    ['col'=>'J', 'nombre'=>'NODENAME', 'desc'=>'Nombre del nodo en red', 'req'=>false],
                    ['col'=>'K', 'nombre'=>'STATUS ACTUAL', 'desc'=>'Status del dispositivo', 'req'=>true],
                    ['col'=>'L', 'nombre'=>'Comentarios', 'desc'=>'Notas adicionales', 'req'=>false],
                    ];
                    @endphp
                    @foreach($columnas as $c)
                    <div style="display:flex;align-items:center;gap:0.6rem;padding:0.3rem 0;border-bottom:1px solid rgba(30,45,69,0.4)">
                        <span style="font-family:'Source Code Pro',monospace;font-size:0.72rem;background:var(--sita-surface2);color:var(--sita-accent);padding:0.1rem 0.35rem;border-radius:2px;min-width:22px;text-align:center">{{ $c['col'] }}</span>
                        <span style="font-family:'Source Code Pro',monospace;font-size:0.75rem;color:var(--sita-text);min-width:120px">{{ $c['nombre'] }}</span>
                        <span style="font-size:0.75rem;color:var(--sita-muted);flex:1">{{ $c['desc'] }}</span>
                        @if($c['req'])
                        <span style="font-size:0.65rem;color:var(--sita-danger)">*</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                <div style="margin-top:0.75rem;font-size:0.72rem;color:var(--sita-muted)">
                    <span style="color:var(--sita-danger)">*</span> Campos obligatorios · La fila 1 debe ser el encabezado
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ TAB: FORMULARIO MULTI-FILA ══ --}}
<div id="tab-form" style="display:none">
    <div class="card animate-in">
        <div class="card-header">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Registro en Lote</span>
            <div style="display:flex;gap:0.5rem">
                <button onclick="agregarFila()" class="btn btn-accent btn-sm">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Agregar Fila
                </button>
                <button onclick="limpiarFilas()" class="btn btn-ghost btn-sm">Limpiar</button>
            </div>
        </div>

        <div style="overflow-x:auto">
            <form method="POST" action="{{ route('importar.store') }}" id="form-multifila">
                @csrf
                <input type="hidden" name="metodo" value="formulario">

                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;min-width:1100px">
                    <thead>
                        <tr style="background:var(--sita-surface2)">
                            <th style="padding:0.6rem 0.75rem;text-align:left;font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--sita-border)">#</th>
                            <th style="padding:0.6rem 0.75rem;text-align:left;font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--sita-border)">Sitio *</th>
                            <th style="padding:0.6rem 0.75rem;text-align:left;font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--sita-border)">Ubicación *</th>
                            <th style="padding:0.6rem 0.75rem;text-align:left;font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--sita-border)">Dispositivo *</th>
                            <th style="padding:0.6rem 0.75rem;text-align:left;font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--sita-border)">Marca *</th>
                            <th style="padding:0.6rem 0.75rem;text-align:left;font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--sita-border)">Modelo *</th>
                            <th style="padding:0.6rem 0.75rem;text-align:left;font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--sita-border)">Serial * </th>
                            <th style="padding:0.6rem 0.75rem;text-align:left;font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--sita-border)">Asset Tag</th>
                            <th style="padding:0.6rem 0.75rem;text-align:left;font-family:'Source Code Pro',monospace;font-size:0.65rem;color:var(--sita-muted);text-transform:uppercase;letter-spacing:0.08em;border-bottom:1px solid var(--sita-border)">Status *</th>
                            <th style="padding:0.6rem 0.75rem;border-bottom:1px solid var(--sita-border)"></th>
                        </tr>
                    </thead>
                    <tbody id="tabla-filas">
                        {{-- Filas se agregan con JS --}}
                    </tbody>
                </table>

                <div style="padding:1rem 1.25rem;border-top:1px solid var(--sita-border);display:flex;align-items:center;justify-content:space-between">
                    <span id="contador-filas" style="font-family:'Source Code Pro',monospace;font-size:0.75rem;color:var(--sita-muted)">0 filas</span>
                    <button type="submit" class="btn btn-accent" id="btn-guardar-lote" disabled>
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Guardar Todo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ TAB: HISTORIAL ══ --}}
<div id="tab-historial" style="display:none">
    <div class="card animate-in">
        <div class="card-header">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:600">Historial de Importaciones</span>
        </div>
        <div class="table-wrapper" style="border:none;border-radius:0">
            <table class="sita-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Método</th>
                        <th>Archivo</th>
                        <th>Total</th>
                        <th>Exitosos</th>
                        <th>Fallidos</th>
                        <th>Usuario</th>
                        <th>Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($importaciones as $imp)
                    <tr>
                        <td style="font-family:'Source Code Pro',monospace;font-size:0.75rem">{{ $imp->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge badge-spare">{{ $imp->metodo }}</span></td>
                        <td style="font-size:0.78rem;color:var(--sita-muted)">{{ $imp->archivo_nombre ?? '—' }}</td>
                        <td style="font-family:'Source Code Pro',monospace;font-size:0.82rem">{{ $imp->total_registros }}</td>
                        <td style="font-family:'Source Code Pro',monospace;font-size:0.82rem;color:var(--sita-success)">{{ $imp->exitosos }}</td>
                        <td style="font-family:'Source Code Pro',monospace;font-size:0.82rem;color:{{ $imp->fallidos > 0 ? 'var(--sita-danger)' : 'var(--sita-muted)' }}">{{ $imp->fallidos }}</td>
                        <td style="font-size:0.82rem">{{ $imp->usuario->nombre_completo ?? '—' }}</td>
                        <td>
                            @if($imp->fallidos > 0 && $imp->errores_detalle)
                            <button onclick="verErrores({{ json_encode($imp->errores_detalle) }})" class="btn btn-ghost btn-sm" style="color:var(--sita-danger);border-color:var(--sita-danger)">
                                Ver errores
                            </button>
                            @else
                            <span style="color:var(--sita-muted);font-size:0.75rem">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:2.5rem;color:var(--sita-muted);font-family:'Source Code Pro',monospace;font-size:0.78rem">
                            // Sin importaciones registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($importaciones->hasPages())
        <div style="padding:1rem 1.25rem;border-top:1px solid var(--sita-border)">
            {{ $importaciones->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal errores --}}
<div id="modal-errores" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:100;align-items:center;justify-content:center">
    <div style="background:var(--sita-surface);border:1px solid var(--sita-border);border-radius:8px;padding:1.5rem;max-width:500px;width:90%;max-height:70vh;overflow-y:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
            <span style="font-family:'Rajdhani',sans-serif;font-weight:600;font-size:1.1rem">Detalle de Errores</span>
            <button onclick="document.getElementById('modal-errores').style.display='none'" style="background:none;border:none;color:var(--sita-muted);cursor:pointer;font-size:1.2rem">✕</button>
        </div>
        <div id="modal-errores-content" style="font-family:'Source Code Pro',monospace;font-size:0.78rem;line-height:1.8;color:var(--sita-danger)"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Datos de catálogos para el formulario multi-fila
    const catalogos = {
        sitios: @json($sitios ?? []),
        ubicaciones: @json($ubicaciones ?? []),
        dispositivos: @json($dispositivos ?? []),
        marcas: @json($marcas ?? []),
        modelos: @json($modelos ?? []),
        statuses: @json($statuses ?? []),
    };

    let filaCount = 0;

    // ── Tabs ────────────────────────────────────────────────
    function showTab(id) {
        ['tab-excel', 'tab-form', 'tab-historial'].forEach(t => {
            document.getElementById(t).style.display = 'none';
        });
        document.getElementById(id).style.display = 'block';

        // Estilo botones
        document.querySelectorAll('[id^="btn-tab-"]').forEach(btn => {
            btn.style.borderBottomColor = 'transparent';
            btn.style.color = 'var(--sita-muted)';
        });
        const activeBtn = document.getElementById('btn-' + id);
        activeBtn.style.borderBottomColor = 'var(--sita-accent)';
        activeBtn.style.color = 'var(--sita-accent)';
    }

    // ── Drop zone ────────────────────────────────────────────
    function handleDrop(e) {
        e.preventDefault();
        document.getElementById('drop-zone').style.borderColor = 'var(--sita-border)';
        const file = e.dataTransfer.files[0];
        if (file) {
            document.getElementById('archivo').files = e.dataTransfer.files;
            onFileSelected({
                files: [file]
            });
        }
    }

    function onFileSelected(input) {
        const file = input.files[0];
        if (file) {
            document.getElementById('drop-text').innerHTML =
                `<span style="color:var(--sita-success)">✓ ${file.name}</span>`;
            document.getElementById('btn-subir').disabled = false;
            document.getElementById('btn-subir').style.opacity = '1';
        }
    }

    // ── Multi-fila ───────────────────────────────────────────
    function buildSelect(name, items, labelKey, valueKey = 'id', placeholder = '— Seleccionar —') {
        let opts = `<option value="">${placeholder}</option>`;
        items.forEach(i => {
            opts += `<option value="${i[valueKey]}">${i[labelKey]}</option>`;
        });
        return `<select name="${name}" required
        style="background:var(--sita-bg);border:1px solid var(--sita-border);color:var(--sita-text);
               padding:0.35rem 0.5rem;font-size:0.78rem;border-radius:3px;width:100%;min-width:110px"
        onchange="onSelectChange(this)">${opts}</select>`;
    }

    function buildInput(name, placeholder = '') {
        return `<input type="text" name="${name}" placeholder="${placeholder}"
        style="background:var(--sita-bg);border:1px solid var(--sita-border);color:var(--sita-text);
               padding:0.35rem 0.5rem;font-size:0.78rem;border-radius:3px;width:100%;min-width:110px;
               font-family:'Source Code Pro',monospace">`;
    }

    function agregarFila() {
        const i = filaCount++;
        const tr = document.createElement('tr');
        tr.id = `fila-${i}`;
        tr.style.borderBottom = '1px solid rgba(30,45,69,0.5)';

        tr.innerHTML = `
        <td style="padding:0.5rem 0.75rem;color:var(--sita-muted);font-family:'Source Code Pro',monospace;font-size:0.72rem">${i + 1}</td>
        <td style="padding:0.4rem 0.5rem">${buildSelect(`filas[${i}][id_sitio]`, catalogos.sitios, 'clave')}</td>
        <td style="padding:0.4rem 0.5rem">
            <select name="filas[${i}][id_ubicacion]" required data-fila="${i}" data-tipo="ubicacion"
                style="background:var(--sita-bg);border:1px solid var(--sita-border);color:var(--sita-text);
                       padding:0.35rem 0.5rem;font-size:0.78rem;border-radius:3px;width:100%;min-width:110px">
                <option value="">— Elige sitio —</option>
            </select>
        </td>
        <td style="padding:0.4rem 0.5rem">${buildSelect(`filas[${i}][id_dispositivo]`, catalogos.dispositivos, 'tipo')}</td>
        <td style="padding:0.4rem 0.5rem">${buildSelect(`filas[${i}][id_marca]`, catalogos.marcas, 'nombre')}</td>
        <td style="padding:0.4rem 0.5rem">
            <select name="filas[${i}][id_modelo]" required data-fila="${i}" data-tipo="modelo"
                style="background:var(--sita-bg);border:1px solid var(--sita-border);color:var(--sita-text);
                       padding:0.35rem 0.5rem;font-size:0.78rem;border-radius:3px;width:100%;min-width:110px">
                <option value="">— Elige marca —</option>
            </select>
        </td>
        <td style="padding:0.4rem 0.5rem">${buildInput(`filas[${i}][serial_number]`, 'Serial...')}</td>
        <td style="padding:0.4rem 0.5rem">${buildInput(`filas[${i}][sita_asset_tag]`, 'Asset tag...')}</td>
        <td style="padding:0.4rem 0.5rem">${buildSelect(`filas[${i}][id_status]`, catalogos.statuses, 'nombre')}</td>
        <td style="padding:0.4rem 0.75rem">
            <button type="button" onclick="eliminarFila(${i})"
                style="background:none;border:none;color:var(--sita-danger);cursor:pointer;font-size:1rem">✕</button>
        </td>
    `;

        // Vincular sitio → ubicaciones
        const selectSitio = tr.querySelector(`select[name="filas[${i}][id_sitio]"]`);
        selectSitio.addEventListener('change', function() {
            const idSitio = parseInt(this.value);
            const selectUb = tr.querySelector(`select[data-tipo="ubicacion"]`);
            selectUb.innerHTML = '<option value="">— Seleccionar —</option>';
            catalogos.ubicaciones
                .filter(u => u.id_sitio === idSitio)
                .forEach(u => selectUb.innerHTML += `<option value="${u.id}">${u.nombre}</option>`);
        });

        // Vincular marca → modelos
        const selectMarca = tr.querySelector(`select[name="filas[${i}][id_marca]"]`);
        selectMarca.addEventListener('change', function() {
            const idMarca = parseInt(this.value);
            const selectMo = tr.querySelector(`select[data-tipo="modelo"]`);
            selectMo.innerHTML = '<option value="">— Seleccionar —</option>';
            catalogos.modelos
                .filter(m => m.id_marca === idMarca)
                .forEach(m => selectMo.innerHTML += `<option value="${m.id}">${m.numero_modelo}</option>`);
        });

        document.getElementById('tabla-filas').appendChild(tr);
        actualizarContador();
    }

    function eliminarFila(i) {
        const fila = document.getElementById(`fila-${i}`);
        if (fila) fila.remove();
        actualizarContador();
    }

    function limpiarFilas() {
        if (confirm('¿Limpiar todas las filas?')) {
            document.getElementById('tabla-filas').innerHTML = '';
            filaCount = 0;
            actualizarContador();
        }
    }

    function actualizarContador() {
        const n = document.getElementById('tabla-filas').children.length;
        document.getElementById('contador-filas').textContent = `${n} fila${n !== 1 ? 's' : ''}`;
        document.getElementById('btn-guardar-lote').disabled = n === 0;
    }

    // ── Modal errores ────────────────────────────────────────
    function verErrores(errores) {
        const html = errores.map(e =>
            `<div>Fila ${e.fila ?? '?'}: ${e.mensaje ?? JSON.stringify(e)}</div>`
        ).join('');
        document.getElementById('modal-errores-content').innerHTML = html;
        document.getElementById('modal-errores').style.display = 'flex';
    }

    // Agregar primera fila automáticamente al cargar el tab
    document.getElementById('btn-subir').disabled = true;
</script>
@endpush
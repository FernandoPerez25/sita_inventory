@extends('layouts.app')

@section('title', 'Usuarios — SITA')
@section('breadcrumb', 'SITA / Usuarios')

@section('content')
<div class="page-header animate-in">
    <div>
        <h1 class="page-title">Usuarios</h1>
        <p class="page-subtitle">// Gestión de accesos al sistema</p>
    </div>
    <button onclick="abrirModal('modal-crear')" class="btn btn-accent">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
        </svg>
        Nuevo Usuario
    </button>
</div>

{{-- Tabla --}}
<div class="card animate-in delay-1">
    <div class="table-wrapper" style="border:none;border-radius:6px">
        <table class="sita-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th style="text-align:right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                <tr>
                    <td style="font-family:'Source Code Pro',monospace;font-size:0.72rem;color:var(--sita-muted)">{{ $usuario->id }}</td>

                    <td>
                        <div style="display:flex;align-items:center;gap:0.6rem">
                            <div style="width:30px;height:30px;background:var(--sita-surface2);border:1px solid var(--sita-border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Rajdhani',sans-serif;font-size:0.82rem;font-weight:700;color:var(--sita-accent);flex-shrink:0">
                                {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-size:0.85rem;font-weight:500">{{ $usuario->nombre_completo }}</div>
                            </div>
                        </div>
                    </td>

                    <td class="td-mono">{{ $usuario->username }}</td>
                    <td style="font-size:0.82rem;color:var(--sita-muted)">{{ $usuario->email }}</td>

                    <td>
                        @php
                        $rolColor = ['admin'=>'badge-gap','usuario'=>'badge-instalado','consultor'=>'badge-spare'];
                        $rc = $rolColor[$usuario->rol->nombre] ?? 'badge-bodega';
                        @endphp
                        <span class="badge {{ $rc }}">{{ $usuario->rol->nombre }}</span>
                    </td>

                    <td>
                        <span class="badge {{ $usuario->activo ? 'badge-instalado' : 'badge-danado' }}">
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>

                    <td style="text-align:right;white-space:nowrap">
                        @if($usuario->id !== auth()->id())
                        <button onclick="abrirEditar({{ $usuario->id }}, {{ Js::from($usuario) }})"
                            class="btn btn-ghost btn-sm" title="Editar">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button onclick="abrirReset({{ $usuario->id }}, '{{ $usuario->username }}')"
                            class="btn btn-ghost btn-sm" title="Cambiar contraseña">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" style="display:inline"
                            onsubmit="return confirm('¿Desactivar al usuario {{ $usuario->username }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" title="Desactivar"
                                @if($usuario->activo)
                                style="color:var(--sita-danger)"
                                @else
                                style="color:var(--sita-success)"
                                @endif>
                                @if($usuario->activo)
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                @else
                                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @endif
                            </button>
                        </form>
                        @else
                        <span style="font-size:0.72rem;color:var(--sita-muted);font-family:'Source Code Pro',monospace">(tú)</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:3rem;color:var(--sita-muted);font-family:'Source Code Pro',monospace;font-size:0.78rem">
                        // Sin usuarios registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($usuarios->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--sita-border)">
        {{ $usuarios->links() }}
    </div>
    @endif
</div>

{{-- ══ MODAL: Crear Usuario ══ --}}
<div id="modal-crear" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:100;align-items:center;justify-content:center">
    <div style="background:var(--sita-surface);border:1px solid var(--sita-border);border-radius:8px;padding:0;max-width:480px;width:92%;box-shadow:0 30px 60px rgba(0,0,0,0.5)">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--sita-border);display:flex;justify-content:space-between;align-items:center">
            <span style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:600">Nuevo Usuario</span>
            <button onclick="cerrarModal('modal-crear')" style="background:none;border:none;color:var(--sita-muted);cursor:pointer;font-size:1.2rem">✕</button>
        </div>
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
                <div class="form-grid-2">
                    <div>
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" class="form-control" required value="{{ old('nombre') }}">
                    </div>
                    <div>
                        <label class="form-label">Apellidos *</label>
                        <input type="text" name="apellidos" class="form-control" required value="{{ old('apellidos') }}">
                    </div>
                </div>
                <div class="form-grid-2">
                    <div>
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-control font-mono" required value="{{ old('username') }}" autocomplete="off">
                    </div>
                    <div>
                        <label class="form-label">Rol *</label>
                        <select name="id_rol" class="form-control" required>
                            @foreach($roles as $rol)
                            <option value="{{ $rol->id }}">{{ ucfirst($rol->nombre) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>
                <div class="form-grid-2">
                    <div>
                        <label class="form-label">Contraseña *</label>
                        <input type="password" name="password" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="form-label">Confirmar *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
            </div>
            <div style="padding:1rem 1.5rem;border-top:1px solid var(--sita-border);display:flex;justify-content:flex-end;gap:0.5rem">
                <button type="button" onclick="cerrarModal('modal-crear')" class="btn btn-ghost">Cancelar</button>
                <button type="submit" class="btn btn-accent">Crear Usuario</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL: Editar Usuario ══ --}}
<div id="modal-editar" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:100;align-items:center;justify-content:center">
    <div style="background:var(--sita-surface);border:1px solid var(--sita-border);border-radius:8px;padding:0;max-width:480px;width:92%">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--sita-border);display:flex;justify-content:space-between;align-items:center">
            <span style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:600">Editar Usuario</span>
            <button onclick="cerrarModal('modal-editar')" style="background:none;border:none;color:var(--sita-muted);cursor:pointer;font-size:1.2rem">✕</button>
        </div>
        <form method="POST" id="form-editar">
            @csrf @method('PUT')
            <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
                <div class="form-grid-2">
                    <div>
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Apellidos *</label>
                        <input type="text" name="apellidos" id="edit-apellidos" class="form-control" required>
                    </div>
                </div>
                <div>
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" id="edit-email" class="form-control" required>
                </div>
                <div class="form-grid-2">
                    <div>
                        <label class="form-label">Rol *</label>
                        <select name="id_rol" id="edit-rol" class="form-control" required>
                            @foreach($roles as $rol)
                            <option value="{{ $rol->id }}">{{ ucfirst($rol->nombre) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Estado</label>
                        <select name="activo" id="edit-activo" class="form-control">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="padding:1rem 1.5rem;border-top:1px solid var(--sita-border);display:flex;justify-content:flex-end;gap:0.5rem">
                <button type="button" onclick="cerrarModal('modal-editar')" class="btn btn-ghost">Cancelar</button>
                <button type="submit" class="btn btn-accent">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL: Reset Password ══ --}}
<div id="modal-reset" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:100;align-items:center;justify-content:center">
    <div style="background:var(--sita-surface);border:1px solid var(--sita-border);border-radius:8px;padding:0;max-width:400px;width:92%">
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--sita-border);display:flex;justify-content:space-between;align-items:center">
            <span style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:600">Restablecer Contraseña</span>
            <button onclick="cerrarModal('modal-reset')" style="background:none;border:none;color:var(--sita-muted);cursor:pointer;font-size:1.2rem">✕</button>
        </div>
        <form method="POST" id="form-reset">
            @csrf @method('PATCH')
            <div style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
                <p id="reset-username-label" style="font-size:0.85rem;color:var(--sita-muted)"></p>
                <div>
                    <label class="form-label">Nueva Contraseña *</label>
                    <input type="password" name="password" class="form-control" required minlength="8" autocomplete="new-password">
                </div>
                <div>
                    <label class="form-label">Confirmar *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>
            <div style="padding:1rem 1.5rem;border-top:1px solid var(--sita-border);display:flex;justify-content:flex-end;gap:0.5rem">
                <button type="button" onclick="cerrarModal('modal-reset')" class="btn btn-ghost">Cancelar</button>
                <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function abrirModal(id) {
        document.getElementById(id).style.display = 'flex';
    }

    function cerrarModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    function abrirEditar(id, usuario) {
        document.getElementById('form-editar').action = `/usuarios/${id}`;
        document.getElementById('edit-nombre').value = usuario.nombre;
        document.getElementById('edit-apellidos').value = usuario.apellidos;
        document.getElementById('edit-email').value = usuario.email;
        document.getElementById('edit-rol').value = usuario.id_rol;
        document.getElementById('edit-activo').value = usuario.activo ? '1' : '0';
        abrirModal('modal-editar');
    }

    function abrirReset(id, username) {
        document.getElementById('form-reset').action = `/usuarios/${id}/reset-password`;
        document.getElementById('reset-username-label').textContent =
            `Restablecer contraseña para: ${username}`;
        abrirModal('modal-reset');
    }

    // Cerrar modal al hacer click fuera
    ['modal-crear', 'modal-editar', 'modal-reset'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) cerrarModal(id);
        });
    });

    // Abrir modal si hay errores de validación
    @if($errors->any())
    abrirModal('modal-crear');
    @endif
</script>
@endpush
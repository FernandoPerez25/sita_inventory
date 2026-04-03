<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table    = 'usuarios';
    protected $fillable = ['id_rol', 'nombre', 'apellidos', 'username', 'email', 'password', 'activo'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
        'activo'   => 'boolean',
    ];

    // ── Relaciones ──────────────────────────────────────

    public function rol()
    {
        return $this->belongsTo(Role::class, 'id_rol');
    }

    public function inventariosRegistrados()
    {
        return $this->hasMany(Inventario::class, 'id_usuario_reg');
    }

    public function historial()
    {
        return $this->hasMany(HistorialInventario::class, 'id_usuario');
    }

    // ── Helpers de roles ────────────────────────────────

    public function esAdmin(): bool
    {
        return $this->rol->nombre === 'admin';
    }

    public function esUsuario(): bool
    {
        return $this->rol->nombre === 'usuario';
    }

    public function esConsultor(): bool
    {
        return $this->rol->nombre === 'consultor';
    }

    public function puedeEditar(): bool
    {
        return in_array($this->rol->nombre, ['admin', 'usuario']);
    }

    // ── Scopes ──────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    // Nombre completo
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }
}

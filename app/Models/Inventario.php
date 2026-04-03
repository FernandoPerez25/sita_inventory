<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventario extends Model
{
    use SoftDeletes;

    protected $table = 'inventario';

    protected $fillable = [
        'id_sitio',
        'id_ubicacion',
        'id_dispositivo',
        'id_marca',
        'id_modelo',
        'id_status',
        'serial_number',
        'sita_asset_tag',
        'po_number',
        'gap_active',
        'nodename',
        'comentarios',
        'qr_code',
        'id_usuario_reg',
        'id_usuario_mod',
    ];

    // ── Relaciones ──────────────────────────────────────

    public function sitio()
    {
        return $this->belongsTo(CatSitio::class, 'id_sitio');
    }

    public function ubicacion()
    {
        return $this->belongsTo(CatUbicacion::class, 'id_ubicacion');
    }

    public function dispositivo()
    {
        return $this->belongsTo(CatDispositivo::class, 'id_dispositivo');
    }

    public function marca()
    {
        return $this->belongsTo(CatMarca::class, 'id_marca');
    }

    public function modelo()
    {
        return $this->belongsTo(CatModelo::class, 'id_modelo');
    }

    public function status()
    {
        return $this->belongsTo(CatStatus::class, 'id_status');
    }

    public function usuarioRegistro()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_reg');
    }

    public function usuarioModificacion()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_mod');
    }

    public function historial()
    {
        return $this->hasMany(HistorialInventario::class, 'id_inventario')->latest();
    }

    // ── Scopes de filtrado ───────────────────────────────

    public function scopePorSitio($query, $idSitio)
    {
        return $query->when($idSitio, fn($q) => $q->where('id_sitio', $idSitio));
    }

    public function scopePorStatus($query, $idStatus)
    {
        return $query->when($idStatus, fn($q) => $q->where('id_status', $idStatus));
    }

    public function scopePorDispositivo($query, $idDispositivo)
    {
        return $query->when($idDispositivo, fn($q) => $q->where('id_dispositivo', $idDispositivo));
    }

    public function scopeBuscar($query, ?string $texto)
    {
        return $query->when($texto, function ($q) use ($texto) {
            $q->where(function ($inner) use ($texto) {
                $inner->where('serial_number',  'like', "%{$texto}%")
                    ->orWhere('sita_asset_tag', 'like', "%{$texto}%")
                    ->orWhere('nodename',      'like', "%{$texto}%")
                    ->orWhere('po_number',     'like', "%{$texto}%");
            });
        });
    }
}

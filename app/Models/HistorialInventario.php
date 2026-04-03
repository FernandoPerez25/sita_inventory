<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialInventario extends Model
{
    protected $table    = 'historial_inventario';
    public $timestamps  = false;

    protected $fillable = [
        'id_inventario',
        'id_usuario',
        'campo_modificado',
        'valor_anterior',
        'valor_nuevo',
        'ip_origen',
        'origen',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Solo created_at, no updated_at
    const UPDATED_AT = null;

    public function inventario()
    {
        return $this->belongsTo(Inventario::class, 'id_inventario');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
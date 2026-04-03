<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Importacion extends Model
{
    protected $table    = 'importaciones';
    protected $fillable = [
        'id_usuario',
        'metodo',
        'total_registros',
        'exitosos',
        'fallidos',
        'archivo_nombre',
        'errores_detalle',
    ];

    protected $casts = [
        'errores_detalle' => 'array',
        'created_at'      => 'datetime',
    ];

    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}

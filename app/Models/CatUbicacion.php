<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatUbicacion extends Model
{
    protected $table    = 'cat_ubicaciones';
    protected $fillable = ['id_sitio', 'nombre', 'activo'];
    public $timestamps  = false;

    public function sitio()
    {
        return $this->belongsTo(CatSitio::class, 'id_sitio');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_ubicacion');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', 1)->orderBy('nombre');
    }

    public function scopePorSitio($query, int $idSitio)
    {
        return $query->where('id_sitio', $idSitio);
    }
}
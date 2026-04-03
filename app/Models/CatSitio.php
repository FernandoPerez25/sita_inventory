<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatSitio extends Model
{
    protected $table    = 'cat_sitios';
    protected $fillable = ['clave', 'nombre', 'activo'];
    public $timestamps  = false;

    public function ubicaciones()
    {
        return $this->hasMany(CatUbicacion::class, 'id_sitio');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_sitio');
    }

    // Solo los activos
    public function scopeActivos($query)
    {
        return $query->where('activo', 1)->orderBy('clave');
    }
}
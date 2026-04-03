<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatMarca extends Model
{
    protected $table    = 'cat_marcas';
    protected $fillable = ['nombre', 'activo'];
    public $timestamps  = false;

    public function modelos()
    {
        return $this->hasMany(CatModelo::class, 'id_marca');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_marca');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', 1)->orderBy('nombre');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatDispositivo extends Model
{
    protected $table    = 'cat_dispositivos';
    protected $fillable = ['tipo', 'descripcion', 'activo'];
    public $timestamps  = false;

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_dispositivo');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', 1)->orderBy('tipo');
    }
}

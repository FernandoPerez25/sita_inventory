<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatStatus extends Model
{
    protected $table    = 'cat_status';
    protected $fillable = ['nombre', 'descripcion', 'activo'];
    public $timestamps  = false;

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_status');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', 1)->orderBy('nombre');
    }
}
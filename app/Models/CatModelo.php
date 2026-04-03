<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatModelo extends Model
{
    protected $table    = 'cat_modelos';
    protected $fillable = ['id_marca', 'numero_modelo', 'activo'];
    public $timestamps  = false;

    public function marca()
    {
        return $this->belongsTo(CatMarca::class, 'id_marca');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'id_modelo');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', 1)->orderBy('numero_modelo');
    }

    public function scopePorMarca($query, int $idMarca)
    {
        return $query->where('id_marca', $idMarca);
    }
}
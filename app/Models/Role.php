<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table    = 'roles';
    protected $fillable = ['nombre', 'descripcion'];
    public $timestamps  = false;

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol');
    }
}
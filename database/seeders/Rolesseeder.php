<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'nombre' => 'admin',     'descripcion' => 'Administrador del sistema'],
            ['id' => 2, 'nombre' => 'usuario',   'descripcion' => 'Usuario estándar'],
            ['id' => 3, 'nombre' => 'consultor', 'descripcion' => 'Solo lectura'],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(['id' => $rol['id']], $rol);
        }
    }
}

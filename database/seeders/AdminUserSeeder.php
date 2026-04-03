<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::updateOrCreate(
            ['username' => 'admin'],
            [
                'email'     => 'sita@admin.com',
                'nombre'    => 'Admin',
                'apellidos' => 'Principal',
                'password'  => Hash::make('Sita2026'),
                'activo'    => 1,
                'id_rol'    => 1,
            ]
        );
    }
}

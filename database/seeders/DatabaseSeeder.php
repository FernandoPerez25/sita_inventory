<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Seeder de roles
        // $this->call(RoleSeeder::class);

        // Seeder de admin
        $this->call(AdminUserSeeder::class);

    }
}

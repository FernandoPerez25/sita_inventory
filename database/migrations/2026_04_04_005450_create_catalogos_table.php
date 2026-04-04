<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_sitios', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->string('nombre');
            $table->boolean('activo')->default(1);
        });

        Schema::create('cat_ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sitio')->constrained('cat_sitios');
            $table->string('nombre');
            $table->boolean('activo')->default(1);
        });

        Schema::create('cat_dispositivos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(1);
        });

        Schema::create('cat_marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->boolean('activo')->default(1);
        });

        Schema::create('cat_modelos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_marca')->constrained('cat_marcas');
            $table->string('numero_modelo');
            $table->boolean('activo')->default(1);
        });

        Schema::create('cat_status', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_modelos');
        Schema::dropIfExists('cat_marcas');
        Schema::dropIfExists('cat_dispositivos');
        Schema::dropIfExists('cat_ubicaciones');
        Schema::dropIfExists('cat_sitios');
        Schema::dropIfExists('cat_status');
    }
};

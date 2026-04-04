<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->constrained('usuarios');
            $table->string('metodo');
            $table->unsignedInteger('total_registros')->default(0);
            $table->unsignedInteger('exitosos')->default(0);
            $table->unsignedInteger('fallidos')->default(0);
            $table->string('archivo_nombre')->nullable();
            $table->json('errores_detalle')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importaciones');
    }
};

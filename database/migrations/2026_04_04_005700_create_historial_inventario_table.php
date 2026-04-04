<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_inventario')->constrained('inventario');
            $table->foreignId('id_usuario')->constrained('usuarios');
            $table->string('campo_modificado');
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->string('origen')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_inventario');
    }
};

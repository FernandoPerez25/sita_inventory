<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sitio')->constrained('cat_sitios');
            $table->foreignId('id_ubicacion')->constrained('cat_ubicaciones');
            $table->foreignId('id_dispositivo')->constrained('cat_dispositivos');
            $table->foreignId('id_marca')->constrained('cat_marcas');
            $table->foreignId('id_modelo')->constrained('cat_modelos');
            $table->foreignId('id_status')->constrained('cat_status');
            $table->string('serial_number')->nullable();
            $table->string('sita_asset_tag')->nullable();
            $table->string('po_number')->nullable();
            $table->string('gap_active')->nullable();
            $table->string('nodename')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('qr_code')->nullable();
            $table->foreignId('id_usuario_reg')->constrained('usuarios');
            $table->foreignId('id_usuario_mod')->nullable()->constrained('usuarios');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};

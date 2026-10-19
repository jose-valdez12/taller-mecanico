<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_solicitudes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('solicitud_repuesto_id')
                ->constrained('solicitud_repuestos');

            $table->foreignId('repuesto_id')
                ->constrained('repuestos');

            $table->integer('cantidad');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_solicitudes');
    }
};

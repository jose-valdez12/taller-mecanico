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

            // Relación con la solicitud
            $table->foreignId('solicitud_repuesto_id')
                ->constrained('solicitud_repuestos');

            // Relación con el repuesto
            $table->foreignId('repuesto_id')
                ->constrained('repuestos');

            // Cantidad solicitada
            $table->unsignedInteger('cantidad');

            // Precio al momento del despacho
            $table->decimal('precio_unitario', 10, 2)
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_solicitudes');
    }
};

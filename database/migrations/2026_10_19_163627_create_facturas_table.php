<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();

            // Relación con reparación
            // Evitar facturas duplicadas
            $table->foreignId('reparacion_id')
                ->unique()
                ->constrained('reparaciones');

            // Total de la factura
            $table->decimal('total', 10, 2);

            // Fecha de emisión
            $table->date('fecha_emision');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};

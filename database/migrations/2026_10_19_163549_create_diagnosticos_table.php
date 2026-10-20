<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();

            // Relación con el ingreso del vehículo
            $table->foreignId('ingreso_id')
                ->constrained('ingresos');

            // Fallas encontradas
            $table->text('descripcion_fallas');

            // Reparaciones necesarias
            $table->text('reparaciones_necesarias');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};

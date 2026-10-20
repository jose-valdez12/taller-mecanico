<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();

            // Relación con el diagnóstico
            $table->foreignId('diagnostico_id')
                ->unique()
                ->constrained('diagnosticos');

            // Información de la reparación
            $table->text('descripcion');

            // Costo de mano de obra
            $table->decimal('costo_mano_obra', 10, 2)
                ->default(0);

            // Estado de la reparación
            $table->enum('estado', [
                'pendiente',
                'en_proceso',
                'finalizada'
            ])->default('pendiente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reparaciones');
    }
};

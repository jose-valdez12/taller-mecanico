<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();

            // Vehículo que ingresa al taller
            $table->foreignId('vehiculo_id')
                ->constrained('vehiculos');

            // Mecánico responsable
            $table->foreignId('mecanico_id')
                ->nullable()
                ->constrained('users');

            // Fechas de ingreso y salida
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_salida')->nullable();

            // Motivo del ingreso
            $table->text('motivo_ingreso');

            // Estado del vehículo
            $table->enum('estado', [
                'recibido',
                'en_diagnostico',
                'en_reparacion',
                'finalizado',
                'entregado'
            ])->default('recibido');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};

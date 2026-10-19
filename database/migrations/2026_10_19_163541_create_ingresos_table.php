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

            $table->foreignId('vehiculo_id')
                ->constrained('vehiculos');

            $table->foreignId('mecanico_id')
                ->nullable()
                ->constrained('users');

            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_salida')->nullable();
            $table->text('motivo_ingreso');

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


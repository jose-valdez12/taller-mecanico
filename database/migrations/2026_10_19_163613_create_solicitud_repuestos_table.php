<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_repuestos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reparacion_id')
                ->constrained('reparaciones');

            $table->foreignId('mecanico_id')
                ->constrained('users');

            $table->enum('estado', [
                'pendiente',
                'aprobada',
                'rechazada',
                'despachada'
            ])->default('pendiente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_repuestos');
    }
};

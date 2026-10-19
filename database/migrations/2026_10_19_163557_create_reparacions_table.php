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

            $table->foreignId('diagnostico_id')
                ->constrained('diagnosticos');

            $table->text('descripcion');
            $table->decimal('costo_mano_obra', 10, 2)->default(0);

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


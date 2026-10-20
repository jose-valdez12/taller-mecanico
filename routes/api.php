<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExistenciaController;

Route::middleware(['web', 'auth', 'role:administrador,bodeguero'])
    ->get('/repuestos/{repuesto}/existencias',
        [ExistenciaController::class, 'consultar'])
    ->name('api.repuestos.existencias');


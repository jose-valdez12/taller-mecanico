<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExistenciaController;

// API REST para consultar existencias en bodega
Route::middleware([
    'web',
    'auth',
    'role:administrador,bodeguero'
])->group(function () {

    Route::get(
        '/repuestos/{repuesto}/existencias',
        [ExistenciaController::class, 'consultar']
    )->name('api.repuestos.existencias');

});

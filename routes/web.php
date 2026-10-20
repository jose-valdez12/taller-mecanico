<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VehiculosController;
use App\Http\Controllers\IngresoController;


// Página principal
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas para usuarios no autenticados
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'mostrarLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.procesar');
});

// Rutas para usuarios autenticados
Route::middleware('auth')->group(function () {

    // Panel principal
    Route::get('/dashboard', [AuthController::class, 'dashboard'])
        ->name('dashboard');

    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Administrador
    Route::get('/administrador', function () {
        return view('dashboard');
    })->middleware('role:administrador')->name('administrador');

    // Recepción
    Route::get('/recepcion', function () {
        return view('dashboard');
    })->middleware('role:administrador,recepcion')->name('recepcion');

    // Mecánico
    Route::get('/mecanico', function () {
        return view('dashboard');
    })->middleware('role:administrador,mecanico')->name('mecanico');

    // Bodeguero
    Route::get('/bodega', function () {
        return view('dashboard');
    })->middleware('role:administrador,bodeguero')->name('bodega');

    Route::middleware('role:administrador')->group(function(){
          Route::resource('usuarios', UsuarioController::class)
        ->except(['show']);
    });

    //Roles
    Route::middleware('role:administrador,recepcion')
    ->group(function () {

        Route::resource('clientes', ClienteController::class)
            ->except(['show', 'destroy']);

    });

//vehiculos
      Route::middleware('role:administrador,recepcion')
        ->group(function () {

            Route::resource('vehiculos', VehiculoController::class)
                ->except(['show', 'destroy']);
        });

        //Ingreso y salidas
         Route::middleware('role:administrador,recepcion')
        ->group(function () {

            Route::get('/ingresos', [IngresoController::class, 'index'])
                ->name('ingresos.index');

            Route::get('/ingresos/create', [IngresoController::class, 'create'])
                ->name('ingresos.create');

            Route::post('/ingresos', [IngresoController::class, 'store'])
                ->name('ingresos.store');

            Route::patch('/ingresos/{ingreso}/salida', [IngresoController::class, 'salida'])
                ->name('ingresos.salida');
        });
});

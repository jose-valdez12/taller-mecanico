<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\IngresoController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\ReparacionController;
use App\Http\Controllers\SolicitudRepuestoController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\BodegaController;
use App\Http\Controllers\FacturaController;

// Página principal
Route::get('/', function () {
    return redirect()->route('login');
});

// Inicio de sesión
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

    // Panel del administrador
    Route::get('/administrador', function () {
        return view('dashboard');
    })->middleware('role:administrador')
      ->name('administrador');

    // Panel de recepción
    Route::get('/recepcion', function () {
        return view('dashboard');
    })->middleware('role:administrador,recepcion')
      ->name('recepcion');

    // Panel del mecánico
    Route::get('/mecanico', function () {
        return view('dashboard');
    })->middleware('role:administrador,mecanico')
      ->name('mecanico');

    // Panel del bodeguero
    Route::get('/bodega', function () {
        return view('dashboard');
    })->middleware('role:administrador,bodeguero')
      ->name('bodega');

    // =====================================
    // ADMINISTRADOR
    // =====================================
    Route::middleware('role:administrador')->group(function () {

        Route::resource('usuarios', UsuarioController::class)
            ->except(['show']);

    });

    // =====================================
    // RECEPCIÓN
    // =====================================
    Route::middleware('role:administrador,recepcion')->group(function () {

        // Clientes
        Route::resource('clientes', ClienteController::class)
            ->except(['show', 'destroy']);

        // Vehículos
        Route::resource('vehiculos', VehiculoController::class)
            ->except(['show', 'destroy']);

        // Ingresos y salidas
        Route::get('/ingresos', [IngresoController::class, 'index'])
            ->name('ingresos.index');

        Route::get('/ingresos/create', [IngresoController::class, 'create'])
            ->name('ingresos.create');

        Route::post('/ingresos', [IngresoController::class, 'store'])
            ->name('ingresos.store');

        Route::patch('/ingresos/{ingreso}/salida', [IngresoController::class, 'salida'])
            ->name('ingresos.salida');

    });

    // =====================================
    // MECÁNICO
    // =====================================
    Route::middleware('role:administrador,mecanico')->group(function () {

        // Diagnósticos
        Route::get('/diagnosticos', [DiagnosticoController::class, 'index'])
            ->name('diagnosticos.index');

        Route::get('/diagnosticos/create', [DiagnosticoController::class, 'create'])
            ->name('diagnosticos.create');

        Route::post('/diagnosticos', [DiagnosticoController::class, 'store'])
            ->name('diagnosticos.store');

        // Reparaciones
        Route::get('/reparaciones', [ReparacionController::class, 'index'])
            ->name('reparaciones.index');

        Route::get('/reparaciones/create', [ReparacionController::class, 'create'])
            ->name('reparaciones.create');

        Route::post('/reparaciones', [ReparacionController::class, 'store'])
            ->name('reparaciones.store');

        Route::patch('/reparaciones/{reparacion}/estado',
            [ReparacionController::class, 'actualizarEstado'])
            ->name('reparaciones.estado');

        // Solicitudes de repuestos
        Route::get('/solicitudes', [SolicitudRepuestoController::class, 'index'])
            ->name('solicitudes.index');

        Route::get('/solicitudes/create', [SolicitudRepuestoController::class, 'create'])
            ->name('solicitudes.create');

        Route::post('/solicitudes', [SolicitudRepuestoController::class, 'store'])
            ->name('solicitudes.store');

    });

    // =====================================
    // BODEGUERO
    // =====================================
    Route::middleware('role:administrador,bodeguero')->group(function () {

        // Inventario
        Route::resource('repuestos', RepuestoController::class)
            ->except(['show', 'destroy']);

        // Solicitudes de Bodega
        Route::get('/bodega/solicitudes', [BodegaController::class, 'index'])
            ->name('bodega.index');

        Route::patch('/bodega/solicitudes/{solicitud}/resolver',
            [BodegaController::class, 'resolver'])
            ->name('bodega.resolver');

        Route::patch('/bodega/solicitudes/{solicitud}/despachar',
            [BodegaController::class, 'despachar'])
            ->name('bodega.despachar');

    });

    // =====================================
    // FACTURACIÓN
    // =====================================
    Route::middleware('role:administrador,recepcion')->group(function () {

        Route::get('/facturas', [FacturaController::class, 'index'])
            ->name('facturas.index');

        Route::get('/facturas/create', [FacturaController::class, 'create'])
            ->name('facturas.create');

        Route::post('/facturas', [FacturaController::class, 'store'])
            ->name('facturas.store');

        Route::get('/facturas/{factura}', [FacturaController::class, 'show'])
            ->name('facturas.show');

    });

});

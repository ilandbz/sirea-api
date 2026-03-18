<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExpedienteController;
use App\Http\Controllers\Api\NotificacionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;

// --- RUTAS PÚBLICAS ---
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS (Requieren Token) ---
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/expedientes', [ExpedienteController::class, 'index']); // Para listar en la tabla
    Route::post('/expedientes', [ExpedienteController::class, 'store']);
    Route::get('/notificaciones', [NotificacionController::class, 'index']);
    Route::get('/usuarios', [UserController::class, 'index']);
    Route::post('/usuarios', [UserController::class, 'registrarUsuario']);
    // Ruta solo para Operadores (usaremos un middleware personalizado)
    Route::post('/notificaciones', [NotificacionController::class, 'store'])
        ->middleware('role:operador');
    Route::get('/expedientes/buscar', [ExpedienteController::class, 'buscar']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

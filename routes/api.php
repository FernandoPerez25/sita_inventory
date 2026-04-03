<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\InventarioApiController;
use App\Http\Controllers\Api\CatalogoApiController;

// ═══════════════════════════════════════════════════════
//  RUTAS PÚBLICAS — No requieren token
// ═══════════════════════════════════════════════════════
Route::post('/login',  [AuthApiController::class, 'login']);


// ═══════════════════════════════════════════════════════
//  RUTAS PROTEGIDAS — Requieren Bearer Token (Sanctum)
// ═══════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

    // ── Sesión ──────────────────────────────────────────
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me',      [AuthApiController::class, 'me']);

    // ── Catálogos ────────────────────────────────────────
    // Carga todos los catálogos de un golpe al iniciar sesión
    Route::get('/catalogos/todos',                       [CatalogoApiController::class, 'todos']);
    Route::get('/catalogos/sitios',                      [CatalogoApiController::class, 'sitios']);
    Route::get('/catalogos/ubicaciones',                 [CatalogoApiController::class, 'ubicaciones']);
    Route::get('/catalogos/dispositivos',                [CatalogoApiController::class, 'dispositivos']);
    Route::get('/catalogos/marcas',                      [CatalogoApiController::class, 'marcas']);
    Route::get('/catalogos/modelos/{id_marca}',          [CatalogoApiController::class, 'modelos']);
    Route::get('/catalogos/status',                      [CatalogoApiController::class, 'status']);

    // ── Inventario ───────────────────────────────────────
    // IMPORTANTE: la ruta /qr/{codigo} debe ir ANTES de /{id}
    // para evitar que Laravel interprete "qr" como un ID
    Route::get('/inventario/qr/{codigo}',                [InventarioApiController::class, 'buscarPorQr']);

    Route::get('/inventario',                            [InventarioApiController::class, 'index']);
    Route::get('/inventario/{id}',                       [InventarioApiController::class, 'show']);
    Route::post('/inventario',                           [InventarioApiController::class, 'store']);
    Route::put('/inventario/{id}',                       [InventarioApiController::class, 'update']);
    Route::patch('/inventario/{id}/status',              [InventarioApiController::class, 'cambiarStatus']);
    Route::delete('/inventario/{id}',                    [InventarioApiController::class, 'destroy']);
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\InventarioApiController;
use App\Http\Controllers\Api\CatalogoApiController;

// ════════════════════════════════════════════════════════
//  RUTAS PÚBLICAS
// ════════════════════════════════════════════════════════
Route::post('/login', [AuthApiController::class, 'login']);

// ════════════════════════════════════════════════════════
//  RUTAS PROTEGIDAS — Requieren Bearer Token (Sanctum)
// ════════════════════════════════════════════════════════
Route::middleware('auth:sanctum')->group(function () {

    // ── Sesión ────────────────────────────────────────────
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me',      [AuthApiController::class, 'me']);

    // ── Catálogos GET ─────────────────────────────────────
    Route::get('/catalogos/todos',              [CatalogoApiController::class, 'todos']);
    Route::get('/catalogos/sitios',             [CatalogoApiController::class, 'sitios']);
    Route::get('/catalogos/ubicaciones',        [CatalogoApiController::class, 'ubicaciones']);
    Route::get('/catalogos/dispositivos',       [CatalogoApiController::class, 'dispositivos']);
    Route::get('/catalogos/marcas',             [CatalogoApiController::class, 'marcas']);
    Route::get('/catalogos/modelos/{id_marca}', [CatalogoApiController::class, 'modelos']);
    Route::get('/catalogos/status',             [CatalogoApiController::class, 'status']);

    // ── Catálogos CRUD (solo admin) ───────────────────────
    Route::middleware('rol:admin')->group(function () {

        // Sitios
        Route::post('/catalogos/sitios',         [CatalogoApiController::class, 'sitiosStore']);
        Route::put('/catalogos/sitios/{sitio}',  [CatalogoApiController::class, 'sitiosUpdate']);

        // Ubicaciones
        Route::post('/catalogos/ubicaciones',              [CatalogoApiController::class, 'ubicacionesStore']);
        Route::put('/catalogos/ubicaciones/{ubicacion}',   [CatalogoApiController::class, 'ubicacionesUpdate']);

        // Dispositivos
        Route::post('/catalogos/dispositivos',               [CatalogoApiController::class, 'dispositivosStore']);
        Route::put('/catalogos/dispositivos/{dispositivo}',  [CatalogoApiController::class, 'dispositivosUpdate']);

        // Marcas
        Route::post('/catalogos/marcas',        [CatalogoApiController::class, 'marcasStore']);
        Route::put('/catalogos/marcas/{marca}', [CatalogoApiController::class, 'marcasUpdate']);

        // Modelos
        Route::post('/catalogos/modelos',          [CatalogoApiController::class, 'modelosStore']);
        Route::put('/catalogos/modelos/{modelo}',  [CatalogoApiController::class, 'modelosUpdate']);

        // Status
        Route::post('/catalogos/status',         [CatalogoApiController::class, 'statusStore']);
        Route::put('/catalogos/status/{status}', [CatalogoApiController::class, 'statusUpdate']);
    });

    // ── Inventario ────────────────────────────────────────
    // IMPORTANTE: /qr/{codigo} debe ir ANTES de /{id}
    Route::get('/inventario/qr/{codigo}',           [InventarioApiController::class, 'buscarPorQr']);
    Route::get('/inventario',                        [InventarioApiController::class, 'index']);
    Route::get('/inventario/{id}',                   [InventarioApiController::class, 'show']);
    Route::post('/inventario',                       [InventarioApiController::class, 'store']);
    Route::put('/inventario/{id}',                   [InventarioApiController::class, 'update']);
    Route::patch('/inventario/{id}/status',          [InventarioApiController::class, 'cambiarStatus']);
    Route::delete('/inventario/{id}',                [InventarioApiController::class, 'destroy']);
});

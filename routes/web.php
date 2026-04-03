<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ImportacionController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ReporteController;

// ══════════════════════════════════════════════════════════
//  RUTAS PÚBLICAS
// ══════════════════════════════════════════════════════════
Route::get('/',        [AuthController::class, 'showLogin']);
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ══════════════════════════════════════════════════════════
//  RUTAS PROTEGIDAS — requieren login
// ══════════════════════════════════════════════════════════
Route::middleware(['auth'])->group(function () {

    // ── Dashboard ────────────────────────────────────────
    Route::get('/dashboard', function () {
        $totalDispositivos = \App\Models\Inventario::count();

        $porStatus = DB::table('inventario as i')
            ->join('cat_status as s', 'i.id_status', '=', 's.id')
            ->whereNull('i.deleted_at')
            ->selectRaw('s.nombre as status, COUNT(*) as total')
            ->groupBy('s.nombre')
            ->orderByDesc('total')
            ->get();

        $porSitio = DB::table('inventario as i')
            ->join('cat_sitios as s', 'i.id_sitio', '=', 's.id')
            ->whereNull('i.deleted_at')
            ->selectRaw('s.clave, s.nombre as nombre_sitio, COUNT(*) as total')
            ->groupBy('s.id', 's.clave', 's.nombre')
            ->orderByDesc('total')
            ->get();

        $porDispositivo = DB::table('inventario as i')
            ->join('cat_dispositivos as d', 'i.id_dispositivo', '=', 'd.id')
            ->whereNull('i.deleted_at')
            ->selectRaw('d.tipo, COUNT(*) as total')
            ->groupBy('d.tipo')
            ->orderByDesc('total')
            ->get();

        $ultimosRegistros = \App\Models\Inventario::with([
            'sitio',
            'ubicacion',
            'dispositivo',
            'marca',
            'modelo',
            'status',
        ])->orderByDesc('created_at')->take(8)->get();

        return view('dashboard.index', compact(
            'totalDispositivos',
            'porStatus',
            'porSitio',
            'porDispositivo',
            'ultimosRegistros'
        ));
    })->name('dashboard');

    // ── Inventario ───────────────────────────────────────
    Route::get('/inventario',                    [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario/crear',              [InventarioController::class, 'create'])->name('inventario.create');
    Route::post('/inventario',                   [InventarioController::class, 'store'])->name('inventario.store');
    Route::get('/inventario/{inventario}',       [InventarioController::class, 'show'])->name('inventario.show');
    Route::get('/inventario/{inventario}/editar', [InventarioController::class, 'edit'])->name('inventario.edit');
    Route::put('/inventario/{inventario}',       [InventarioController::class, 'update'])->name('inventario.update');
    Route::delete('/inventario/{inventario}',    [InventarioController::class, 'destroy'])->name('inventario.destroy');
    Route::get('/inventario/{inventario}/qr',    [InventarioController::class, 'mostrarQr'])->name('inventario.qr');

    // ── Carga masiva (admin y usuario) ───────────────────
    Route::middleware('rol:admin,usuario')->group(function () {
        Route::get('/importar',           [ImportacionController::class, 'index'])->name('importar.index');
        Route::post('/importar',          [ImportacionController::class, 'store'])->name('importar.store');
        Route::get('/importar/plantilla', [ImportacionController::class, 'descargarPlantilla'])->name('importar.plantilla');
    });

    // ── Reportes ─────────────────────────────────────────
    Route::get('/reportes',          [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');

    // ── AJAX: selects dinámicos (sin middleware extra) ───
    Route::get('/catalogos/ubicaciones-por-sitio/{id}', [CatalogoController::class, 'ubicacionesPorSitio'])->name('catalogos.ubicaciones-por-sitio');
    Route::get('/catalogos/modelos-por-marca/{id}',     [CatalogoController::class, 'modelosPorMarca'])->name('catalogos.modelos-por-marca');

    // ── Catálogos (solo admin) ───────────────────────────
    Route::middleware('rol:admin')->group(function () {

        // Vista unificada de catálogos (con tabs por ?tab=)
        Route::get('/catalogos', [CatalogoController::class, 'index'])->name('catalogos.index');

        // Sitios
        Route::post('/catalogos/sitios',            [CatalogoController::class, 'sitiosStore'])->name('catalogos.sitios.store');
        Route::put('/catalogos/sitios/{sitio}',     [CatalogoController::class, 'sitiosUpdate'])->name('catalogos.sitios.update');
        Route::delete('/catalogos/sitios/{sitio}',  [CatalogoController::class, 'sitiosDestroy'])->name('catalogos.sitios.destroy');

        // Ubicaciones
        Route::post('/catalogos/ubicaciones',              [CatalogoController::class, 'ubicacionesStore'])->name('catalogos.ubicaciones.store');
        Route::put('/catalogos/ubicaciones/{ubicacion}',   [CatalogoController::class, 'ubicacionesUpdate'])->name('catalogos.ubicaciones.update');
        Route::delete('/catalogos/ubicaciones/{ubicacion}', [CatalogoController::class, 'ubicacionesDestroy'])->name('catalogos.ubicaciones.destroy');

        // Dispositivos
        Route::post('/catalogos/dispositivos',               [CatalogoController::class, 'dispositivosStore'])->name('catalogos.dispositivos.store');
        Route::put('/catalogos/dispositivos/{dispositivo}',  [CatalogoController::class, 'dispositivosUpdate'])->name('catalogos.dispositivos.update');

        // Marcas
        Route::post('/catalogos/marcas',         [CatalogoController::class, 'marcasStore'])->name('catalogos.marcas.store');
        Route::put('/catalogos/marcas/{marca}',  [CatalogoController::class, 'marcasUpdate'])->name('catalogos.marcas.update');

        // Modelos
        Route::post('/catalogos/modelos',          [CatalogoController::class, 'modelosStore'])->name('catalogos.modelos.store');
        Route::put('/catalogos/modelos/{modelo}',  [CatalogoController::class, 'modelosUpdate'])->name('catalogos.modelos.update');

        // Status
        Route::post('/catalogos/status',         [CatalogoController::class, 'statusStore'])->name('catalogos.status.store');
        Route::put('/catalogos/status/{status}', [CatalogoController::class, 'statusUpdate'])->name('catalogos.status.update');

        // Usuarios
        Route::get('/usuarios',                      [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios',                     [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{usuario}',            [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::patch('/usuarios/{usuario}/reset-password', [UsuarioController::class, 'resetPassword'])->name('usuarios.reset-password');
        Route::delete('/usuarios/{usuario}',         [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });
});

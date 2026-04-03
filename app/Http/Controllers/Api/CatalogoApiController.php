<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CatSitio;
use App\Models\CatUbicacion;
use App\Models\CatDispositivo;
use App\Models\CatMarca;
use App\Models\CatModelo;
use App\Models\CatStatus;
use Illuminate\Http\JsonResponse;

class CatalogoApiController extends Controller
{
    // ── GET /api/catalogos/sitios ────────────────────────
    public function sitios(): JsonResponse
    {
        $sitios = CatSitio::activos()->get(['id', 'clave', 'nombre']);

        return response()->json($sitios);
    }

    // ── GET /api/catalogos/ubicaciones?id_sitio=1 ────────
    // Si se pasa id_sitio filtra por sitio, si no devuelve todas
    public function ubicaciones(): JsonResponse
    {
        $idSitio = request('id_sitio');

        $ubicaciones = CatUbicacion::activos()
            ->when($idSitio, fn($q) => $q->porSitio((int) $idSitio))
            ->with('sitio:id,clave')
            ->get(['id', 'id_sitio', 'nombre']);

        return response()->json($ubicaciones);
    }

    // ── GET /api/catalogos/dispositivos ──────────────────
    public function dispositivos(): JsonResponse
    {
        $dispositivos = CatDispositivo::activos()->get(['id', 'tipo', 'descripcion']);

        return response()->json($dispositivos);
    }

    // ── GET /api/catalogos/marcas ─────────────────────────
    public function marcas(): JsonResponse
    {
        $marcas = CatMarca::activos()->get(['id', 'nombre']);

        return response()->json($marcas);
    }

    // ── GET /api/catalogos/modelos/{id_marca} ─────────────
    // La app llama esto cuando el usuario selecciona una marca
    public function modelos(int $idMarca): JsonResponse
    {
        $modelos = CatModelo::activos()
            ->porMarca($idMarca)
            ->get(['id', 'id_marca', 'numero_modelo']);

        return response()->json($modelos);
    }

    // ── GET /api/catalogos/status ─────────────────────────
    public function status(): JsonResponse
    {
        $statuses = CatStatus::activos()->get(['id', 'nombre', 'descripcion']);

        return response()->json($statuses);
    }

    // ── GET /api/catalogos/todos ──────────────────────────
    // Un solo endpoint que devuelve TODOS los catálogos de golpe.
    // Útil para que la app los cachee al iniciar sesión.
    public function todos(): JsonResponse
    {
        return response()->json([
            'sitios'       => CatSitio::activos()->get(['id', 'clave', 'nombre']),
            'ubicaciones'  => CatUbicacion::activos()->get(['id', 'id_sitio', 'nombre']),
            'dispositivos' => CatDispositivo::activos()->get(['id', 'tipo', 'descripcion']),
            'marcas'       => CatMarca::activos()->get(['id', 'nombre']),
            'modelos'      => CatModelo::activos()->get(['id', 'id_marca', 'numero_modelo']),
            'statuses'     => CatStatus::activos()->get(['id', 'nombre', 'descripcion']),
        ]);
    }
}

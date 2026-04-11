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
use Illuminate\Http\Request;

class CatalogoApiController extends Controller
{
    // ════════════════════════════════════════════════════
    //  GET — Todos los catálogos de golpe
    // ════════════════════════════════════════════════════
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

    // ════════════════════════════════════════════════════
    //  SITIOS
    // ════════════════════════════════════════════════════
    public function sitios(): JsonResponse
    {
        return response()->json(CatSitio::activos()->get(['id', 'clave', 'nombre']));
    }

    public function sitiosStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'clave'  => ['required', 'string', 'max:3', 'unique:cat_sitios,clave'],
            'nombre' => ['required', 'string', 'max:80'],
        ], [
            'clave.unique' => 'Ya existe un sitio con esa clave.',
        ]);

        $sitio = CatSitio::create($data);

        // Crear las 5 ubicaciones estándar automáticamente
        foreach (['CHECK-IN', 'GATE', 'COREROOM', 'KIOSCO', 'BODEGA'] as $ub) {
            CatUbicacion::create(['id_sitio' => $sitio->id, 'nombre' => $ub]);
        }

        return response()->json($sitio, 201);
    }

    public function sitiosUpdate(Request $request, CatSitio $sitio): JsonResponse
    {
        $data = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $sitio->update($data);

        return response()->json($sitio);
    }

    // ════════════════════════════════════════════════════
    //  UBICACIONES
    // ════════════════════════════════════════════════════
    public function ubicaciones(): JsonResponse
    {
        $idSitio = request('id_sitio');

        $ubicaciones = CatUbicacion::activos()
            ->when($idSitio, fn($q) => $q->porSitio((int) $idSitio))
            ->get(['id', 'id_sitio', 'nombre']);

        return response()->json($ubicaciones);
    }

    public function ubicacionesStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_sitio' => ['required', 'exists:cat_sitios,id'],
            'nombre'   => ['required', 'string', 'max:60'],
        ]);

        $ubicacion = CatUbicacion::create($data);

        return response()->json([
            'id'       => $ubicacion->id,
            'id_sitio' => $ubicacion->id_sitio,
            'nombre'   => $ubicacion->nombre,
        ], 201);
    }

    public function ubicacionesUpdate(Request $request, CatUbicacion $ubicacion): JsonResponse
    {
        $data = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $ubicacion->update($data);

        return response()->json([
            'id'       => $ubicacion->id,
            'id_sitio' => $ubicacion->id_sitio,
            'nombre'   => $ubicacion->nombre,
        ]);
    }

    // ════════════════════════════════════════════════════
    //  DISPOSITIVOS
    // ════════════════════════════════════════════════════
    public function dispositivos(): JsonResponse
    {
        return response()->json(CatDispositivo::activos()->get(['id', 'tipo', 'descripcion']));
    }

    public function dispositivosStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tipo'        => ['required', 'string', 'max:40', 'unique:cat_dispositivos,tipo'],
            'descripcion' => ['nullable', 'string', 'max:120'],
        ], [
            'tipo.unique' => 'Ya existe un dispositivo con ese tipo.',
        ]);

        $dispositivo = CatDispositivo::create($data);

        return response()->json($dispositivo, 201);
    }

    public function dispositivosUpdate(Request $request, CatDispositivo $dispositivo): JsonResponse
    {
        $data = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $dispositivo->update($data);

        return response()->json($dispositivo);
    }

    // ════════════════════════════════════════════════════
    //  MARCAS
    // ════════════════════════════════════════════════════
    public function marcas(): JsonResponse
    {
        return response()->json(CatMarca::activos()->get(['id', 'nombre']));
    }

    public function marcasStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:cat_marcas,nombre'],
        ], [
            'nombre.unique' => 'Ya existe una marca con ese nombre.',
        ]);

        $marca = CatMarca::create($data);

        return response()->json($marca, 201);
    }

    public function marcasUpdate(Request $request, CatMarca $marca): JsonResponse
    {
        $data = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $marca->update($data);

        return response()->json($marca);
    }

    // ════════════════════════════════════════════════════
    //  MODELOS
    // ════════════════════════════════════════════════════
    public function modelos(int $idMarca): JsonResponse
    {
        $modelos = CatModelo::activos()
            ->porMarca($idMarca)
            ->get(['id', 'id_marca', 'numero_modelo']);

        return response()->json($modelos);
    }

    public function modelosStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_marca'      => ['required', 'exists:cat_marcas,id'],
            'numero_modelo' => ['required', 'string', 'max:80'],
        ]);

        $modelo = CatModelo::create($data);

        return response()->json([
            'id'            => $modelo->id,
            'id_marca'      => $modelo->id_marca,
            'numero_modelo' => $modelo->numero_modelo,
        ], 201);
    }

    public function modelosUpdate(Request $request, CatModelo $modelo): JsonResponse
    {
        $data = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $modelo->update($data);

        return response()->json([
            'id'            => $modelo->id,
            'id_marca'      => $modelo->id_marca,
            'numero_modelo' => $modelo->numero_modelo,
        ]);
    }

    // ════════════════════════════════════════════════════
    //  STATUS
    // ════════════════════════════════════════════════════
    public function status(): JsonResponse
    {
        return response()->json(CatStatus::activos()->get(['id', 'nombre', 'descripcion']));
    }

    public function statusStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:30', 'unique:cat_status,nombre'],
            'descripcion' => ['nullable', 'string', 'max:120'],
        ], [
            'nombre.unique' => 'Ya existe un status con ese nombre.',
        ]);

        $status = CatStatus::create($data);

        return response()->json($status, 201);
    }

    public function statusUpdate(Request $request, CatStatus $status): JsonResponse
    {
        $data = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $status->update($data);

        return response()->json($status);
    }
}

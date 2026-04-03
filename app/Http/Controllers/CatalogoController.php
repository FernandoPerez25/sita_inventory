<?php

namespace App\Http\Controllers;

use App\Models\CatSitio;
use App\Models\CatUbicacion;
use App\Models\CatDispositivo;
use App\Models\CatMarca;
use App\Models\CatModelo;
use App\Models\CatStatus;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    // ── Vista unificada con tabs ──────────────────────────
    // Recibe ?tab=sitios|ubicaciones|dispositivos|marcas|modelos|status
    public function index()
    {
        $tab = request('tab', 'sitios');

        return view('catalogos.index', [
            'activeTab'   => $tab,
            'sitios'      => CatSitio::with('ubicaciones')->orderBy('clave')->get(),
            'ubicaciones' => CatUbicacion::with('sitio')->orderBy('id_sitio')->get(),
            'dispositivos' => CatDispositivo::orderBy('tipo')->get(),
            'marcas'      => CatMarca::withCount('modelos')->orderBy('nombre')->get(),
            'modelos'     => CatModelo::with('marca')->orderBy('numero_modelo')->get(),
            'statuses'    => CatStatus::orderBy('nombre')->get(),
        ]);
    }

    // ════════════════════════════════════════════════════
    //  SITIOS
    // ════════════════════════════════════════════════════
    public function sitiosStore(Request $request)
    {
        $data = $request->validate([
            'clave'  => ['required', 'string', 'max:3', 'unique:cat_sitios,clave'],
            'nombre' => ['required', 'string', 'max:80'],
        ]);

        $sitio = CatSitio::create($data);

        foreach (['CHECK-IN', 'GATE', 'COREROOM', 'KIOSCO', 'BODEGA'] as $ub) {
            CatUbicacion::create(['id_sitio' => $sitio->id, 'nombre' => $ub]);
        }

        return redirect()->route('catalogos.index', ['tab' => 'sitios'])
            ->with('success', "Sitio {$sitio->clave} creado con ubicaciones por defecto.");
    }

    public function sitiosUpdate(Request $request, CatSitio $sitio)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $sitio->update($data);
        return redirect()->route('catalogos.index', ['tab' => 'sitios'])
            ->with('success', 'Sitio actualizado.');
    }

    public function sitiosDestroy(CatSitio $sitio)
    {
        if ($sitio->inventarios()->exists()) {
            return redirect()->route('catalogos.index', ['tab' => 'sitios'])
                ->with('error', 'No se puede eliminar: tiene dispositivos asignados.');
        }
        $sitio->delete();
        return redirect()->route('catalogos.index', ['tab' => 'sitios'])
            ->with('success', 'Sitio eliminado.');
    }

    // ════════════════════════════════════════════════════
    //  UBICACIONES
    // ════════════════════════════════════════════════════
    public function ubicacionesStore(Request $request)
    {
        $request->validate([
            'id_sitio' => ['required', 'exists:cat_sitios,id'],
            'nombre'   => ['required', 'string', 'max:60'],
        ]);
        CatUbicacion::create($request->only('id_sitio', 'nombre'));
        return redirect()->route('catalogos.index', ['tab' => 'ubicaciones'])
            ->with('success', 'Ubicación creada.');
    }

    public function ubicacionesUpdate(Request $request, CatUbicacion $ubicacion)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:60'],
            'activo' => ['nullable', 'boolean'],
        ]);
        $ubicacion->update($data);
        return redirect()->route('catalogos.index', ['tab' => 'ubicaciones'])
            ->with('success', 'Ubicación actualizada.');
    }

    public function ubicacionesDestroy(CatUbicacion $ubicacion)
    {
        if ($ubicacion->inventarios()->exists()) {
            return redirect()->route('catalogos.index', ['tab' => 'ubicaciones'])
                ->with('error', 'No se puede eliminar: tiene dispositivos asignados.');
        }
        $ubicacion->delete();
        return redirect()->route('catalogos.index', ['tab' => 'ubicaciones'])
            ->with('success', 'Ubicación eliminada.');
    }

    // ════════════════════════════════════════════════════
    //  DISPOSITIVOS
    // ════════════════════════════════════════════════════
    public function dispositivosStore(Request $request)
    {
        $request->validate([
            'tipo'        => ['required', 'string', 'max:40', 'unique:cat_dispositivos,tipo'],
            'descripcion' => ['nullable', 'string', 'max:120'],
        ]);
        CatDispositivo::create($request->only('tipo', 'descripcion'));
        return redirect()->route('catalogos.index', ['tab' => 'dispositivos'])
            ->with('success', 'Tipo de dispositivo creado.');
    }

    public function dispositivosUpdate(Request $request, CatDispositivo $dispositivo)
    {
        $data = $request->validate([
            'descripcion' => ['nullable', 'string', 'max:120'],
            'activo'      => ['nullable', 'boolean'],
        ]);
        $dispositivo->update($data);
        return redirect()->route('catalogos.index', ['tab' => 'dispositivos'])
            ->with('success', 'Dispositivo actualizado.');
    }

    // ════════════════════════════════════════════════════
    //  MARCAS
    // ════════════════════════════════════════════════════
    public function marcasStore(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:50', 'unique:cat_marcas,nombre'],
        ]);
        CatMarca::create($request->only('nombre'));
        return redirect()->route('catalogos.index', ['tab' => 'marcas'])
            ->with('success', 'Marca creada.');
    }

    public function marcasUpdate(Request $request, CatMarca $marca)
    {
        $data = $request->validate([
            'activo' => ['nullable', 'boolean'],
        ]);
        $marca->update($data);
        return redirect()->route('catalogos.index', ['tab' => 'marcas'])
            ->with('success', 'Marca actualizada.');
    }

    // ════════════════════════════════════════════════════
    //  MODELOS
    // ════════════════════════════════════════════════════
    public function modelosStore(Request $request)
    {
        $request->validate([
            'id_marca'      => ['required', 'exists:cat_marcas,id'],
            'numero_modelo' => ['required', 'string', 'max:80'],
        ]);
        CatModelo::create($request->only('id_marca', 'numero_modelo'));
        return redirect()->route('catalogos.index', ['tab' => 'modelos'])
            ->with('success', 'Modelo creado.');
    }

    public function modelosUpdate(Request $request, CatModelo $modelo)
    {
        $data = $request->validate([
            'activo' => ['nullable', 'boolean'],
        ]);
        $modelo->update($data);
        return redirect()->route('catalogos.index', ['tab' => 'modelos'])
            ->with('success', 'Modelo actualizado.');
    }

    // ════════════════════════════════════════════════════
    //  STATUS
    // ════════════════════════════════════════════════════
    public function statusStore(Request $request)
    {
        $request->validate([
            'nombre'      => ['required', 'string', 'max:30', 'unique:cat_status,nombre'],
            'descripcion' => ['nullable', 'string', 'max:120'],
        ]);
        CatStatus::create($request->only('nombre', 'descripcion'));
        return redirect()->route('catalogos.index', ['tab' => 'status'])
            ->with('success', 'Status creado.');
    }

    public function statusUpdate(Request $request, CatStatus $status)
    {
        $data = $request->validate([
            'descripcion' => ['nullable', 'string', 'max:120'],
            'activo'      => ['nullable', 'boolean'],
        ]);
        $status->update($data);
        return redirect()->route('catalogos.index', ['tab' => 'status'])
            ->with('success', 'Status actualizado.');
    }

    // ════════════════════════════════════════════════════
    //  AJAX — selects dinámicos
    // ════════════════════════════════════════════════════
    public function modelosPorMarca(int $id)
    {
        $modelos = CatModelo::where('id_marca', $id)
            ->where('activo', 1)
            ->orderBy('numero_modelo')
            ->get(['id', 'numero_modelo']);

        return response()->json($modelos);
    }

    public function ubicacionesPorSitio(int $id)
    {
        $ubicaciones = CatUbicacion::where('id_sitio', $id)
            ->where('activo', 1)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($ubicaciones);
    }
}

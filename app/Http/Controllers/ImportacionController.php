<?php

namespace App\Http\Controllers;

use App\Models\Importacion;
use App\Services\ImportacionService;
use App\Models\CatSitio;
use App\Models\CatUbicacion;
use App\Models\CatDispositivo;
use App\Models\CatMarca;
use App\Models\CatModelo;
use App\Models\CatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportacionController extends Controller
{
    public function __construct(private ImportacionService $importacionService) {}

    public function index()
    {
        $importaciones = Importacion::with('usuario')
            ->orderByDesc('created_at')
            ->paginate(10);

        $sitios       = CatSitio::activos()->get(['id', 'clave', 'nombre']);
        $ubicaciones  = CatUbicacion::activos()->with('sitio:id,clave')->get(['id', 'id_sitio', 'nombre']);
        $dispositivos = CatDispositivo::activos()->get(['id', 'tipo']);
        $marcas       = CatMarca::activos()->get(['id', 'nombre']);
        $modelos      = CatModelo::activos()->get(['id', 'id_marca', 'numero_modelo']);
        $statuses     = CatStatus::activos()->get(['id', 'nombre']);

        return view('inventario.importar', compact(
            'importaciones',
            'sitios',
            'ubicaciones',
            'dispositivos',
            'marcas',
            'modelos',
            'statuses'
        ));
    }

    public function store(Request $request)
    {
        $metodo = $request->input('metodo', 'excel');

        if ($metodo === 'formulario') {
            $request->validate([
                'filas'                  => ['required', 'array', 'min:1'],
                'filas.*.id_sitio'       => ['required', 'exists:cat_sitios,id'],
                'filas.*.id_ubicacion'   => ['required', 'exists:cat_ubicaciones,id'],
                'filas.*.id_dispositivo' => ['required', 'exists:cat_dispositivos,id'],
                'filas.*.id_marca'       => ['required', 'exists:cat_marcas,id'],
                'filas.*.id_modelo'      => ['required', 'exists:cat_modelos,id'],
                'filas.*.id_status'      => ['required', 'exists:cat_status,id'],
                'filas.*.serial_number'  => ['required', 'string'],
            ]);

            $resultado = $this->importacionService->procesarFormulario(
                $request->input('filas'),
                Auth::user()
            );
        } else {
            $request->validate([
                'archivo' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            ]);

            $resultado = $this->importacionService->procesarArchivo(
                $request->file('archivo'),
                Auth::user()
            );
        }

        if ($resultado['fallidos'] > 0 && $resultado['exitosos'] === 0) {
            return back()->with([
                'error'   => "Importación fallida: {$resultado['fallidos']} errores.",
                'errores' => $resultado['errores'],
            ]);
        }

        if ($resultado['fallidos'] > 0) {
            return back()->with([
                'warning' => "Importación parcial: {$resultado['exitosos']} guardados, {$resultado['fallidos']} con error.",
                'errores' => $resultado['errores'],
            ]);
        }

        return back()->with('success', "✓ {$resultado['exitosos']} dispositivos importados correctamente.");
    }

    public function descargarPlantilla()
    {
        $ruta = storage_path('app/plantillas/plantilla_inventario_sita.xlsx');

        if (!file_exists($ruta)) {
            return back()->with('error', 'La plantilla no está disponible aún.');
        }

        return response()->download($ruta, 'Plantilla_Inventario_SITA.xlsx');
    }
}

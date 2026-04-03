<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\CatSitio;
use App\Models\CatStatus;
use App\Models\CatDispositivo;
use App\Exports\InventarioExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function index()
    {
        $totalDispositivos = Inventario::count();

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

        // Catálogos para los selects del formulario de exportar
        $sitios       = CatSitio::activos()->get();
        $statuses     = CatStatus::activos()->get();
        $dispositivos = CatDispositivo::activos()->get();

        return view('reportes.index', compact(
            'totalDispositivos',
            'porStatus',
            'porSitio',
            'porDispositivo',
            'sitios',
            'statuses',
            'dispositivos'
        ));
    }

    public function exportar(Request $request)
    {
        $filtros = $request->only(['sitio', 'status', 'dispositivo', 'q']);
        $nombre  = 'SITA_Inventario_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new InventarioExport($filtros), $nombre);
    }
}

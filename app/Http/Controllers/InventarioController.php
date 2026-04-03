<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\HistorialInventario;
use App\Models\CatSitio;
use App\Models\CatUbicacion;
use App\Models\CatDispositivo;
use App\Models\CatMarca;
use App\Models\CatModelo;
use App\Models\CatStatus;
use App\Services\QrService;
use App\Http\Requests\StoreInventarioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function __construct(private QrService $qrService) {}

    // ── INDEX — lista con filtros y búsqueda ─────────────
    public function index(Request $request)
    {
        $query = Inventario::with([
            'sitio',
            'ubicacion',
            'dispositivo',
            'marca',
            'modelo',
            'status',
        ]);

        // Filtros
        $query->porSitio($request->sitio)
            ->porStatus($request->status)
            ->porDispositivo($request->dispositivo)
            ->buscar($request->q);

        $inventario = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        // Catálogos para los selects de filtro
        $sitios      = CatSitio::activos()->get();
        $statuses    = CatStatus::activos()->get();
        $dispositivos = CatDispositivo::activos()->get();

        return view('inventario.index', compact('inventario', 'sitios', 'statuses', 'dispositivos'));
    }

    // ── SHOW — ficha de detalle + QR ─────────────────────
    public function show(Inventario $inventario)
    {
        $inventario->load([
            'sitio',
            'ubicacion',
            'dispositivo',
            'marca',
            'modelo',
            'status',
            'usuarioRegistro',
            'historial.usuario',
        ]);

        // Generar QR si no existe todavía
        if (!$inventario->qr_code) {
            $this->qrService->generarYGuardar($inventario);
            $inventario->refresh();
        }

        // Pasar el SVG directamente como variable a la vista
        $imagenQr = $this->qrService->generarImagen($inventario->qr_code);

        return view('inventario.show', compact('inventario', 'imagenQr'));
    }

    // ── CREATE — formulario nuevo equipo ─────────────────
    public function create()
    {
        $datos = $this->datosCatalogos();
        return view('inventario.create', $datos);
    }

    // ── STORE — guardar nuevo equipo ─────────────────────
    public function store(StoreInventarioRequest $request)
    {
        DB::beginTransaction();
        try {
            $item = Inventario::create([
                ...$request->validated(),
                'id_usuario_reg' => Auth::id(),
            ]);

            // Generar QR automáticamente al crear
            $this->qrService->generarYGuardar($item);

            DB::commit();

            return redirect()
                ->route('inventario.show', $item)
                ->with('success', "Dispositivo {$item->serial_number} registrado correctamente.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    // ── EDIT — formulario editar ─────────────────────────
    public function edit(Inventario $inventario)
    {
        $datos = $this->datosCatalogos();
        return view('inventario.edit', array_merge($datos, compact('inventario')));
    }

    // ── UPDATE — guardar cambios con auditoría ────────────
    public function update(StoreInventarioRequest $request, Inventario $inventario)
    {
        DB::beginTransaction();
        try {
            $campos         = $request->validated();
            $historialBatch = [];

            // Detectar campos que cambiaron para auditoría
            foreach ($campos as $campo => $valorNuevo) {
                $valorAnterior = $inventario->getAttribute($campo);
                if ((string) $valorAnterior !== (string) $valorNuevo) {
                    $historialBatch[] = [
                        'id_inventario'    => $inventario->id,
                        'id_usuario'       => Auth::id(),
                        'campo_modificado' => $campo,
                        'valor_anterior'   => $valorAnterior,
                        'valor_nuevo'      => $valorNuevo,
                        'ip_origen'        => $request->ip(),
                        'origen'           => 'web',
                        'created_at'       => now(),
                    ];
                }
            }

            $inventario->update([
                ...$campos,
                'id_usuario_mod' => Auth::id(),
            ]);

            if (!empty($historialBatch)) {
                HistorialInventario::insert($historialBatch);
            }

            DB::commit();

            return redirect()
                ->route('inventario.show', $inventario)
                ->with('success', 'Dispositivo actualizado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // ── DESTROY — soft delete ─────────────────────────────
    public function destroy(Inventario $inventario)
    {
        $inventario->delete();
        return redirect()
            ->route('inventario.index')
            ->with('success', "Dispositivo {$inventario->serial_number} eliminado.");
    }

    // ── QR — mostrar imagen del QR en pantalla ───────────
    public function mostrarQr(Inventario $inventario)
    {
        if (!$inventario->qr_code) {
            $this->qrService->generarYGuardar($inventario);
            $inventario->refresh();
        }

        $svg = $this->qrService->generarImagen($inventario->qr_code);

        return response($svg, 200, [
            'Content-Type'        => 'image/svg+xml',
            'Content-Disposition' => 'inline; filename="QR-' . $inventario->serial_number . '.svg"',
        ]);
    }

    // ── HELPER: catálogos para los formularios ───────────
    private function datosCatalogos(): array
    {
        return [
            'sitios'       => CatSitio::activos()->get(),
            'ubicaciones'  => CatUbicacion::activos()->get(),
            'dispositivos' => CatDispositivo::activos()->get(),
            'marcas'       => CatMarca::activos()->get(),
            'modelos'      => CatModelo::activos()->get(),
            'statuses'     => CatStatus::activos()->get(),
        ];
    }
}

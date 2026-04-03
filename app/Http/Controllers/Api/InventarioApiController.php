<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\HistorialInventario;
use App\Services\QrService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventarioApiController extends Controller
{
    public function __construct(private QrService $qrService) {}

    // ── GET /api/inventario ───────────────────────────────
    // Lista con filtros y paginación
    public function index(Request $request): JsonResponse
    {
        $inventario = Inventario::with([
            'sitio:id,clave,nombre',
            'ubicacion:id,nombre',
            'dispositivo:id,tipo',
            'marca:id,nombre',
            'modelo:id,numero_modelo',
            'status:id,nombre',
        ])
            ->porSitio($request->id_sitio)
            ->porStatus($request->id_status)
            ->porDispositivo($request->id_dispositivo)
            ->buscar($request->q)
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json($inventario);
    }

    // ── GET /api/inventario/{id} ──────────────────────────
    // Detalle completo de un dispositivo
    public function show(int $id): JsonResponse
    {
        $item = Inventario::with([
            'sitio:id,clave,nombre',
            'ubicacion:id,nombre',
            'dispositivo:id,tipo',
            'marca:id,nombre',
            'modelo:id,numero_modelo',
            'status:id,nombre',
            'usuarioRegistro:id,nombre,apellidos',
        ])->findOrFail($id);

        return response()->json($item);
    }

    // ── GET /api/inventario/qr/{codigo} ──────────────────
    // Buscar dispositivo por QR escaneado desde la app
    public function buscarPorQr(string $codigo): JsonResponse
    {
        $item = Inventario::with([
            'sitio:id,clave,nombre',
            'ubicacion:id,nombre',
            'dispositivo:id,tipo',
            'marca:id,nombre',
            'modelo:id,numero_modelo',
            'status:id,nombre',
            'usuarioRegistro:id,nombre,apellidos',
        ])->where('qr_code', $codigo)->first();

        if (!$item) {
            return response()->json([
                'message' => 'Dispositivo no encontrado. El código QR no está registrado.',
            ], 404);
        }

        return response()->json($item);
    }

    // ── POST /api/inventario ──────────────────────────────
    // Registrar nuevo dispositivo desde la app
    public function store(Request $request): JsonResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = $request->user();

        if (!$usuario->puedeEditar()) {
            return response()->json(['message' => 'No tienes permiso para registrar dispositivos.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id_sitio'       => ['required', 'exists:cat_sitios,id'],
            'id_ubicacion'   => ['required', 'exists:cat_ubicaciones,id'],
            'id_dispositivo' => ['required', 'exists:cat_dispositivos,id'],
            'id_marca'       => ['required', 'exists:cat_marcas,id'],
            'id_modelo'      => ['required', 'exists:cat_modelos,id'],
            'id_status'      => ['required', 'exists:cat_status,id'],
            'serial_number'  => ['required', 'string', 'max:60', 'unique:inventario,serial_number'],
            'sita_asset_tag' => ['nullable', 'string', 'max:40', 'unique:inventario,sita_asset_tag'],
            'po_number'      => ['nullable', 'string', 'max:40'],
            'gap_active'     => ['nullable', 'string', 'max:40'],
            'nodename'       => ['nullable', 'string', 'max:60'],
            'comentarios'    => ['nullable', 'string', 'max:500'],
        ], [
            'serial_number.unique'   => 'Este número de serie ya está registrado.',
            'sita_asset_tag.unique'  => 'Este SITA Asset Tag ya está registrado.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $item = Inventario::create([
                ...$validator->validated(),
                'id_usuario_reg' => $usuario->id,
            ]);

            // Generar QR automáticamente
            $this->qrService->generarYGuardar($item);

            DB::commit();

            return response()->json([
                'message'    => 'Dispositivo registrado correctamente.',
                'inventario' => $item->load([
                    'sitio:id,clave,nombre',
                    'ubicacion:id,nombre',
                    'dispositivo:id,tipo',
                    'marca:id,nombre',
                    'modelo:id,numero_modelo',
                    'status:id,nombre',
                ]),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al guardar el dispositivo.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ── PUT /api/inventario/{id} ──────────────────────────
    // Editar dispositivo completo
    public function update(Request $request, int $id): JsonResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = $request->user();

        if (!$usuario->puedeEditar()) {
            return response()->json(['message' => 'No tienes permiso para editar dispositivos.'], 403);
        }

        $item = Inventario::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'id_sitio'       => ['sometimes', 'exists:cat_sitios,id'],
            'id_ubicacion'   => ['sometimes', 'exists:cat_ubicaciones,id'],
            'id_dispositivo' => ['sometimes', 'exists:cat_dispositivos,id'],
            'id_marca'       => ['sometimes', 'exists:cat_marcas,id'],
            'id_modelo'      => ['sometimes', 'exists:cat_modelos,id'],
            'id_status'      => ['sometimes', 'exists:cat_status,id'],
            'serial_number'  => ['sometimes', 'string', 'max:60', "unique:inventario,serial_number,{$id}"],
            'sita_asset_tag' => ['nullable', 'string', 'max:40', "unique:inventario,sita_asset_tag,{$id}"],
            'po_number'      => ['nullable', 'string', 'max:40'],
            'gap_active'     => ['nullable', 'string', 'max:40'],
            'nodename'       => ['nullable', 'string', 'max:60'],
            'comentarios'    => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $campos         = $validator->validated();
            $historialBatch = [];

            foreach ($campos as $campo => $valorNuevo) {
                $valorAnterior = $item->getAttribute($campo);
                if ((string) $valorAnterior !== (string) $valorNuevo) {
                    $historialBatch[] = [
                        'id_inventario'    => $item->id,
                        'id_usuario'       => $usuario->id,
                        'campo_modificado' => $campo,
                        'valor_anterior'   => $valorAnterior,
                        'valor_nuevo'      => $valorNuevo,
                        'ip_origen'        => $request->ip(),
                        'origen'           => 'movil',
                        'created_at'       => now(),
                    ];
                }
            }

            $item->update([...$campos, 'id_usuario_mod' => $usuario->id]);

            if (!empty($historialBatch)) {
                HistorialInventario::insert($historialBatch);
            }

            DB::commit();

            return response()->json([
                'message'    => 'Dispositivo actualizado.',
                'inventario' => $item->fresh([
                    'sitio:id,clave,nombre',
                    'ubicacion:id,nombre',
                    'dispositivo:id,tipo',
                    'marca:id,nombre',
                    'modelo:id,numero_modelo',
                    'status:id,nombre',
                ]),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar.', 'error' => $e->getMessage()], 500);
        }
    }

    // ── PATCH /api/inventario/{id}/status ─────────────────
    // Cambio RÁPIDO de status desde campo (lo más usado en la app)
    public function cambiarStatus(Request $request, int $id): JsonResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = $request->user();

        if (!$usuario->puedeEditar()) {
            return response()->json(['message' => 'No tienes permiso para cambiar el status.'], 403);
        }

        $validated = $request->validate([
            'id_status'   => ['required', 'exists:cat_status,id'],
            'comentarios' => ['nullable', 'string', 'max:500'],
        ]);

        $item = Inventario::findOrFail($id);
        $statusAnterior = $item->id_status;

        $item->update([
            'id_status'      => $validated['id_status'],
            'comentarios'    => $validated['comentarios'] ?? $item->comentarios,
            'id_usuario_mod' => $usuario->id,
        ]);

        // Registrar en historial
        HistorialInventario::create([
            'id_inventario'    => $item->id,
            'id_usuario'       => $usuario->id,
            'campo_modificado' => 'id_status',
            'valor_anterior'   => (string) $statusAnterior,
            'valor_nuevo'      => (string) $validated['id_status'],
            'ip_origen'        => $request->ip(),
            'origen'           => 'movil',
        ]);

        return response()->json([
            'message' => 'Status actualizado correctamente.',
            'status'  => $item->status()->first(['id', 'nombre']),
        ]);
    }

    // ── DELETE /api/inventario/{id} ───────────────────────
    // Solo admin puede eliminar (soft delete)
    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = $request->user();

        if (!$usuario->esAdmin()) {
            return response()->json(['message' => 'Solo el administrador puede eliminar dispositivos.'], 403);
        }

        $item = Inventario::findOrFail($id);
        $item->delete();

        return response()->json(['message' => "Dispositivo {$item->serial_number} eliminado."]);
    }
}

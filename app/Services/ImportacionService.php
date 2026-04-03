<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\Importacion;
use App\Models\CatSitio;
use App\Models\CatUbicacion;
use App\Models\CatDispositivo;
use App\Models\CatMarca;
use App\Models\CatModelo;
use App\Models\CatStatus;
use App\Models\Usuario;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportacionService
{
    // Mapeo de columnas del Excel original de SITA
    // Columna => índice (0-based)
    private array $columnas = [
        'site'          => 0,   // A - AGU, GDL...
        'location'      => 1,   // B - CHECK-IN, GATE...
        'dispositivo'   => 2,   // C - ATB, BTP...
        'marca'         => 3,   // D - FUJITSU, HP...
        'modelo'        => 4,   // E - F9860, P22V G4...
        'serial'        => 5,   // F - número de serie
        'asset_tag'     => 6,   // G - SITA Asset Tag
        'po'            => 7,   // H - PO #
        'gap'           => 8,   // I - GAP ACTIVE
        'nodename'      => 9,   // J - NODENAME
        'status'        => 10,  // K - STATUS ACTUAL
        'comentarios'   => 11,  // L - Comentarios
    ];

    // ── Procesar archivo Excel o CSV ─────────────────────
    public function procesarArchivo(UploadedFile $archivo, Usuario $usuario): array
    {
        $exitosos = 0;
        $fallidos = 0;
        $errores  = [];

        try {
            $spreadsheet = IOFactory::load($archivo->getRealPath());
            $hoja        = $spreadsheet->getActiveSheet();
            $filas       = $hoja->toArray();

            // Saltar la fila de encabezados (fila 0)
            foreach ($filas as $numFila => $fila) {
                if ($numFila === 0) continue; // encabezado
                if ($this->filaVacia($fila)) continue;

                $resultado = $this->procesarFila($fila, $numFila + 1, $usuario);

                if ($resultado['ok']) {
                    $exitosos++;
                } else {
                    $fallidos++;
                    $errores[] = [
                        'fila'    => $numFila + 1,
                        'mensaje' => $resultado['error'],
                        'serial'  => $fila[$this->columnas['serial']] ?? '?',
                    ];
                }
            }
        } catch (\Throwable $e) {
            $fallidos++;
            $errores[] = ['fila' => '?', 'mensaje' => 'Error al leer el archivo: ' . $e->getMessage()];
        }

        // Registrar en tabla importaciones
        Importacion::create([
            'id_usuario'      => $usuario->id,
            'metodo'          => 'excel',
            'total_registros' => $exitosos + $fallidos,
            'exitosos'        => $exitosos,
            'fallidos'        => $fallidos,
            'archivo_nombre'  => $archivo->getClientOriginalName(),
            'errores_detalle' => !empty($errores) ? $errores : null,
        ]);

        return compact('exitosos', 'fallidos', 'errores');
    }

    // ── Procesar formulario multi-fila ───────────────────
    public function procesarFormulario(array $filas, Usuario $usuario): array
    {
        $exitosos = 0;
        $fallidos = 0;
        $errores  = [];

        DB::beginTransaction();
        try {
            foreach ($filas as $numFila => $datos) {
                // Validar serial obligatorio
                if (empty($datos['serial_number'])) {
                    $fallidos++;
                    $errores[] = ['fila' => $numFila + 1, 'mensaje' => 'Serial number vacío'];
                    continue;
                }

                // Verificar que no exista el serial
                if (Inventario::where('serial_number', trim($datos['serial_number']))->exists()) {
                    $fallidos++;
                    $errores[] = [
                        'fila'    => $numFila + 1,
                        'mensaje' => "Serial {$datos['serial_number']} ya existe en el sistema",
                    ];
                    continue;
                }

                try {
                    $item = Inventario::create([
                        'id_sitio'       => $datos['id_sitio'],
                        'id_ubicacion'   => $datos['id_ubicacion'],
                        'id_dispositivo' => $datos['id_dispositivo'],
                        'id_marca'       => $datos['id_marca'],
                        'id_modelo'      => $datos['id_modelo'],
                        'id_status'      => $datos['id_status'],
                        'serial_number'  => trim($datos['serial_number']),
                        'sita_asset_tag' => !empty($datos['sita_asset_tag']) ? trim($datos['sita_asset_tag']) : null,
                        'id_usuario_reg' => $usuario->id,
                    ]);

                    // Generar QR
                    $item->update(['qr_code' => "SITA-{$item->id}-{$item->serial_number}"]);
                    $exitosos++;
                } catch (\Throwable $e) {
                    $fallidos++;
                    $errores[] = [
                        'fila'    => $numFila + 1,
                        'mensaje' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $errores[] = ['fila' => '?', 'mensaje' => 'Error general: ' . $e->getMessage()];
        }

        // Registrar importación
        Importacion::create([
            'id_usuario'      => $usuario->id,
            'metodo'          => 'formulario',
            'total_registros' => $exitosos + $fallidos,
            'exitosos'        => $exitosos,
            'fallidos'        => $fallidos,
            'errores_detalle' => !empty($errores) ? $errores : null,
        ]);

        return compact('exitosos', 'fallidos', 'errores');
    }

    // ── Procesar una sola fila del Excel ─────────────────
    private function procesarFila(array $fila, int $numFila, Usuario $usuario): array
    {
        try {
            $serial = trim((string)($fila[$this->columnas['serial']] ?? ''));
            if (empty($serial)) {
                return ['ok' => false, 'error' => 'Serial number vacío'];
            }

            if (Inventario::where('serial_number', $serial)->exists()) {
                return ['ok' => false, 'error' => "Serial '{$serial}' ya existe"];
            }

            // Resolver FKs desde los catálogos
            $idSitio = $this->resolverSitio($fila[$this->columnas['site']] ?? '');
            if (!$idSitio) return ['ok' => false, 'error' => "Sitio '{$fila[$this->columnas['site']]}' no encontrado"];

            $idUbicacion = $this->resolverUbicacion($fila[$this->columnas['location']] ?? '', $idSitio);
            if (!$idUbicacion) return ['ok' => false, 'error' => "Ubicación '{$fila[$this->columnas['location']]}' no encontrada"];

            $idDispositivo = $this->resolverDispositivo($fila[$this->columnas['dispositivo']] ?? '');
            if (!$idDispositivo) return ['ok' => false, 'error' => "Dispositivo '{$fila[$this->columnas['dispositivo']]}' no encontrado"];

            $idMarca = $this->resolverMarca($fila[$this->columnas['marca']] ?? '');
            if (!$idMarca) return ['ok' => false, 'error' => "Marca '{$fila[$this->columnas['marca']]}' no encontrada"];

            $idModelo = $this->resolverModelo($fila[$this->columnas['modelo']] ?? '', $idMarca);
            if (!$idModelo) return ['ok' => false, 'error' => "Modelo '{$fila[$this->columnas['modelo']]}' no encontrado"];

            $idStatus = $this->resolverStatus($fila[$this->columnas['status']] ?? '');
            if (!$idStatus) return ['ok' => false, 'error' => "Status '{$fila[$this->columnas['status']]}' no encontrado"];

            $assetTag = trim((string)($fila[$this->columnas['asset_tag']] ?? ''));
            $assetTag = ($assetTag === '' || strtoupper($assetTag) === 'N/A') ? null : $assetTag;

            // Verificar asset_tag único si existe
            if ($assetTag && Inventario::where('sita_asset_tag', $assetTag)->exists()) {
                $assetTag = null; // ignorar duplicado silenciosamente
            }

            $item = Inventario::create([
                'id_sitio'       => $idSitio,
                'id_ubicacion'   => $idUbicacion,
                'id_dispositivo' => $idDispositivo,
                'id_marca'       => $idMarca,
                'id_modelo'      => $idModelo,
                'id_status'      => $idStatus,
                'serial_number'  => $serial,
                'sita_asset_tag' => $assetTag,
                'po_number'      => $this->limpiar($fila[$this->columnas['po']] ?? ''),
                'gap_active'     => $this->limpiar($fila[$this->columnas['gap']] ?? ''),
                'nodename'       => $this->limpiar($fila[$this->columnas['nodename']] ?? ''),
                'comentarios'    => $this->limpiar($fila[$this->columnas['comentarios']] ?? ''),
                'id_usuario_reg' => $usuario->id,
            ]);

            $item->update(['qr_code' => "SITA-{$item->id}-{$item->serial_number}"]);

            return ['ok' => true];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    // ── Helpers de resolución de catálogos ───────────────

    private function resolverSitio(string $clave): ?int
    {
        $sitio = CatSitio::where('clave', strtoupper(trim($clave)))->first();
        return $sitio?->id;
    }

    private function resolverUbicacion(string $nombre, int $idSitio): ?int
    {
        $ub = CatUbicacion::where('id_sitio', $idSitio)
            ->whereRaw('UPPER(nombre) = ?', [strtoupper(trim($nombre))])
            ->first();
        return $ub?->id;
    }

    private function resolverDispositivo(string $tipo): ?int
    {
        $d = CatDispositivo::whereRaw('UPPER(tipo) = ?', [strtoupper(trim($tipo))])->first();
        return $d?->id;
    }

    private function resolverMarca(string $nombre): ?int
    {
        $m = CatMarca::whereRaw('UPPER(nombre) = ?', [strtoupper(trim($nombre))])->first();
        return $m?->id;
    }

    private function resolverModelo(string $modelo, int $idMarca): ?int
    {
        $mo = CatModelo::where('id_marca', $idMarca)
            ->whereRaw('UPPER(numero_modelo) = ?', [strtoupper(trim($modelo))])
            ->first();
        return $mo?->id;
    }

    private function resolverStatus(string $nombre): ?int
    {
        // Normalizar variantes del Excel: instalado/Instalado/Instalada → Instalado
        $mapa = [
            'INSTALADO' => 'Instalado',
            'INSTALADA' => 'Instalado',
            'SPARE'     => 'Spare',
            'BODEGA'    => 'Bodega',
            'DAÑADO'    => 'Dañado',
            'DANADO'    => 'Dañado',
            'GAP'       => 'GAP',
        ];

        $key    = strtoupper(trim($nombre));
        $normalizado = $mapa[$key] ?? ucfirst(strtolower(trim($nombre)));

        $st = CatStatus::where('nombre', $normalizado)->first();
        return $st?->id;
    }

    private function limpiar(string $valor): ?string
    {
        $valor = trim($valor);
        if ($valor === '' || strtoupper($valor) === 'N/A') return null;
        return $valor;
    }

    private function filaVacia(array $fila): bool
    {
        return empty(array_filter($fila, fn($v) => $v !== null && $v !== ''));
    }
}

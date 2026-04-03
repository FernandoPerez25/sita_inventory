<?php

namespace App\Services;

use App\Models\Inventario;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrService
{
    // Genera el string del QR y lo guarda en la BD
    public function generarYGuardar(Inventario $item): string
    {
        $contenido = "SITA-{$item->id}-{$item->serial_number}";
        $item->update(['qr_code' => $contenido]);
        return $contenido;
    }

    // SVG limpio para insertar inline en HTML — no requiere Imagick ni GD
    public function generarImagen(string $contenido, int $size = 300): string
    {
        $svg = QrCode::size($size)
            ->errorCorrection('H')
            ->margin(2)
            ->generate($contenido);

        // Eliminar declaración XML que rompe el renderizado inline en navegadores
        $svg = preg_replace('/<\?xml[^>]*\?>\s*/i', '', $svg);
        $svg = trim($svg);

        return $svg;
    }

    public function generarEtiqueta(string $contenido): string
    {
        $svg = QrCode::size(150)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($contenido);

        $svg = preg_replace('/<\?xml[^>]*\?>\s*/i', '', $svg);
        return trim($svg);
    }
}

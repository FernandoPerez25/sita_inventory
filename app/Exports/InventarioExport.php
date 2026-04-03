<?php

namespace App\Exports;

use App\Models\Inventario;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarioExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(private array $filtros = []) {}

    public function query()
    {
        return Inventario::with([
            'sitio',
            'ubicacion',
            'dispositivo',
            'marca',
            'modelo',
            'status',
            'usuarioRegistro',
        ])
            ->porSitio($this->filtros['sitio'] ?? null)
            ->porStatus($this->filtros['status'] ?? null)
            ->porDispositivo($this->filtros['dispositivo'] ?? null)
            ->buscar($this->filtros['q'] ?? null)
            ->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'ID',
            'SITE',
            'LOCATION',
            'Dispositivo',
            'Marca',
            'Model Number',
            'Serial Number',
            'SITA Asset Tag',
            'PO #',
            'GAP ACTIVE',
            'NODENAME',
            'STATUS ACTUAL',
            'Comentarios',
            'Registrado por',
            'Fecha registro',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->sitio->clave         ?? '',
            $row->ubicacion->nombre    ?? '',
            $row->dispositivo->tipo    ?? '',
            $row->marca->nombre        ?? '',
            $row->modelo->numero_modelo ?? '',
            $row->serial_number,
            $row->sita_asset_tag       ?? '',
            $row->po_number            ?? '',
            $row->gap_active           ?? '',
            $row->nodename             ?? '',
            $row->status->nombre       ?? '',
            $row->comentarios          ?? '',
            $row->usuarioRegistro->nombre_completo ?? '',
            $row->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Encabezados en negrita con fondo azul SITA
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1A3A5C']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}

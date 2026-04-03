<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\Usuario|null $usuario */
        $usuario = Auth::user();
        return $usuario !== null && $usuario->puedeEditar();
    }

    public function rules(): array
    {
        // Al editar, ignorar el serial_number del registro actual
        $inventarioId = $this->route('inventario')?->id;
        $uniqueSerial = 'unique:inventario,serial_number' . ($inventarioId ? ",{$inventarioId}" : '');
        $uniqueAsset  = 'unique:inventario,sita_asset_tag'  . ($inventarioId ? ",{$inventarioId}" : '');

        return [
            // FKs — selects en la UI
            'id_sitio'        => ['required', 'exists:cat_sitios,id'],
            'id_ubicacion'    => ['required', 'exists:cat_ubicaciones,id'],
            'id_dispositivo'  => ['required', 'exists:cat_dispositivos,id'],
            'id_marca'        => ['required', 'exists:cat_marcas,id'],
            'id_modelo'       => ['required', 'exists:cat_modelos,id'],
            'id_status'       => ['required', 'exists:cat_status,id'],

            // Campos únicos obligatorios
            'serial_number'   => ['required', 'string', 'max:60', $uniqueSerial],

            // Opcionales
            'sita_asset_tag'  => ['nullable', 'string', 'max:40', $uniqueAsset],
            'po_number'       => ['nullable', 'string', 'max:40'],
            'gap_active'      => ['nullable', 'string', 'max:40'],
            'nodename'        => ['nullable', 'string', 'max:60'],
            'comentarios'     => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_sitio.required'       => 'Selecciona un sitio.',
            'id_ubicacion.required'   => 'Selecciona una ubicación.',
            'id_dispositivo.required' => 'Selecciona el tipo de dispositivo.',
            'id_marca.required'       => 'Selecciona la marca.',
            'id_modelo.required'      => 'Selecciona el modelo.',
            'id_status.required'      => 'Selecciona el status del dispositivo.',
            'serial_number.required'  => 'El número de serie es obligatorio.',
            'serial_number.unique'    => 'Este número de serie ya está registrado.',
            'sita_asset_tag.unique'   => 'Este SITA Asset Tag ya está registrado.',
        ];
    }
}

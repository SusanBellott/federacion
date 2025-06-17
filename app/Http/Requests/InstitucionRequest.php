<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstitucionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $institucion = \App\Models\Institucion::where('uuid_institucion', $this->route('uuid'))->first();
        $id = $institucion?->id_institucion ?? null;
        return [
            'id_distrito' => 'required|integer|exists:distritos,id_distrito',
            'subsistema' => 'required|string|in:ALTERNATIVA Y ESPECIAL PERMANENTE,EDUCACION SUPERIOR,REGULAR',


            'servicio' => [
                'required',
                'numeric',
                'between:5520221,5528626',
                Rule::unique('instituciones', 'servicio')->ignore($id, 'id_institucion'),
            ],

            'servicio_generado' => 'required|numeric',
            'nivel' => 'required|string|in:ALTERNATIVA,ESPECIAL,INICIAL,PERMANENTE,PRIMARIA,SECUNDARIA,SUPERIOR',


        ];
    }

    public function messages()
    {
        return [
            'id_distrito.required' => 'El campo "Distrito" es obligatorio.',
            'id_distrito.exists' => 'El distrito seleccionado no es válido.',
            'subsistema.required' => 'El campo "Subsistema" es obligatorio.',
            'subsistema.in' => 'Seleccione un subsistema válido.',
            'servicio.unique' => 'Este número de servicio ya está registrado en otra institución.',
            'servicio.required' => 'El campo "Servicio" es obligatorio.',
            'servicio.numeric' => 'El campo "Servicio" debe ser un número.',
            'servicio.between' => 'El campo "Servicio" debe estar entre 5520221 y 5528626.',
            'servicio_generado.required' => 'El campo "Servicio Generado" es obligatorio.',
            'nivel.required' => 'El campo "Nivel" es obligatorio.',
            'nivel.in' => 'Seleccione un nivel válido.',

        ];
    }
}

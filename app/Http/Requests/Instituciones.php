<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Instituciones extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_distrito' => 'required',
            'subsistema' => 'required',
            'servicio' => 'required|numeric|between:5520221,5528626',
            'servicio_generado' => 'required',
            'nivel' => 'required',
            'programa' => 'required|numeric|between:11,80730947',
            'unidad_educativa' => [
                'required',
                'string',
                'regex:/[A-Za-zÁÉÍÓÚáéíóúÑñ]/u',
                'max:255',
            ],
        ];
    }
    public function messages()
    {
        return [
            'id_distrito.required' => 'El campo "Distrito" es obligatorio.',
            'subsistema.required' => 'El campo "Subsistema" es obligatorio.',
            'servicio.required' => 'El campo "Servicio" es obligatorio.',
            'servicio.numeric' => 'El campo "Servicio" debe ser un número.',
            'servicio.between' => 'El campo "Servicio" debe estar entre 5520221 y 5528626.',
            'servicio_generado.required' => 'El campo "Servicio Generado" es obligatorio.',
            'nivel.required' => 'El campo "Nivel" es obligatorio.',
            'programa.required' => 'El campo "Programa" es obligatorio.',
            'programa.numeric' => 'El campo "Programa" debe ser un número.',
            'programa.between' => 'El campo "Programa" debe estar entre 11 y 80730947.',
            'unidad_educativa.required' => 'El campo "Unidad Educativa" es obligatorio.',
            'unidad_educativa.regex' => 'La descripción del distrito debe contener al menos una letra.',
            'unidad_educativa.max' => 'La descripción no puede tener más de 255 caracteres.',  // Si estás usando el max:255
        ];
    }
}

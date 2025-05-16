<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\CodigoSie;

class CodigoSieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'distrito_id' => 'required|exists:distritos,id_distrito',
           'programa' => [
    'required',
    'numeric',
    'between:11,80730947',
    Rule::unique('codigo_sies', 'programa')
    ->ignore(
        CodigoSie::where('uuid_codigo_sie', $this->route('codigo_sie'))->value('id_codigo_sie'),
        'id_codigo_sie'
    ),

    
],


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
            'distrito_id.required' => 'El campo "Distrito" es obligatorio.',
'distrito_id.exists' => 'El distrito seleccionado no es válido.',

            'programa.required' => 'El campo "Programa" es obligatorio.',
            'programa.numeric' => 'El campo "Programa" debe ser numérico.',
            'programa.between' => 'El campo "Programa" debe estar entre 11 y 80730947.',
'programa.unique' => 'Este Código ya está registrado en el sistema.',

            'unidad_educativa.required' => 'El campo "Unidad Educativa" es obligatorio.',
            'unidad_educativa.regex' => 'La unidad educativa debe contener al menos una letra.',
            'unidad_educativa.max' => 'La unidad educativa no puede superar los 255 caracteres.',
        ];
    }
}

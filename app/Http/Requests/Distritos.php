<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Distritos extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        // Usando el operador ternario para asignar el valor de la regla 'unique' o vacío
        $validar = $this->isMethod('put') ? '' : 'unique:distritos,codigo';

        $rules = [
            'codigo' => (String)'required|integer|digits:3|min:200|max:300|' . $validar,
            'descripcion' => [
                'required',
                'string',
                'regex:/[A-Za-zÁÉÍÓÚáéíóúÑñ]/u',
                'max:255',
            ],
        ];
    
        return $rules;
    }
    public function messages()
    {
        return [
            'codigo.required' => 'El campo "Codigo" es obligatorio.',
            'codigo.digits' => 'El código de distrito debe tener exactamente 3 dígitos.',
            'codigo.min' => 'El código de distrito no puede ser menor a 200.',
            'codigo.max' => 'El código de distrito no puede ser mayor a 300.',
            'codigo.unique' => 'Este Codigo ya está registrado en el sistema.',

            'descripcion.required' => 'El campo "Descripción" es obligatorio.',
            'descripcion.regex' => 'La descripción del distrito debe contener al menos una letra.',
            'descripcion.max' => 'La descripción no puede tener más de 255 caracteres.',  // Si estás usando el max:255

        ];
    }
}

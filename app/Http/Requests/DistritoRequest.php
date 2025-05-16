<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistritoRequest  extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $distritoId = null;
    
        // Solo si es edición buscamos el id_distrito usando el uuid
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $distrito = \App\Models\Distrito::where('uuid_distrito', $this->route('uuid'))->first();
            $distritoId = $distrito?->id_distrito ?? null;
        }
    
        return [
            'codigo' => [
                'required',
                'integer',
                'digits:3',
                'min:200',
                'max:300',
                'unique:distritos,codigo,' . ($distritoId ?? 'NULL') . ',id_distrito',
            ],
            'descripcion' => [
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
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMethod('put') || $this->isMethod('patch')) {
                $codigo = $this->input('codigo');
    
                // Buscar el distrito actual por UUID
                $distrito = \App\Models\Distrito::where('uuid_distrito', $this->route('uuid'))->first();
                $distritoId = $distrito?->id_distrito ?? null;
    
                // Verificar si existe otro distrito con el mismo código
                $existente = \App\Models\Distrito::where('codigo', $codigo)
                    ->where('id_distrito', '!=', $distritoId)
                    ->first();
    
                if ($existente) {
                    $validator->errors()->add('codigo', 'Este código ya pertenece al distrito: ' . strtoupper($existente->descripcion));
                }
            }
        });
    }
    
}

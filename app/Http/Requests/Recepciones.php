<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Recepciones extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'institucion_id' => 'required',
            'distrito_id' => 'required|exists:distritos,id_distrito',
'codigo_sie_id' => 'required|exists:codigo_sies,id_codigo_sie',

            'id_curso' => 'required',
            'ci' => 'required|numeric|digits_between:3,10|unique:users,ci',  // Reglas para 'ci'
            'name' => 'required|regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+$/u|max:30',
            'name2' => 'nullable|regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+$/u|max:30',
            'primer_apellido' => 'nullable|regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+$/u|max:30',
            'segundo_apellido' => 'nullable|regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+$/u|max:30',
          //  'email' => 'required|email|unique:users,email',
            'rda' => 'required|numeric|digits_between:2,10|unique:users,rda',
            'item' => 'required|integer|min:1|max:99993',
            'cargo' => 'required|integer|min:20|max:9082',
            'horas' => 'required|integer|min:8|max:160',

        ];
    }
    public function messages()
    {
        return [
            'institucion_id.required' => 'El campo "Institución" es obligatorio.',
            'distrito_id.required' => 'El campo Distrito es obligatorio.',
'distrito_id.exists' => 'El distrito seleccionado no es válido.',
'codigo_sie_id.required' => 'El campo Código SIE es obligatorio.',
'codigo_sie_id.exists' => 'El código SIE seleccionado no es válido.',

            'id_curso.required' => 'El campo "Curso" es obligatorio.',
            'ci.required' => 'El campo CI es obligatorio.',
            'ci.numeric' => 'El CI debe ser un número.',
            'ci.digits_between' => 'El CI debe tener entre 3 y 10 dígitos.',
            'ci.unique' => 'Este CI ya está registrado en el sistema.',
            'name.required' => 'El campo "Nombre" es obligatorio.',

           
            'name.regex' => 'El nombre solo puede contener letras sin espacios ni caracteres especiales.',
            'name2.regex' => 'El nombre solo puede contener letras sin espacios ni caracteres especiales.',
            'primer_apellido.regex' => 'El apellido paterno solo puede contener letras sin espacios ni caracteres especiales.',
            'segundo_apellido.regex' => 'El apellido materno solo puede contener letras sin espacios ni caracteres especiales.',

            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está registrado. Por favor, ingrese otro.',

            'rda.required' => 'El campo RDA es obligatorio.',
            'rda.numeric' => 'El RDA debe ser un valor numérico.',
            'rda.unique' => 'Este RDA ya está registrado. Por favor, ingrese otro.',
            'rda.digits_between' => 'El RDA debe tener entre 2 y 10 dígitos.',

            'item.required' => 'El campo Item es obligatorio.',
            'item.numeric' => 'El Item debe ser un valor numérico.',
            'item.min' => 'El item debe ser al menos 1.',
            'item.max' => 'El item no puede ser mayor a 99993.',
            'cargo.required' => 'El campo Cargo es obligatorio.',
            'cargo.numeric' => 'El campo Cargo debe ser un valor numérico.',
            'cargo.min' => 'El cargo debe ser mínimo 20.',
            'cargo.max' => 'El cargo no puede ser mayor a 9082.',
            'horas.required' => 'El campo Horas es obligatorio.',
            'horas.numeric' => 'El Horas debe ser un valor numérico.',
            'horas.min' => 'Las horas deben ser al menos 8.',
            'horas.max' => 'Las horas no pueden superar 160.',

        ];
    }


}

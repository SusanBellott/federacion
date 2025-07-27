<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoActividadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'horas_minimas' => [
                'integer',
                'regex:/^\d+$/',
                'min:2',
                'max:400',
            ],

        ];
    }
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del taller es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 100 caracteres.',
            'horas_minimas.required' => 'Debe ingresar las horas mínimas.',
            'horas_minimas.integer' => 'Las horas mínimas deben ser un número entero.',
            'horas_minimas.min' => 'Las horas mínimas deben ser al menos 2.',
            'horas_minimas.max' => 'Las horas mínimas no deben ser mayores a 400.',
            'horas_minimas.regex' => 'Las horas mínimas deben contener solo números enteros positivos, sin letras ni caracteres especiales.',

        ];
    }
}

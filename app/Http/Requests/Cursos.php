<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Curso;

class Cursos extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
'tipo_actividad_id' => [
    'required',
    'exists:tipos_actividad,id',
    Rule::unique('cursos', 'tipo_actividad_id')->ignore($this->getCursoId(), 'id_curso'),
],


            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',

            // Inscripción: hoy en adelante
            'fecha_inicio_inscripcion' => 'required|date|after_or_equal:today',
            'fecha_fin_inscripcion' => 'required|date|after_or_equal:fecha_inicio_inscripcion',

            // Curso: después de inscripción
            'fecha_inicio' => 'required|date|after_or_equal:fecha_fin_inscripcion',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',

            // Carga horaria no debe enviarse manualmente
           'carga_horaria' => 'nullable|integer|min:1|max:1000',

        ];
        
    }

    public function messages(): array
    {
        return [
            'tipo_actividad_id.required' => 'El campo tipo de actividad es obligatorio.',
            'tipo_actividad_id.exists' => 'El tipo de actividad seleccionado no existe.',
            'tipo_actividad_id.unique' => 'Este tipo de actividad ya está asignado a otro curso.',

            'nombre.required' => 'El nombre del curso es obligatorio.',
            'descripcion.required' => 'La descripción es obligatoria.',

            'fecha_inicio_inscripcion.required' => 'La fecha de inicio de inscripción es obligatoria.',
            'fecha_inicio_inscripcion.after_or_equal' => 'La fecha de inicio de inscripción no puede ser anterior a hoy.',

            'fecha_fin_inscripcion.required' => 'La fecha fin de inscripción es obligatoria.',
            'fecha_fin_inscripcion.after_or_equal' => 'La fecha fin debe ser posterior a la fecha de inicio de inscripción.',

            'fecha_inicio.required' => 'La fecha de inicio del curso es obligatoria.',
            'fecha_inicio.after_or_equal' => 'La fecha de inicio del curso debe ser después de la inscripción.',

            'fecha_fin.required' => 'La fecha de finalización del curso es obligatoria.',
            'fecha_fin.after_or_equal' => 'La fecha de finalización debe ser posterior a la fecha de inicio del curso.',

            'carga_horaria.prohibited' => 'La carga horaria no puede ser modificada desde aquí. Se asigna automáticamente.',
        ];
    }
    protected function getCursoId()
{
    $uuid = $this->route('id'); // o el nombre de la ruta (ver web.php)
    return \App\Models\Curso::where('uuid_curso', $uuid)->value('id_curso');
}

}
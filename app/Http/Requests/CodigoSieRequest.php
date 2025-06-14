<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\CodigoSie;
use App\Models\Institucion;

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
               'institucion_id' => 'required|exists:instituciones,id_institucion',

'programa' => [
    'required',
    'numeric',
    'between:11,80730947',
    function ($attribute, $value, $fail) {
        $institucionId = $this->input('institucion_id');
        $codigoId = CodigoSie::where('uuid_codigo_sie', $this->route('codigo_sie'))->value('id_codigo_sie');

        $institucion = Institucion::find($institucionId);
        if (!$institucion) {
            $fail('Institución no válida.');
            return;
        }

        $nivel = strtoupper(trim($institucion->nivel)); // ✅ el nivel viene desde la tabla institución
        $nivelesPermitidos = ['INICIAL', 'PRIMARIA', 'SECUNDARIA'];

        // Verifica todos los registros que usan este código en esta misma institución
        $registros = CodigoSie::where('programa', $value)
            ->where('institucion_id', $institucionId);

        if ($codigoId) {
            $registros->where('id_codigo_sie', '!=', $codigoId);
        }

        $nivelesUsados = $registros
            ->join('instituciones', 'codigo_sies.institucion_id', '=', 'instituciones.id_institucion')
            ->pluck('instituciones.nivel')
            ->unique();

        if (in_array($nivel, $nivelesPermitidos)) {
            if ($nivelesUsados->count() >= 3 || $nivelesUsados->contains($nivel)) {
                $fail('Este código SIE ya fue registrado para este nivel en la misma institución.');
            }
        } else {
            // Para niveles no permitidos, debe ser completamente único
            if ($nivelesUsados->count() > 0) {
                $fail('Este código SIE ya está registrado y no puede repetirse en este nivel.');
            }
        }
    }
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


            'institucion_id.required' => 'El campo "Institución" es obligatorio.',
            'institucion_id.exists' => 'La institución seleccionada no es válida.',


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

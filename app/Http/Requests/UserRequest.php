<?php
// App\Http\Requests\UserRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $ci = $this->input('ci');
        $complemento = $this->input('complemento_ci');

        // Buscar el ID real
        $userId = User::where('uuid_user', $this->route('uuid'))->value('id');

        return [
            'ci' => [
                'required',
                'numeric',
                'digits_between:3,10',
                Rule::unique('users')->where(function ($query) use ($complemento) {
                    return $query->where('complemento_ci', $complemento);
                })->ignore($userId),
            ],
'complemento_ci' => [
    'nullable',
    'regex:/^[A-Z0-9\-]{1,5}$/i',
    function ($attribute, $value, $fail) use ($ci, $userId) {
        $usuarioExistente = User::where('ci', $ci)
            ->where(function ($query) use ($value) {
                if ($value) {
                    $query->where('complemento_ci', $value);
                } else {
                    $query->whereNull('complemento_ci');
                }
            })
            ->where('id', '!=', $userId)
            ->first();

        if ($usuarioExistente) {
            if (!$value) {
                // Mensaje para el campo CI
                $fail('ci', 'Este número de carnet ya está asignado a un usuario dentro del sistema.');
                // Mensaje para el complemento
                $fail('Agrega un complemento para este número de carnet.');
            } else {
                $fail('Ya existe un usuario con este CI y complemento.');
            }
        }
    }
],



            'rda' => [
                'required',
                'numeric',
                'digits_between:2,10',
                Rule::unique('users', 'rda')->ignore($userId),
            ],

            'name' => 'required|regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+(?:\s[\pLÁÉÍÓÚáéíóúÑñ]+)*$/u|max:30',

            'primer_apellido' => 'nullable|regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+$/u|max:30',
            'segundo_apellido' => 'nullable|regex:/^[\pLÁÉÍÓÚáéíóúÑñ]+$/u|max:30',
            'item' => 'required|integer|min:1|max:99993',
            'cargo' => 'required|integer|min:20|max:9082',
            'horas' => 'required|integer|min:8|max:160',
            'email' => 'nullable|email|max:255',
            'distrito_id' => 'required|exists:distritos,id_distrito',
            'codigo_sie_id' => 'required|exists:codigo_sies,id_codigo_sie',
            'institucion_id' => 'required|exists:instituciones,id_institucion',
            'role' => 'required|exists:roles,id',
        ];

        // Si estamos actualizando, añadir regla para el email
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $user = $this->route('uuid');
            $userId = \App\Models\User::where('uuid_user', $user)->first()->id ?? null;

            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ];
        }

        return $rules;
    }
    public function messages()
    {
        return [
            'ci.required' => 'El campo CI es obligatorio.',
            'ci.numeric' => 'El CI debe ser un número.',
            'ci.digits_between' => 'El CI debe tener entre 3 y 10 dígitos.',
            'ci.unique' => 'Este CI ya está registrado en el sistema',
            'complemento_ci.required' => 'El complemento CI es obligatorio porque el CI ya existe.',
            'complemento_ci.regex' => 'El complemento CI debe contener solo letras, números o guiones.',


            'name.required' => 'El campo "Nombre" es obligatorio.',

            'primer_apellido.required' => 'El campo "Primer Apellido" es obligatorio.',
            'segundo_apellido.required' => 'El campo "Segundo Apellido" es obligatorio.',
            'name.regex' => 'El nombre solo puede contener letras y espacios entre palabras, sin espacios al inicio o al final.',

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
            'institucion_id.required' => 'El campo "Institución" es obligatorio.',
            'distrito_id.required' => 'El campo Distrito es obligatorio.',
            'distrito_id.exists' => 'El distrito seleccionado no es válido.',
            'codigo_sie_id.required' => 'El campo Código SIE es obligatorio.',
            'codigo_sie_id.exists' => 'El código SIE seleccionado no es válido.',
            'role.required' => 'Se requiere un rol.',
            'role.exists' => 'El rol seleccionado no es válido.',

        ];
    }
}
?>

<?php
// App\Http\Requests\DistritoRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Distrito;

class DistritoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $distrito = Distrito::where('uuid_distrito', $this->route('uuid'))->first();
        $id = $distrito?->id_distrito ?? null;

        return [
            'codigo' => [
                'required',
                'integer',
                'digits:3',
                'min:200',
                'max:300',
                Rule::unique('distritos', 'codigo')->ignore($id, 'id_distrito'),
            ],
            'descripcion' => [
                'required',
                'string',
                'regex:/^[\pLÁÉÍÓÚáéíóúÑñ\s]+$/u',
                'max:255',
            ],
        ];
    }

    public function messages()
    {
        return [
            'codigo.required' => 'El campo "Código" es obligatorio.',
            'codigo.integer' => 'El código debe ser numérico.',
            'codigo.digits' => 'Debe tener exactamente 3 dígitos.',
            'codigo.min' => 'Debe ser mínimo 200.',
            'codigo.max' => 'Debe ser máximo 300.',
            'codigo.unique' => 'Este código ya está registrado.',

            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.regex' => 'La descripción solo puede contener letras y espacios.',
            'descripcion.max' => 'No puede superar 255 caracteres.',
        ];
    }
}
?>

<?php
// App\Http\Requests\InstitucionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Institucion;

class InstitucionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $institucion = Institucion::where('uuid_institucion', $this->route('uuid'))->first();
        $id = $institucion?->id_institucion ?? null;

        return [
            'id_distrito' => 'required|exists:distritos,id_distrito',
            'servicio' => [
                'required',
                'numeric',
                'between:5520221,5528626',
                Rule::unique('instituciones', 'servicio')->ignore($id, 'id_institucion'),
            ],
            'servicio_generado' => 'required|numeric',

            'subsistema' => [
                'required',
                'string',
                'in:EDUCACION SUPERIOR,REGULAR,ALTERNATIVA,ESPECIAL PERMANENTE',
            ],

            'nivel' => [
                'required',
                'string',
                'in:ALTERNATIVA,ESPECIAL,INICIAL,PERMANENTE,PRIMARIA,SECUNDARIA',
            ],
        ];
    }

    public function messages()
    {
        return [
            'id_distrito.required' => 'El campo "Distrito" es obligatorio.',
            'id_distrito.exists' => 'El distrito seleccionado no es válido.',

            'servicio.required' => 'El campo "Servicio" es obligatorio.',
            'servicio.numeric' => 'Debe ser un número.',
            'servicio.between' => 'Debe estar entre 5520221 y 5528626.',
            'servicio.unique' => 'Este número ya está registrado.',

            'servicio_generado.required' => 'Este campo es obligatorio.',
            'servicio_generado.numeric' => 'Debe ser numérico.',

            'subsistema.required' => 'Selecciona un subsistema.',
            'subsistema.in' => 'Valor inválido.',

            'nivel.required' => 'Selecciona un nivel.',
            'nivel.in' => 'Valor inválido.',
        ];
    }
}
?>

<?php
// App\Http\Requests\CodigoSieRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\CodigoSie;

class CodigoSieRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $codigoId = CodigoSie::where('uuid_codigo_sie', $this->route('codigo_sie'))->value('id_codigo_sie');

        return [
            'distrito_id' => 'required|exists:distritos,id_distrito',
            'institucion_id' => 'required|exists:instituciones,id_institucion',

            'programa' => [
                'required',
                'numeric',
                'between:11,80730947',
                Rule::unique('codigo_sies', 'programa')->ignore($codigoId, 'id_codigo_sie'),
            ],

            'unidad_educativa' => [
                'required',
                'string',
                'regex:/[A-Za-zÁÉÍÓÚáéíóúÑñ\s]/u',
                'max:255',
            ],
        ];
    }

    public function messages()
    {
        return [
            'distrito_id.required' => 'El campo "Distrito" es obligatorio.',
            'distrito_id.exists' => 'El distrito no es válido.',

            'institucion_id.required' => 'El campo "Institución" es obligatorio.',
            'institucion_id.exists' => 'La institución seleccionada no es válida.',

            'programa.required' => 'El campo "Programa" es obligatorio.',
            'programa.numeric' => 'Debe ser un número.',
            'programa.between' => 'Debe estar entre 11 y 80730947.',
            'programa.unique' => 'Este código ya está registrado.',

            'unidad_educativa.required' => 'Este campo es obligatorio.',
            'unidad_educativa.regex' => 'Debe contener letras.',
            'unidad_educativa.max' => 'No puede superar los 255 caracteres.',
        ];
    }
}
?>
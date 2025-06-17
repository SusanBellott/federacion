<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User as ModelUser;
use App\Models\Inscripcion as ModelInscripcion;
use App\Models\Curso;
use App\Models\Distrito;
use App\Models\CodigoSie;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Institucion as ModelInstitucion;
use App\Http\Requests\Recepciones;

class Recepcion extends Controller
{
    function __construct()
    {
        $this->middleware('permission:recepciones.index', ['only' => ['index']]);
        $this->middleware('permission:recepcion.create', ['only' => ['store']]);
        $this->middleware('permission:editarestadorecepcion.update', ['only' => ['updatedelete']]);
        $this->middleware('permission:recepcioneseditar.update', ['only' => ['update']]);
    }

    /**
     * Muestra una lista paginada de usuarios con opción de búsqueda por varios campos,
     * y también proporciona listas de instituciones y cursos activos para su posible uso en la vista.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $searchTerm = request()->query('search');
        $perPage = request()->query('perPage', 10);
        $query = ModelUser::query();
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('rda', 'like', "%{$searchTerm}%")
                    ->orWhere('name', 'like', "%{$searchTerm}%")
                    ->orWhere('primer_apellido', 'like', "%{$searchTerm}%")
                    ->orWhere('segundo_apellido', 'like', "%{$searchTerm}%")
                    ->orWhere('item', 'like', "%{$searchTerm}%")
                    ->orWhere('cargo', 'like', "%{$searchTerm}%")
                    ->orWhere('horas', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }
        $usuarios = $query->paginate($perPage);
        $instituciones = ModelInstitucion::where('estado', '<>', 'eliminado')->get();


        $hoy = Carbon::now(); // ✅ este filtro es la solución
        $cursos = Curso::where('estado', 'activo')
            ->whereDate('fecha_inicio_inscripcion', '<=', $hoy)
            ->whereDate('fecha_fin_inscripcion', '>=', $hoy)
            ->get();

        return Inertia::render('Recepcion', [
            'usuarios' => $usuarios,
            'instituciones' => $instituciones,
            'cursos' => $cursos,
            'distritos' => Distrito::all(),
            'codigosSie' => CodigoSie::all(),
            'filters' => [
                'search' => $searchTerm,
                'perPage' => $perPage
            ]
        ]);
    }

    /**
     * Almacena un nuevo usuario y su inscripción a un curso en la base de datos.
     * Valida los datos de la petición utilizando el formulario de solicitud 'Recepciones',
     * crea un nuevo usuario con un correo electrónico generado automáticamente y una contraseña basada en su CI,
     * le asigna el rol de 'Estudiante' y luego crea una inscripción para ese usuario al curso seleccionado.
     * Finalmente, redirige a la página de inscritos con un filtro de búsqueda por el UUID del usuario recién creado.
     *
     * @param  \App\Http\Requests\Recepciones  $request El objeto de la petición HTTP validada.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Recepciones $request)
    {
        $validated = $request->validated();
        $inicial = strtoupper(substr($validated['name'], 0, 1));
        $correo = $inicial . '_' . $request->rda . '@fdteulp.com';
        $registroguardar = ModelUser::create([
            'institucion_id' => $request->institucion_id,
            'distrito_id' => $request->distrito_id,            // ✅ AÑADIR
            'codigo_sie_id' => $request->codigo_sie_id,
            'uuid_user' => Str::uuid(),
            'ci' => $request->ci,
            'complemento_ci' => $request->complemento_ci,
            'rda' => $request->rda,
            'name' => trim($request->name . ' ' . ($request->name2 ?? '')),

            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'item' => $request->item,
            'cargo' => $request->cargo,
            'horas' => $request->horas,
            'email' => $correo,
            'password' => bcrypt($request->ci),
            'estado' => 'activo'
        ])->assignRole('Estudiante');
        $registroId = $registroguardar->id;
        $registrouuId = $registroguardar->uuid_user;
        // Solo crear inscripción si se seleccionó un curso
        if ($request->filled('id_curso')) {
            ModelInscripcion::create([
                'id_user' => $registroId,
                'id_curso' => $request->id_curso,
                'uuid_inscripcion' => Str::uuid(),
                'fecha_inscripcion' => Carbon::now(),
                'estado_ins' => "inscrito"
            ]);
        }

        return redirect()
            ->route('inscritos.index', [
                'perPage' => 10,
                'search' => (string) $registrouuId,

            ])
            ->with('success', 'Usuario inscrito correctamente')
            ->with('datos_array', [$validated]);
    }
}

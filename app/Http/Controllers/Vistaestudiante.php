<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Curso as ModelCurso;
use App\Models\Inscripcion as ModelInscripcion;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Vistaestudiante extends Controller
{
    function __construct()
    {
        $this->middleware('permission:estudiantes.index', ['only' => ['index']]);
        $this->middleware('permission:editarestadodeleteestudiantes.update', ['only' => ['updatedelete']]);
        $this->middleware('permission:estudianteseditar.update', ['only' => ['update']]);
    }
    /**
     * Muestra la página de mis cursos para el usuario autenticado.
     * Recupera las inscripciones activas del usuario, cargando la información del curso, con opción de búsqueda.
     * También lista los cursos activos a los que el usuario aún no se ha inscrito, con opción de búsqueda.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();
        $miid = $user->id;
        $nombre_user = $user->name . " " . $user->primer_apellido . " " . $user->segundo_apellido;
        $searchTerm = request()->query('search');
        $perPage = request()->query('perPage', 10);

        // Filtros principales: usuario autenticado + estados activos
        $query = ModelInscripcion::where('id_user', $miid)
            ->where('estado', 'activo') // Inscripción activa
            ->whereHas('user', function ($q) {
                $q->where('estado', 'activo'); // Usuario activo
            })
            ->whereHas('curso', function ($q) {
                $q->where('estado', 'activo'); // Curso activo
            });
        // Búsqueda (opcional)
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('fecha_inscripcion', 'like', "%{$searchTerm}%")
                    ->orWhereHas('curso', function ($q) use ($searchTerm) {
                        $q->where('nombre', 'like', "%{$searchTerm}%")
                            ->orWhere('descripcion', 'like', "%{$searchTerm}%");
                    });
            });
        }
        $miscursos = $query->with(['curso'])->paginate($perPage);
        //---------------------------------------------------------------------------------------------------------
        $searchTerm2 = request()->query('searchuser');
        $perPage2 = request()->query('perPage2', 6);

      // Cursos gratuitos (tipo_pago: 'libre')
$queryGratis = ModelCurso::select('cursos.*')
    ->withCount([
        'inscripciones as total_inscritos' => function ($q) {
            $q->where('estado', 'activo');
        }
    ])
    ->where('estado_curso', '!=', 'terminado')
    ->where('tipo_pago', 'libre') // o 'gratis', según cómo lo tengas
    ->whereDoesntHave('inscripciones', function ($q) use ($user) {
        $q->where('id_user', $user->id)
            ->where('estado', 'activo');
    });

if ($searchTerm2) {
    $queryGratis->where(function ($q) use ($searchTerm2) {
        $q->where('nombre', 'like', "%{$searchTerm2}%")
            ->orWhere('descripcion', 'like', "%{$searchTerm2}%")
            ->orWhere('fecha_inicio', 'like', "%{$searchTerm2}%")
            ->orWhere('fecha_fin', 'like', "%{$searchTerm2}%");
    });
}

$cursos = $queryGratis->paginate($perPage2);

// Cursos de pago (tipo_pago: 'pago')
$queryPago = ModelCurso::select('cursos.*')
    ->withCount([
        'inscripciones as total_inscritos' => function ($q) {
            $q->where('estado', 'activo');
        }
    ])
    ->where('estado_curso', '!=', 'terminado')
    ->where('tipo_pago', 'pago')
    ->whereDoesntHave('inscripciones', function ($q) use ($user) {
        $q->where('id_user', $user->id)
            ->where('estado', 'activo');
    });

if ($searchTerm2) {
    $queryPago->where(function ($q) use ($searchTerm2) {
        $q->where('nombre', 'like', "%{$searchTerm2}%")
            ->orWhere('descripcion', 'like', "%{$searchTerm2}%")
            ->orWhere('fecha_inicio', 'like', "%{$searchTerm2}%")
            ->orWhere('fecha_fin', 'like', "%{$searchTerm2}%");
    });
}

$pagoCursos = $queryPago->paginate($perPage2);

        // Obtener los IDs de los cursos en los que el usuario ya está inscrito
        $misCursosIds = ModelInscripcion::where('id_user', $miid)
            ->where('estado', 'activo')
            ->pluck('id_curso')
            ->toArray();






        //--------------------------------------------
        return Inertia::render('Miscursos', [
            'miscursos' => $miscursos,
            'nombre_user' => $nombre_user,
            'cursos' => $cursos,
            'misCursosIds' => $misCursosIds, // Enviar los IDs al frontend
    'cursos_pago' => $pagoCursos, // 👈 ESTA LÍNEA ES CLAVE
        ]);
    }
    public function update($id)
    {
        $registro = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        $user = Auth::user();
        $miid = $user->id;

        $inscripcionExistente = ModelInscripcion::where('id_user', $miid)
            ->where('id_curso', $registro->id_curso)
            ->first(); // Usamos first() para obtener el primer registro
        if ($inscripcionExistente) {
            //dd($inscripcionExistente->id_inscripcion);
            $registro = ModelInscripcion::where('id_inscripcion', $inscripcionExistente->id_inscripcion)->firstOrFail();
            if ($registro) {
                $registro->update(['estado' => 'activo']);
                return back()->with('editado', 'ok');
            }
        } else {
            $mensaje = ["registro exitoso"];
            $registroguardar = ModelInscripcion::create([
                'id_user' => $miid,
                'id_curso' => $registro->id_curso,
                'uuid_inscripcion' => Str::uuid(),
                'fecha_inscripcion' => Carbon::now(),
                'estado_ins' => "inscrito"
            ]);
            // Lógica de guardado (ejemplo)
            return back()
                ->with('success', 'Usuario  inscrito')
                ->with('datos_array', [$mensaje]);
        }
    }

    /**
     * Actualiza el estado de una inscripción (activo, inactivo o eliminado) basado en el código proporcionado.
     * Busca la inscripción por su UUID y actualiza la columna 'estado' según el valor de '$cod'.
     *
     * @param  string  $id El UUID único de la inscripción que se va a actualizar.
     * @param  string  $cod El código que indica el nuevo estado de la inscripción ('1' para activo, '2' para inactivo, otro para eliminado).
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatedelete($id, $cod)
    {
        //dd($id, $cod);
        // Buscar la institución o fallar con 404
        // $institucion = ModelInstitucion::findOrFail($id);
        if ($id && $cod) {
            $institucion = ModelInscripcion::where('uuid_inscripcion', $id)->firstOrFail();
            // Actualizar el estado
            if ($cod == '1') {
                $institucion->update(['estado' => 'activo']);
                return back()->with('editado', 'ok');
            } elseif ($cod == '2') {
                $institucion->update(['estado' => 'inactivo']);
                return back()->with('editado', 'ok');
            } else {
                $institucion->update(['estado' => 'eliminado']);
                return back()->with('editado', 'ok');
            }
        } else {
            return back()->with('error', 'fue un error');
        }
    }
}

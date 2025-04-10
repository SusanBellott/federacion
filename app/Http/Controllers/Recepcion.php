<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User as ModelUser;
use App\Models\Inscripcion as ModelInscripcion;
use App\Models\Curso;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Institucion as ModelInstitucion;
use App\Http\Requests\Recepciones;
class Recepcion extends Controller
{
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
        $cursos = Curso::where('estado', '<>', 'eliminado')->get();
        return Inertia::render('Recepcion', [
            'usuarios' => $usuarios,
            'instituciones' => $instituciones,
            'cursos' => $cursos,
            'filters' => [
                'search' => $searchTerm,
                'perPage' => $perPage
            ]
        ]);
    }
    public function store(Recepciones $request)
    {
        $validated = $request->validated();
        $registroguardar = ModelUser::create([
            'id_institucion' => $request->id_institucion,
            'uuid_user' => Str::uuid(),
            'ci' => $request->ci,
            'rda' => $request->rda,
            'name' => $request->name . ' ' . $request->name2,
            'primer_apellido' => $request->primer_apellido,
            'segundo_apellido' => $request->segundo_apellido,
            'item' => $request->item,
            'cargo' => $request->cargo,
            'horas' => $request->horas,
            'email' => $request->email,
            'password' => bcrypt($request->ci)

        ])->assignRole('Estudiante');
        $registroId = $registroguardar->id;
        $registrouuId = $registroguardar->uuid_user;
        $registroguardar = ModelInscripcion::create([
            'id_user' => $registroId,
            'id_curso' => $request->id_curso,
            'uuid_inscripcion' => Str::uuid(),
            'fecha_inscripcion' => Carbon::now(),
            'estado_ins' => "inscrito"
        ]);
        return redirect()
            ->route('inscritos.index', [
                'perPage' => 10,
                'search' => (string) $registrouuId,

            ])
            ->with('success', 'Usuario inscrito correctamente')
            ->with('datos_array', [$validated]);
    }

}

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_institucion' => 'required',
            'id_curso' => 'required',
            'ci' => 'required',
            'name' => 'required',
            'primer_apellido' => 'required',
            'segundo_apellido' => 'required',
            'email' => 'required'
        ]);
        $registroguardar = ModelUser::create([
            'id_institucion' => $request->id_institucion,
            'uuid_user' => Str::uuid(),
            'ci' => $request->ci,
            'rda' => $request->rda,
            'name' => $request->name,
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
        // Lógica de guardado (ejemplo)
        /*  return back()
             ->with('success', 'Usuario  inscrito')
             ->with('datos_array', [$validated]); */
               //'page' => 1,
        return redirect()
            ->route('inscritos.index', [
                'perPage' => 10,
                'search' => (string) $registrouuId,

            ])
            ->with('success', 'Usuario inscrito correctamente')
            ->with('datos_array', [$validated]);
    }

    // App/Http/Controllers/InstitucionController.php
    public function updateinstitutucion(Request $request, $id)
    {
        $institucion = ModelInstitucion::where('uuid_institucion', $id)->firstOrFail();
        //dd($id, $request->id_distrito);
        $validated = $request->validate([
            'id_distrito' => 'required',
            'subsistema' => 'required',
            'servicio' => 'required',
            'servicio_generado' => 'required',
            'nivel' => 'required',
            'programa' => 'required',
            'unidad_educativa' => 'required'
        ]);
        $institucion->update($validated);
        return back()
            ->with('success', 'Institución editada correctamente')
            ->with('datos_array', [$validated]);
    }
    public function updatedelete($id, $cod)
    {
        //dd($id, $cod);
        // Buscar la institución o fallar con 404
        // $institucion = ModelInstitucion::findOrFail($id);
        if ($id && $cod) {
            $institucion = ModelInstitucion::where('uuid_institucion', $id)->firstOrFail();
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

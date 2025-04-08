<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Institucion as ModelInstitucion;
use App\Models\Distrito as ModelIDistrito;

use function Termwind\render;

class Institucion extends Controller
{
    public function index()
    {
        $searchTerm = request()->query('search');
        $perPage = request()->query('perPage', 10);

        $query = ModelInstitucion::query();

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id_distrito', 'like', "%{$searchTerm}%")
                    ->orWhere('subsistema', 'like', "%{$searchTerm}%")
                    ->orWhere('servicio', 'like', "%{$searchTerm}%")
                    ->orWhere('servicio_generado', 'like', "%{$searchTerm}%")
                    ->orWhere('nivel', 'like', "%{$searchTerm}%")
                    ->orWhere('programa', 'like', "%{$searchTerm}%")
                    ->orWhere('unidad_educativa', 'like', "%{$searchTerm}%");
            });
        }

        $instituciones = $query->paginate($perPage);
        $distritos = ModelIDistrito::where('estado', '<>', 'eliminado')->get();
        return Inertia::render('Instituciones', [
            'instituciones' => $instituciones,
            'distritos' => $distritos,
            'filters' => [
                'search' => $searchTerm,
                'perPage' => $perPage
            ]
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_distrito' => 'required',
            'subsistema' => 'required',
            'servicio' => 'required',
            'servicio_generado' => 'required',
            'nivel' => 'required',
            'programa' => 'required',
            'unidad_educativa' => 'required'
        ]);
        $institucion = ModelInstitucion::create([
            'id_distrito' => $request->id_distrito,
            'uuid_institucion' => Str::uuid(),
            'subsistema' => $request->subsistema,
            'servicio' => $request->servicio,
            'servicio_generado' => $request->servicio_generado,
            'nivel' => $request->nivel,
            'programa' => $request->programa,
            'unidad_educativa' => $request->unidad_educativa,
        ]);
        // Lógica de guardado (ejemplo)
        return back()
            ->with('success', 'Institución creada correctamente')
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

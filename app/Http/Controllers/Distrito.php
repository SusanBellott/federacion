<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Distrito as ModelIDistrito;
use Illuminate\Support\Str;
use App\Http\Requests\Distritos;

class Distrito extends Controller
{
    public function index()
    {
        $searchTerm = request()->query('search');
        $perPage = request()->query('perPage', 10);

        $query = ModelIDistrito::query();

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('uuid_distrito', 'like', "%{$searchTerm}%")
                    ->orWhere('codigo', 'like', "%{$searchTerm}%")
                    ->orWhere('descripcion', 'like', "%{$searchTerm}%")
                ;
            });
        }

        $distritos = $query->paginate($perPage);

        return Inertia::render('Distrito', [
            'distritos' => $distritos,
            'filters' => [
                'search' => $searchTerm,
                'perPage' => $perPage
            ]
        ]);
    }
    public function store(Distritos $request)
    {
        $validated = $request->validated();
        $distrito = ModelIDistrito::create([
            'uuid_distrito' => Str::uuid(),
            'codigo' => $request->codigo,
            'descripcion' => $request->descripcion,

        ]);
        // Lógica de guardado (ejemplo)
        return back()
            ->with('success', 'Distrito creada correctamente')
            ->with('datos_array', [$validated]);
    }

    public function update(Distritos $request, $id)
    {
        $institucion = ModelIDistrito::where('uuid_distrito', $id)->firstOrFail();
        $validated = $request->validated();
        $institucion->update($validated);
        return back()
            ->with('success', 'Distrito editada correctamente')
            ->with('datos_array', [$validated]);
    }
    public function updatedelete($id, $cod)
    {
        //dd($id, $cod);
        // Buscar la institución o fallar con 404
        // $institucion = ModelInstitucion::findOrFail($id);
        if ($id && $cod) {
            $institucion = ModelIDistrito::where('uuid_distrito', $id)->firstOrFail();
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

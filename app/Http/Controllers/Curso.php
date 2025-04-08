<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Distrito as ModelIDistrito;
use App\Models\Curso as ModelCurso;
use App\Models\Imagencertificado;

use Illuminate\Support\Facades\Storage;

class Curso extends Controller
{

    public function index()
    {
        $searchTerm = request()->query('search');
        $perPage = request()->query('perPage', 10);

        $query = ModelCurso::query();

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nombre', 'like', "%{$searchTerm}%")
                    ->orWhere('descripcion', 'like', "%{$searchTerm}%")
                    ->orWhere('fecha_inicio', 'like', "%{$searchTerm}%")
                    ->orWhere('fecha_fin', 'like', "%{$searchTerm}%")
                    ->orWhere('carga_horaria', 'like', "%{$searchTerm}%")
                    ->orWhere('encargado', 'like', "%{$searchTerm}%")
                    ->orWhere('grado_academico', 'like', "%{$searchTerm}%");
            });
        }
        $cursos = $query->with('imagencertificados')->paginate($perPage);
        return Inertia::render('Cursos', [
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
            'nombre' => 'required',
            'descripcion' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'carga_horaria' => 'required',
            'encargado' => 'required',
            'grado_academico' => 'required',
            'estado_curso' => 'required',
        ]);
        $data = $request->only(['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'carga_horaria', 'encargado', 'grado_academico', 'estado_curso']);
        $data['uuid_curso'] = Str::uuid();
        if ($request->hasFile('imagen')) {
            $image = $request->file('imagen');
            $uniqueName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('imagenescursos', $uniqueName, 'public');
            $data['img_curso'] = (string) 'storage/' . $imagePath;
        }
        $curso = ModelCurso::create($data);
        /*   Imagencertificado::create([
              'uuid_imgcer' => Str::uuid(),
              'id_curso' => $curso->id_curso,
          ]); */
        // Lógica de guardado (ejemplo)
        return back()
            ->with('success', 'Curso creada correctamente')
            ->with('datos_array', [$validated]);

    }
    public function storeimagen(Request $request)
    {
        $validated = $request->validate([
            'imagenes' => 'required',
        ]);
        $curso = ModelCurso::where('uuid_curso', (string) $request->uuid_curso)->firstOrFail();
        $imagenesData = [];
        //dd($curso->id_curso);
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $image) {
                // Validar que sea una imagen
                if (!$image->isValid()) {
                    continue; // Saltar imágenes inválidas
                }
                // Generar nombre único
                $uniqueName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('imagenescertificados', $uniqueName, 'public');
                // Crear registro de imagen
                $imagen = Imagencertificado::create([
                    'uuid_imgcer' => Str::uuid(),
                    'id_curso' => $curso->id_curso,
                    'imagenescer' => (string) 'storage/' . $imagePath,
                    'descripcion' => $uniqueName,
                ]);

                $imagenesData[] = [
                    'nombre' => $imagen->descripcion,
                ];
            }
        }
        return back()
            ->with('success', 'Imágenes agregadas correctamente')
            ->with('datos_array', $imagenesData);

    }
    // App/Http/Controllers/InstitucionController.php
    public function update(Request $request, $id)
    {
        $institucion = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        $validated = $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin' => 'required',
            'carga_horaria' => 'required',
            'encargado' => 'required',
            'grado_academico' => 'required',
            'estado_curso' => 'required',
        ]);
        if ($request->hasFile('imagen')) {
            if ($institucion->img_curso) {
                $oldImagePath = str_replace('storage/', '', $institucion->img_curso);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            $image = $request->file('imagen');
            $uniqueName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('imagenescursos', $uniqueName, 'public');
            $validated['img_curso'] = (string) 'storage/' . $imagePath;
        }
        $institucion->update($validated);
        return back()
            ->with('success', 'Curso editada correctamente')
            ->with('datos_array', [$validated]);
    }
    public function updatedelete($id, $cod)
    {
        //dd($id, $cod);
        // Buscar la institución o fallar con 404
        // $institucion = ModelInstitucion::findOrFail($id);
        if ($id && $cod) {
            $registro = ModelCurso::where('uuid_curso', $id)->firstOrFail();
            // Actualizar el estado
            if ($cod == '1') {
                $registro->update(['estado' => 'activo']);
                return back()->with('editado', 'ok');
            } elseif ($cod == '2') {
                $registro->update(['estado' => 'inactivo']);
                return back()->with('editado', 'ok');
            } else {
                $registro->update(['estado' => 'eliminado']);
                return back()->with('editado', 'ok');
            }
        } else {
            return back()->with('error', 'fue un error');
        }
    }
    public function updateimagen(Request $request, $id)
    {
        $mensaje = ["registro exitoso"];
        dd($request->uuid_curso);
        dd($request->all());
        dd($request->hasFile('imagenes'));
        // Lógica para actualizar la imagen
        // Puedes acceder a los datos con $request->file('imagen') o $request->input('campo')
        return back()
        ->with('success', 'Imágenes agregadas correctamente')
        ->with('datos_array', $mensaje);
    }
    public function updateimagen222(Request $request, $id)
    {
        dd($id);
        $curso = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        $imagenesData = [];

        /* if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $image) {
                // Validar que sea una imagen
                if (!$image->isValid()) {
                    continue; // Saltar imágenes inválidas
                }
                // Generar nombre único
                $uniqueName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('imagenescertificados', $uniqueName, 'public');
                // Crear registro de imagen
                $imagen = new Imagencertificado(); // Usando tu modelo existente
                $imagen->uuid_imgcer = Str::uuid();
                $imagen->id_curso = $curso->id;
                $imagen->imagenescer = (string) 'storage/' . $imagePath;
                $imagen->descripcion = $uniqueName;
                $imagen->save();
                $imagenesData[] = [
                    'nombre' => $imagen->descripcion
                ];
            }
        }
         */
        return back()
            ->with('success', 'Imágenes agregadas correctamente')
            ->with('datos_array', $imagenesData);
    }
}

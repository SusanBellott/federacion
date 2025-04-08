<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Imagencertificado;
use App\Models\Curso as ModelCurso;

class Certificado extends Controller
{
    public function index($uuid_curso, Request $request)
    {

        $from = request()->query('from');
        $decodedUrl = base64_decode($from);
        $curso = ModelCurso::where('uuid_curso', (string) $uuid_curso)
            ->where('estado', 'activo')
            ->firstOrFail()
            ->makeHidden(['id_curso']);

        $imagenes = [];
        $imagenes2 = [];
        if ($curso) {
                $imagenes2 = Imagencertificado::where('estado', '<>', 'eliminado')
                ->where('id_curso', $curso->id_curso) // Compara con el valor de uuid_curso
                ->get();
                $imagenes = Imagencertificado::where('estado', 'activo')
                ->where('id_curso', $curso->id_curso) // Compara con el valor de id_curso
                ->get();
                

        }

        return Inertia::render('Certificado', [
            'uuid_curso' => $uuid_curso,
            'rutaanterior' => $decodedUrl,
            'imagenes' => $imagenes,
            'imagenes2' => $imagenes2,
            'cursos' => $curso,
        ]);
    }
    public function update(Request $request, $id)
    {
        //dd($request->titulocer);
        $cursos = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        $data = $request->only(['titulocer','contenidocer','estado']); 
            $cursos->update($data);
        return back()
            ->with('editado', 'Distrito editada correctamente')
            ->with('datos_array', [$data]);
    }
    public function update2(Request $request, $id)
    {
        //dd($id,$request->img_firma1,$request->img_firma2,$request->img_firma3,$request->img_firma4);
        $cursos = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        $data = $request->only(['img_cer','img_cerlogo','img_firma1','img_firma2','img_firma3','img_firma4','img_sello','estado']); 
        $cursos->update($data);
        $imagenes = Imagencertificado::where('uuid_imgcer', $request->uuid_imgcer)->firstOrFail();
        $imagenes->update(['estado' => 'inactivo',]);
        return back()
            ->with('editado', 'Distrito editada correctamente')
            ->with('datos_array', [$data]);
    }
    public function update3(Request $request, $id)
    {
       // dd($id,$request->uuid_imgcer);
        $cursos = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        $data = $request->only(['img_cer','img_cerlogo','img_firma1','img_firma2','img_firma3','img_firma4','img_sello','estado']); 
        $cursos->update($data);
        $imagenes = Imagencertificado::where('uuid_imgcer', $request->uuid_imgcer)->firstOrFail();
        $imagenes->update(['estado' => 'activo',]);
        return back()
            ->with('editado', 'Distrito editada correctamente')
            ->with('datos_array', [$data]);
    }
    
}

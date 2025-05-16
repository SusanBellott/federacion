<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Distrito as ModelIDistrito;
use App\Models\Curso as ModelCurso;
use App\Models\Imagencertificado;
use App\Models\TipoActividad; 
use Carbon\Carbon;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Cursos;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;

class Curso extends Controller
{
    function __construct()
    {
         $this->middleware('permission:cursos.index', ['only' => ['index']]);
         $this->middleware('permission:cursos.create', ['only' => ['store']]);
         $this->middleware('permission:editarestadodeletecursos.update', ['only' => ['updatedelete']]);
         $this->middleware('permission:cursoseditar.update', ['only' => ['update']]);
         $this->middleware('permission:cursosimagenes.create', ['only' => ['storeimagen']]);
    }
    /**
     * Muestra una lista paginada de cursos, con opción de búsqueda y filtrado por página.
     * También actualiza el estado de los cursos activos según su fecha de finalización.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $searchTerm = request()->query('search');
        $perPage = (int)$request->input('perPage', 10);
        $allowedPerPage = [10, 12, 24, 48];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $query = ModelCurso::query();

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nombre', 'like', "%{$searchTerm}%")
                ->orWhere('codigo_curso', 'like', "%{$searchTerm}%")
                    ->orWhere('descripcion', 'like', "%{$searchTerm}%")
                    ->orWhere('fecha_inicio', 'like', "%{$searchTerm}%")
                    ->orWhere('fecha_fin', 'like', "%{$searchTerm}%")
                    ->orWhere('carga_horaria', 'like', "%{$searchTerm}%")
                    ->orWhere('encargado', 'like', "%{$searchTerm}%")
                    ->orWhere('grado_academico', 'like', "%{$searchTerm}%")
                    ->orWhereHas('tipoActividad', function ($qt) use ($searchTerm) {
                        $qt->where('nombre', 'like', "%{$searchTerm}%")
                           ->orWhere('codigo', 'like', "%{$searchTerm}%");
           
                        });
                    });
        }

        
        $cursos = $query->with([
            'tipoActividad:id,codigo,nombre',
            'imagencertificados'
        ])->paginate($perPage);

        
        
        
        $uuidEnEdicion = $request->query('edit');
        $cursoEditando = null;
    
        if ($uuidEnEdicion) {
            $cursoEditando = ModelCurso::with('tipoActividad')
                ->where('uuid_curso', $uuidEnEdicion)
                ->first();
        }
    
        $tipos = TipoActividad::orderBy('codigo')->get();



        return Inertia::render('Cursos', [
            'cursos' => $cursos,
            'filters' => [
                'search' => $searchTerm,
                'perPage' => $perPage,
            ],
            'tipos' => $tipos,
            'cursoEditando' => $cursoEditando,
        ]);
    }

    /**
     * Almacena un nuevo curso en la base de datos.
     * Valida los datos de la petición, genera un UUID y guarda la imagen del curso si se proporciona.
     *
     * @param  \Illuminate\Http\Request  $request El objeto de la petición HTTP que contiene los datos del curso.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Cursos $request)
    {
        $validated = $request->validate([
           'tipo_actividad_id' => [
    'required',
    'exists:tipos_actividad,id',
    Rule::unique('cursos', 'tipo_actividad_id'),
],

            'nombre' => 'required',
            'descripcion' => 'required',
            'fecha_inicio_inscripcion' => 'required|date|after_or_equal:today',
'fecha_fin_inscripcion' => 'required|date|after_or_equal:fecha_inicio_inscripcion|before_or_equal:2040-12-31',
'fecha_inicio' => 'required|date|after_or_equal:fecha_fin_inscripcion',
'fecha_fin' => 'required|date|after_or_equal:fecha_inicio|before_or_equal:2040-12-31',
           'carga_horaria' => 'required|integer|min:1|max:1000',
        ]);
        $data = $request->only(['tipo_actividad_id','nombre', 'descripcion','fecha_inicio_inscripcion', 'fecha_fin_inscripcion', 'fecha_inicio', 'fecha_fin', 'carga_horaria' ]);
        $data['uuid_curso'] = Str::uuid();
        $data['estado_curso'] = 'abierto';
        if ($request->hasFile('imagen')) {
            $image = $request->file('imagen');
            $uniqueName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('imagenescursos', $uniqueName, 'public');
            $data['img_curso'] = 'storage/' . $imagePath;
        }
        $data['codigo_curso'] = $this->generarCodigoCurso($request->nombre);

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


    private function generarCodigoCurso($nombre)
    {
        $excluir = ['de', 'la', 'el', 'y', 'en', 'del', 'los', 'las', 'para', 'con', 'segun', 'su'];
    
        $palabras = collect(explode(' ', strtolower($nombre)))
            ->filter(fn($p) => !in_array($p, $excluir) && trim($p) !== '');
    
        $letras = '';
        foreach ($palabras as $palabra) {
            if (strlen($letras) < 3) {
                $letras .= strtoupper(mb_substr($palabra, 0, 1));
            }
        }
    
        // Si hay menos de 3 letras, completa con letras adicionales de la primera palabra significativa
        if (strlen($letras) < 3 && $palabras->isNotEmpty()) {
            $restante = 3 - strlen($letras);
            $letras .= strtoupper(mb_substr($palabras->first(), 1, $restante));
        }
    
        // En caso extremo, usa 'XXX'
        $codigoBase = str_pad($letras, 3, 'X');
    
        // Verifica si ya existe este mismo curso con mismo nombre
        $yaExiste = ModelCurso::where('nombre', $nombre)
            ->where('codigo_curso', 'like', "$codigoBase-V%")
            ->first();
    
        if ($yaExiste) {
            return $yaExiste->codigo_curso;
        }
    
        // Si hay más cursos con este código base, aumenta versión
        $version = ModelCurso::where('codigo_curso', 'like', "$codigoBase-V%")->count() + 1;
        return "$codigoBase-V$version";
    }
    

    /**
     * Almacena múltiples imágenes de certificado asociadas a un curso específico.
     * Valida que se hayan enviado imágenes, las guarda en el almacenamiento y crea registros en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request El objeto de la petición HTTP que contiene las imágenes y el UUID del curso.
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Actualiza la información de un curso existente, incluyendo la imagen si se proporciona.
     * Valida los datos de la petición, actualiza la imagen si es necesario,
     * actualiza el estado del curso según la fecha de finalización y guarda los cambios.
     *
     * @param  \Illuminate\Http\Request  $request El objeto de la petición HTTP con los datos actualizados del curso.
     * @param  string  $id El UUID único del curso que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Cursos $request, $id)
    {
        $curso = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        $validated = $request->validate([
            'tipo_actividad_id' => [
                'required',
                'exists:tipos_actividad,id',
                Rule::unique('cursos', 'tipo_actividad_id')->ignore($curso->id_curso, 'id_curso'),
            ],
            'nombre' => 'required',
            'descripcion' => 'required',
            'fecha_inicio_inscripcion' => 'required|date|after_or_equal:today',
'fecha_fin_inscripcion' => 'required|date|after_or_equal:fecha_inicio_inscripcion|before_or_equal:2040-12-31',
'fecha_inicio' => 'required|date|after_or_equal:fecha_fin_inscripcion',
'fecha_fin' => 'required|date|after_or_equal:fecha_inicio|before_or_equal:2040-12-31',
      'carga_horaria' => 'required|integer|min:1|max:1000',
        ]);
        if ($request->hasFile('imagen')) {
            if ($curso->img_curso) {
                $oldImagePath = str_replace('storage/', '', $curso->img_curso);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }
            $image = $request->file('imagen');
            $uniqueName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('imagenescursos', $uniqueName, 'public');
            $validated['img_curso'] = 'storage/' . $imagePath;
        }
        
        $fecha_actual = Carbon::now();
        $inicio_insc = $request->fecha_inicio_inscripcion ? Carbon::parse($request->fecha_inicio_inscripcion) : null;
        $fin_insc    = $request->fecha_fin_inscripcion ? Carbon::parse($request->fecha_fin_inscripcion) : null;
        $inicio_curso = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio) : null;
        $fin_curso    = $request->fecha_fin ? Carbon::parse($request->fecha_fin) : null;
        
        if ($inicio_insc && $fecha_actual->lt($inicio_insc)) {
            $validated['estado_curso'] = 'no iniciado';
        } elseif ($inicio_insc && $fin_insc && $fecha_actual->between($inicio_insc, $fin_insc)) {
            $validated['estado_curso'] = 'abierto';
        } elseif ($inicio_curso && $fin_curso && $fecha_actual->between($inicio_curso, $fin_curso)) {
            $validated['estado_curso'] = 'curso';
        } elseif ($fin_curso && $fecha_actual->gt($fin_curso)) {
            $validated['estado_curso'] = 'terminado';
        } else {
            $validated['estado_curso'] = 'cerrado';
        }
        

        if ($curso->nombre !== $request->nombre) {
            $validated['codigo_curso'] = $this->generarCodigoCurso($request->nombre);
        }
        $curso->update($validated);

        return back()->with([
            'success' => 'Curso editado correctamente',
            'datos_array' => $validated,
        ]);


    }

    /**
     * Actualiza el estado de un curso (activo, inactivo o eliminado) basado en el código proporcionado.
     * Busca el curso por su UUID y actualiza la columna 'estado' según el valor de '$cod'.
     *
     * @param  string  $id El UUID único del curso que se va a actualizar.
     * @param  string  $cod El código que indica el nuevo estado del curso ('1' para activo, '2' para inactivo, otro para eliminado).
     * @return \Illuminate\Http\RedirectResponse
     */
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

     /**
     * Actualiza el estado del curso ('abierto', 'curso' o 'terminado') basado en el código proporcionado.
     * Busca el curso por su UUID y actualiza la columna 'estado_curso' según el valor de '$cod'.
     *
     * @param  string  $id El UUID único del curso que se va a actualizar.
     * @param  string  $cod El código que indica el nuevo estado del curso ('1' para abierto, '2' para en curso, otro para terminado).
     * @return \Illuminate\Http\RedirectResponse
     */
    public function estadoupdate($id, $cod)
    {
        if ($id && $cod) {
            $registro = ModelCurso::where('uuid_curso', $id)->firstOrFail();
            // Actualizar el estado
            if ($cod == '1') {
                $registro->update(['estado_curso' => 'abierto']);
                return back()->with('editado', 'ok');
            } elseif ($cod == '2') {
                $registro->update(['estado_curso' => 'curso']);
                return back()->with('editado', 'ok');
            } else {
                $registro->update(['estado_curso' => 'terminado']);
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
        return back()
        ->with('success', 'Imágenes agregadas correctamente')
        ->with('datos_array', $mensaje);
    }
    public function updateimagen222(Request $request, $id)
    {
        dd($id);
        $curso = ModelCurso::where('uuid_curso', $id)->firstOrFail();
        $imagenesData = [];
        return back()
            ->with('success', 'Imágenes agregadas correctamente')
            ->with('datos_array', $imagenesData);
    }

    public function misCursos()
    {
        $userId = Auth::id();
        $fechaActual = Carbon::now();
    
        $cursosDisponibles = ModelCurso::inscripcionesDisponibles()
            ->where('estado', 'activo')
            ->with(['tipoActividad', 'imagencertificados'])
            ->orderBy('fecha_inicio_inscripcion')
            ->get()
            ->map(function ($curso) use ($userId, $fechaActual) {
                // Verificar si el usuario está inscrito activamente (no eliminado/desinscrito)
                $yaInscrito = \App\Models\Inscripcion::where('id_user', $userId)
                    ->where('id_curso', $curso->id_curso)
                    ->where('estado_ins', 'inscrito') // o el valor que usas para "activo"
                    ->exists();
    
                $curso->ya_inscrito = $yaInscrito;
    
                return $curso;
            });
    
        $miscursos = \App\Models\Inscripcion::with('curso')
            ->where('id_user', $userId)
            ->paginate(10);
    
        return Inertia::render('Miscursos', [
            'nombre_user' => Auth::user()->name,
            'cursos' => $cursosDisponibles,
            'miscursos' => $miscursos,
        ]);
    }
    
    
}

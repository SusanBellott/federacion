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

        $cursos->getCollection()->transform(function ($curso) {
            $fecha_actual = Carbon::now();
            $inicio_insc = $curso->fecha_inicio_inscripcion ? Carbon::parse($curso->fecha_inicio_inscripcion) : null;
            $fin_insc = $curso->fecha_fin_inscripcion ? Carbon::parse($curso->fecha_fin_inscripcion)->endOfDay() : null;
            $inicio_curso = $curso->fecha_inicio ? Carbon::parse($curso->fecha_inicio) : null;
            $fin_curso = $curso->fecha_fin ? Carbon::parse($curso->fecha_fin)->endOfDay() : null;

            if ($inicio_insc && $fecha_actual->lt($inicio_insc)) {
                $curso->estado_curso = 'no iniciado';
            } elseif ($inicio_insc && $fin_insc && $fecha_actual->between($inicio_insc, $fin_insc)) {
                $curso->estado_curso = 'abierto';
            } elseif ($fin_insc && $inicio_curso && $fecha_actual->gt($fin_insc) && $fecha_actual->lt($inicio_curso)) {
                $curso->estado_curso = 'cerrado'; // ✅ inscripciones cerradas, aún no empezó
            } elseif ($inicio_curso && $fin_curso && $fecha_actual->between($inicio_curso, $fin_curso)) {
                $curso->estado_curso = 'curso';
            } elseif ($fin_curso && $fecha_actual->gt($fin_curso)) {
                $curso->estado_curso = 'terminado';
            } else {
                $curso->estado_curso = 'sin estado';
            }

            return $curso;
        });




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
            'tipo_pago' => 'required|in:gratuito,pago',
            'precio' => 'nullable|required_if:tipo_pago,pago|numeric|min:10',

        ]);
        $data = $request->only(['tipo_actividad_id', 'nombre', 'descripcion', 'fecha_inicio_inscripcion', 'fecha_fin_inscripcion', 'fecha_inicio', 'fecha_fin', 'carga_horaria', 'tipo_pago', 'precio']);
        $data['uuid_curso'] = Str::uuid();
        $data['estado_curso'] = 'abierto';
        if ($request->hasFile('imagen')) {
            $image = $request->file('imagen');
            $uniqueName = time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('imagenescursos', $uniqueName, 'public');
            $data['img_curso'] = 'storage/' . $imagePath;
        }
        $data['codigo_curso'] = $this->generarCodigoCurso($request->nombre);
        $data['tipo_pago'] = $request->tipo_pago;
        $data['precio'] = $request->tipo_pago === 'pago' ? $request->precio : null;

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
            ->filter(fn($p) => !in_array($p, $excluir) && trim($p) !== '')
            ->values();

        $letras = '';

        // Paso 1: Tomar la primera letra de hasta 3 palabras
        foreach ($palabras as $palabra) {
            if (strlen($letras) < 3) {
                $letras .= strtoupper(mb_substr($palabra, 0, 1));
            }
        }

        // Paso 2: Si no alcanza 3 letras, completar con más letras de la primera palabra
        if (strlen($letras) < 3 && isset($palabras[0])) {
            $restante = 3 - strlen($letras);
            $letras .= strtoupper(mb_substr($palabras[0], 1, $restante));
        }

        // Paso 3: Si aún no llega a 3 letras, completar con 'X'
        $codigoBase = str_pad($letras, 3, 'X');

        // Buscar la última versión
        $ultimaVersion = ModelCurso::where('codigo_curso', 'like', "$codigoBase-V%")
            ->get()
            ->map(function ($curso) {
                $partes = explode('-V', $curso->codigo_curso);
                return isset($partes[1]) && is_numeric($partes[1]) ? (int)$partes[1] : 0;
            })
            ->max();

        $siguienteVersion = $ultimaVersion ? $ultimaVersion + 1 : 1;

        return "$codigoBase-V$siguienteVersion";
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

        $rules = [
            'tipo_actividad_id' => [
                'required',
                'exists:tipos_actividad,id',
                Rule::unique('cursos', 'tipo_actividad_id')->ignore($curso->id_curso, 'id_curso'),
            ],
            'nombre' => 'required',
            'descripcion' => 'required',
            'carga_horaria' => 'required|integer|min:1|max:1000',
            'tipo_pago' => 'required|in:gratuito,pago',

        ];
        // 👇 Solo agregamos esta validación si es curso de pago
        if ($request->input('tipo_pago') === 'pago') {
            $rules['precio'] = 'required|numeric|min:10';
        }
        // Si se está enviando una nueva fecha, se valida; si no, se conserva la anterior
        if ($request->filled('fecha_inicio_inscripcion')) {
            $rules['fecha_inicio_inscripcion'] = 'required|date|before_or_equal:fecha_fin_inscripcion';
        }

        if ($request->filled('fecha_fin_inscripcion')) {
            $rules['fecha_fin_inscripcion'] = 'required|date|after_or_equal:fecha_inicio_inscripcion|before_or_equal:2040-12-31';
        }

        if ($request->filled('fecha_inicio')) {
            $rules['fecha_inicio'] = 'required|date|after_or_equal:fecha_fin_inscripcion';
        }

        if ($request->filled('fecha_fin')) {
            $rules['fecha_fin'] = 'required|date|after_or_equal:fecha_inicio|before_or_equal:2040-12-31';
        }

        $validated = $request->validate($rules);

        // Si alguna fecha no vino en la request, conservar la actual
        $validated['fecha_inicio_inscripcion'] = $request->fecha_inicio_inscripcion ?? $curso->fecha_inicio_inscripcion;
        $validated['fecha_fin_inscripcion'] = $request->fecha_fin_inscripcion ?? $curso->fecha_fin_inscripcion;
        $validated['fecha_inicio'] = $request->fecha_inicio ?? $curso->fecha_inicio;
        $validated['fecha_fin'] = $request->fecha_fin ?? $curso->fecha_fin;

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

        // Estado dinámico
        $fecha_actual = Carbon::now();
        $inicio_insc = Carbon::parse($validated['fecha_inicio_inscripcion']);
        $fin_insc = Carbon::parse($validated['fecha_fin_inscripcion'])->endOfDay();
        $inicio_curso = Carbon::parse($validated['fecha_inicio']);
        $fin_curso = Carbon::parse($validated['fecha_fin'])->endOfDay();

        if ($inicio_insc && $fecha_actual->lt($inicio_insc)) {
            $validated['estado_curso'] = 'no iniciado';
        } elseif ($inicio_insc && $fin_insc && $fecha_actual->between($inicio_insc, $fin_insc)) {
            $validated['estado_curso'] = 'abierto';
        } elseif ($fin_insc && $inicio_curso && $fecha_actual->gt($fin_insc) && $fecha_actual->lt($inicio_curso)) {
            $validated['estado_curso'] = 'cerrado';
        } elseif ($inicio_curso && $fin_curso && $fecha_actual->between($inicio_curso, $fin_curso)) {
            $validated['estado_curso'] = 'curso';
        } elseif ($fin_curso && $fecha_actual->gt($fin_curso)) {
            $validated['estado_curso'] = 'terminado';
        } else {
            $validated['estado_curso'] = 'sin estado';
        }

        // ✅ Asegurar coherencia en tipo_pago y precio
        $validated['tipo_pago'] = $request->tipo_pago;
        $validated['precio'] = $request->tipo_pago === 'pago' ? $request->precio : null;


        // Siempre verifica si ya existe otro curso con el mismo nombre y tipo_actividad_id
        $existeOtro = ModelCurso::where('nombre', $request->nombre)
            ->where('id_curso', '!=', $curso->id_curso)
            ->exists();

        if ($request->nombre !== $curso->nombre) {
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
        $user = Auth::user();
        $userId = $user->id;
        $fechaActual = Carbon::now();

        // 🔐 Aplicar condición de visibilidad según rol
        $cursosQuery = ModelCurso::inscripcionesDisponibles()
            ->where('estado', 'activo');
        if ($user->hasRole('Estudiante')) {
            $cursosQuery->where('tipo_pago', 'gratuito');
        } elseif ($user->hasAnyRole(['Administrador', 'Encargado'])) {
            $cursosQuery->whereIn('tipo_pago', ['gratuito', 'pago']);
        }




        $cursosDisponibles = $cursosQuery

            ->with(['tipoActividad', 'imagencertificados'])
            ->orderBy('fecha_inicio_inscripcion')
            ->get()
            ->map(function ($curso) use ($userId, $fechaActual) {
                // Verificar si el usuario está inscrito activamente
                $yaInscrito = Inscripcion::where('id_user', $userId)
                    ->where('id_curso', $curso->id_curso)
                    ->where('estado_ins', 'inscrito')
                    ->exists();

                $curso->ya_inscrito = $yaInscrito;

                return $curso;
            });

        $miscursos = Inscripcion::with('curso')
            ->where('id_user', $userId)
            ->paginate(10);

        // 🔥 Esta es la línea clave que te falta:
        $misCursosIds = Inscripcion::where('id_user', $userId)
            ->where('estado_ins', 'inscrito')
            ->pluck('id_curso')
            ->toArray();

        return Inertia::render('Miscursos', [
            'nombre_user' => Auth::user()->name,
            'cursos' => $cursosDisponibles,
            'miscursos' => $miscursos,
            'misCursosIds' => $misCursosIds, // ✅ pásalo al frontend
        ]);
    }
}

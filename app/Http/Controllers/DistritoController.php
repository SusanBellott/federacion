<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Distrito;
use App\Models\Institucion;
use App\Models\CodigoSie;
use App\Http\Requests\DistritoRequest;

class DistritoController extends Controller
{
    /**
     * Constructor que aplica middleware de permisos a las rutas del controlador.
     */
    public function __construct()
    {
        $this->middleware('permission:distritos.index', ['only' => ['index']]);
        $this->middleware('permission:distritos.create', ['only' => ['store']]);
        $this->middleware('permission:editarestadodeletedistrito.update', ['only' => ['updateStatus']]);
        $this->middleware('permission:distritoseditar.update', ['only' => ['update']]);
    }
    
    /**
     * Muestra una lista paginada de distritos con opción de búsqueda.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $searchTerm = $request->query('search');
        $perPage = (int)$request->input('perPage', 10);
        $allowedPerPage = [10, 12, 24, 48];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        $query = Distrito::query();

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('uuid_distrito', 'like', "%{$searchTerm}%")
                    ->orWhere('codigo', 'like', "%{$searchTerm}%")
                    ->orWhere('descripcion', 'like', "%{$searchTerm}%");
            });
        }

        return Inertia::render('Distrito', [
            'distritos' => $query->paginate($perPage),
            'filters' => [
                'search' => $searchTerm,
                'perPage' => $perPage
            ]
        ]);
    }

    /**
     * Almacena un nuevo distrito en la base de datos.
     *
     * @param DistritoRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DistritoRequest $request)
    {
        $validated = $request->validated();
        $validated['uuid_distrito'] = Str::uuid();
        
        Distrito::create($validated);

        return back()
            ->with('success', 'Distrito creado correctamente')
            ->with('datos_array', [$validated]);
    }

    /**
     * Actualiza la información de un distrito existente.
     *
     * @param DistritoRequest $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DistritoRequest $request, $uuid)
    {
        $distrito = Distrito::where('uuid_distrito', $uuid)->firstOrFail();
        $validated = $request->validated();
        
        $distrito->update($validated);
        
        return redirect()->route('distritos.index')->with([
            'success' => true,
            'datos_array' => [
                'title' => '¡Actualizado!',
                'text' => 'El distrito fue editado correctamente.',
                'icon' => 'success',
            ]
        ]);
        
    }

    /**
     * Actualiza el estado de un distrito (activo, inactivo o eliminado).
     *
     * @param string $uuid
     * @param string $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($uuid, $code)
    {
        if (!$uuid || !$code) {
            return back()->with('error', 'Hubo un error con la solicitud.');
        }

        $distrito = Distrito::where('uuid_distrito', $uuid)->firstOrFail();
        
        $newStatus = match($code) {
            '1' => 'activo',
            '2' => 'inactivo',
            default => 'eliminado',
        };

        $distrito->update(['estado' => $newStatus]);
        
        return back()->with('editado', 'ok');
    }

    /**
     * Obtiene las instituciones asociadas a un distrito.
     *
     * @param int $distrito_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function instituciones($distrito_id)
    {
        $distrito = Distrito::findOrFail($distrito_id);
        return response()->json([
            'instituciones' => $distrito->instituciones()->get(['id_institucion', 'nivel'])
        ]);
    }

    /**
     * Obtiene los códigos SIE asociados a un distrito.
     *
     * @param int $distrito_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function codigosSie($distrito_id)
    {
        $distrito = Distrito::findOrFail($distrito_id);
        return response()->json([
            'codigos_sie' => $distrito->codigosSie()->get(['id_codigo_sie', 'unidad_educativa'])
        ]);
    }
    
    /**
     * Obtiene todos los datos relacionados con un distrito (instituciones y códigos SIE).
     * 
     * @param int $distritoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function datosRelacionados($id)
    {
        return response()->json([
            'codigos_sie' => CodigoSie::where('distrito_id', $id)  // ✅ corregido aquí
                ->where('estado', 'activo')
                ->get(['id_codigo_sie', 'unidad_educativa']),
            
            'instituciones' => Institucion::where('id_distrito', $id)
                ->where('estado', 'activo')
                ->get(['id_institucion', 'nivel']),
        ]);
    }
    
}
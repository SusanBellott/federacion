<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CodigoSie;
use App\Models\Distrito;
use Illuminate\Support\Str;
use App\Http\Requests\CodigoSieRequest;
use App\Http\Requests\CodigoSieUpdateRequest;
use Illuminate\Support\Facades\Log;
class CodigoSieController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:codigosie.index', ['only' => ['index']]);
        $this->middleware('permission:codigosie.create', ['only' => ['store']]);
        $this->middleware('permission:codigosie.estado.update', ['only' => ['updateStatus']]);
        $this->middleware('permission:codigosie.update', ['only' => ['update']]);
    }
    
    public function index(Request $request)
    {
        $searchTerm = $request->query('search');
        $perPage = (int)$request->input('perPage', 10);
        $allowedPerPage = [10, 12, 24, 48];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
      $query = CodigoSie::with(['distrito', 'institucion']);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('programa', 'like', "%{$searchTerm}%")
                  ->orWhere('unidad_educativa', 'like', "%{$searchTerm}%")
                  ->orWhereHas('distrito', function ($d) use ($searchTerm) {
                      $d->where('descripcion', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('institucion', function ($i) use ($searchTerm) {
    $i->where('nivel', 'like', "%{$searchTerm}%");
});

            });
        }
        
        return Inertia::render('CodigoSie', [
            'codigosie' => $query->paginate($perPage),
            'distritos' => Distrito::select('id_distrito', 'descripcion')->get(),
            'instituciones' => \App\Models\Institucion::select('id_institucion', 'id_distrito', 'nivel')->get(),
            'filters' => ['search' => $searchTerm, 'perPage' => $perPage],
        ]);

    }
    
   

    public function store(CodigoSieRequest $request)
    {
        $validated = $request->validated();
        $validated['uuid_codigo_sie'] = Str::uuid(); // Asegúrate de generar UUID si no viene del frontend
    
        CodigoSie::create($validated);
     // Obtener los datos reales para mostrar en flash (si existen)
    $distrito = Distrito::find($validated['distrito_id'] ?? null);
    $institucion = \App\Models\Institucion::find($validated['institucion_id'] ?? null);

       return back()
    ->with('success', 'Código SIE creado correctamente')
    ->with('datos_array', [
        'Distrito' => optional($request->distrito)->descripcion,
        'Institución' => optional($request->institucion)->nivel
    ]);


    }
    
    /**
     * Update the specified codigo sie.
     */
    public function update(CodigoSieRequest $request, $codigo_sie)
    {
        $codigo = CodigoSie::where('uuid_codigo_sie', $codigo_sie)->firstOrFail();

        $validated = $request->validated();
        
        $codigo->update($validated);
        return back()->with([
            'success' => true,
            'datos_array' => [
                'title' => '¡Actualizado!',
                'text' => 'El código SIE fue editado correctamente.',
                'icon' => 'success',
            ]
        ]);
        
    }
    
    
    public function updateStatus($uuid, $code)
    {
        if (!$uuid || !$code) {
            return back()->with('error', 'Hubo un error con la solicitud.');
        }
   
        // Buscar el código correctamente
        $codigo = CodigoSie::where('uuid_codigo_sie', $uuid)->firstOrFail();
        $newStatus = match($code) {
            '1' => 'activo',
            '2' => 'inactivo',
            default => 'eliminado',
        };
   
        $codigo->update(['estado' => $newStatus]);
   
        return back()->with('editado', 'ok');
    }
    public function getByDistrito($distritoId)
{
    return response()->json(
        CodigoSie::where('distrito_id', $distritoId)
            ->get(['id_codigo_sie', 'unidad_educativa'])
    );
}
public function getByInstitucion($institucionId)
{
    return response()->json(
        CodigoSie::where('institucion_id', $institucionId)
            ->where('estado', 'activo')
            ->get(['id_codigo_sie', 'unidad_educativa'])
    );
}

}
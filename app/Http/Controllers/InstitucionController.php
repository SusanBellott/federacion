<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Models\Institucion;
use App\Models\Distrito;
use App\Models\CodigoSie;
use App\Http\Requests\InstitucionRequest;

class InstitucionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:instituciones.index', ['only' => ['index']]);
        $this->middleware('permission:instituciones.create', ['only' => ['store']]);
        $this->middleware('permission:editarestadodelete.update', ['only' => ['updateStatus']]);
        $this->middleware('permission:institucioneseditar.update', ['only' => ['update']]);
    }

    public function index(Request $request)
    {
        $searchTerm = $request->query('search');
        $perPage = (int)$request->input('perPage', 10);
        $allowedPerPage = [10, 12, 24, 48];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        // Solo usamos las relaciones válidas
        $query = Institucion::with(['distrito']);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('subsistema', 'like', "%{$searchTerm}%")
                    ->orWhere('nivel', 'like', "%{$searchTerm}%")
                    ->orWhere('servicio', 'like', "%{$searchTerm}%")
                    ->orWhere('servicio_generado', 'like', "%{$searchTerm}%")
                    ->orWhereHas('distrito', function ($d) use ($searchTerm) {
                        $d->where('descripcion', 'like', "%{$searchTerm}%");
                    });
                    
            });
        }

        return Inertia::render('Instituciones', [
            'instituciones' => $query->paginate($perPage),
            'distritos' => Distrito::select('id_distrito', 'descripcion', 'codigo')->get(),
            'filters' => ['search' => $searchTerm, 'perPage' => $perPage],
        ]);
    }

    public function store(InstitucionRequest $request)
    {
        $validated = $request->validated();
        $validated['uuid_institucion'] = Str::uuid();

        // Solo se guarda el ID de la unidad educativa
        $institucion = Institucion::create($validated);

return back()->with('success', 'Institución creada correctamente')->with('datos_array', [
    'Distrito' => optional($institucion->distrito)->descripcion,
]);

    }

    public function update(InstitucionRequest $request, $uuid)
    {
        $institucion = Institucion::where('uuid_institucion', $uuid)->firstOrFail();
        $validated = $request->validated();

        $institucion->update($validated);

        return redirect()->route('instituciones.index')->with([
            'success' => true,
            'datos_array' => [
                'title' => '¡Actualizado!',
                'text' => 'El Institucion fue editado correctamente.',
                'icon' => 'success',
            ]
        ]);
    }

    public function updateStatus($uuid, $code)
    {
        if (!$uuid || !$code) {
            return back()->with('error', 'Hubo un error con la solicitud.');
        }

        $institucion = Institucion::where('uuid_institucion', $uuid)->firstOrFail();

        $newStatus = match ($code) {
            '1' => 'activo',
            '2' => 'inactivo',
            default => 'eliminado',
        };

        $institucion->update(['estado' => $newStatus]);

        return back()->with('editado', 'ok');
    }



public function getByDistrito($distritoId)
{
    return response()->json(
        Institucion::where('id_distrito', $distritoId)
            ->where('estado', 'activo')
            ->get(['id_institucion', 'nivel'])
    );
}


public function getCodigosSieByInstitucion($institucionId)
{
    return response()->json(
        CodigoSie::where('institucion_id', $institucionId)
            ->where('estado', 'activo')
            ->get(['id_codigo_sie', 'unidad_educativa'])
    );
}
public function getDatosRelacionados($id)
{
    try {
        // Validar si existe el distrito
        $distrito = Distrito::findOrFail($id);

        // Obtener instituciones relacionadas activas
        $instituciones = Institucion::where('id_distrito', $id)
            ->where('estado', 'activo')
            ->get(['id_institucion', 'nivel']);

        // (Opcional) podrías incluir códigos sie relacionados también
        $codigos = CodigoSie::whereIn('institucion_id', $instituciones->pluck('id_institucion'))
            ->where('estado', 'activo')
            ->get(['id_codigo_sie', 'unidad_educativa', 'institucion_id']);

        return response()->json([
            'instituciones' => $instituciones,
            'codigos_sie' => $codigos,
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'error' => 'Error al obtener datos relacionados: ' . $th->getMessage(),
        ], 500);
    }
}

}

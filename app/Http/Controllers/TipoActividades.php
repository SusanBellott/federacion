<?php

namespace App\Http\Controllers;

use App\Models\TipoActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TipoActividades extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tiposactividad.index', ['only' => ['index']]);
        $this->middleware('permission:tiposactividad.store', ['only' => ['store']]);
        $this->middleware('permission:tiposactividad.update', ['only' => ['update']]);
        $this->middleware('permission:tiposactividad.destroy', ['only' => ['destroy']]);
        $this->middleware('permission:editarestadotipoactividad.update', ['only' => ['updateEstado']]);
    }

    public function index(Request $request)
    {
        $busqueda = $request->input('search', '');
        $perPage = (int)$request->input('perPage', 10);
        $allowedPerPage = [10, 12, 24, 48];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        $query = TipoActividad::select('id', 'uuid_tipo_actividad', 'codigo', 'nombre', 'horas_minimas', 'estado');


        // Aplicar filtro de búsqueda si existe
        if (!empty($busqueda)) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('codigo', 'LIKE', "%{$busqueda}%")
                    ->orWhere('nombre', 'LIKE', "%{$busqueda}%");
            });
        }

        $tipos = $query->orderBy('id', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('TipoActividad', [
            'tipos' => $tipos,
            'filters' => [
                'perPage' => $perPage,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'horas_minimas' => 'required|integer|min:2',
        ]);

        // Generar código incremental tipo AD-001
        $ultimo = TipoActividad::orderBy('id', 'desc')->first();
        $nuevoCodigo = 'AD-' . str_pad(($ultimo ? $ultimo->id + 1 : 1), 3, '0', STR_PAD_LEFT);

        $tipo = TipoActividad::create([
            'uuid_tipo_actividad' => Str::uuid(),
            'codigo' => $nuevoCodigo,
            'nombre' => $validatedData['nombre'],
            'horas_minimas' => $validatedData['horas_minimas'],
            'estado' => 'activo',
        ]);

        return redirect()->back()->with([
            'success' => true,
            'datos_array' => [
                'titulo' => '¡Registro creado!',
                'texto' => "El tipo de actividad {$tipo->nombre} ha sido creado exitosamente",
                'tipo' => 'success',
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoActividad::findOrFail($id);

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:100',
            'horas_minimas' => 'required|integer|min:2',
        ]);

        $tipo->update([
            'nombre' => $validatedData['nombre'],
            'horas_minimas' => $validatedData['horas_minimas'],
        ]);

        return redirect()->back()->with([
            'success' => true,
            'datos_array' => [
                'titulo' => '¡Registro actualizado!',
                'texto' => "El tipo de actividad {$tipo->nombre} ha sido actualizado correctamente",
                'tipo' => 'success',
            ]
        ]);
    }
    public function updateEstado($uuid, $code)
    {
        if (!$uuid || !$code) {
            return back()->with('error', 'Hubo un error con la solicitud.');
        }

        $tipo = TipoActividad::where('uuid_tipo_actividad', $uuid)->firstOrFail();

        $nuevoEstado = match ((string)$code) {
            '1' => 'activo',
            '2' => 'inactivo',
            default => 'eliminado',
        };

        $tipo->update(['estado' => $nuevoEstado]);

        return back()->with('editado', 'ok'); // Para disparar SweetAlert
    }



    public function destroy($id)
    {
        $tipo = TipoActividad::findOrFail($id);
        $nombre = $tipo->nombre;

        // En lugar de eliminar físicamente, cambiamos el estado
        $tipo->update(['estado' => 'eliminado']);

        return redirect()->back()->with([
            'success' => true,
            'datos_array' => [
                'titulo' => '¡Registro eliminado!',
                'texto' => "El tipo de actividad {$nombre} ha sido eliminado",
                'tipo' => 'success',
            ]
        ]);
    }
}

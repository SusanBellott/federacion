<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

use App\Models\Institucion as ModelInstitucion;
use App\Models\User as ModelUser;
use App\Models\Curso;
use Spatie\Permission\Models\Role;

class User extends Controller
{
    public function index()
    {
        $searchTerm = request()->query('search');
        $perPage = request()->query('perPage', 10);

        $query = ModelUser::query()->with('roles');

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('ci', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $roles = Role::orderBy('id', 'ASC')->get();
        $instituciones = ModelInstitucion::orderBy('id_institucion', 'ASC')->get();

        $usuarios = $query->paginate($perPage);

        return Inertia::render('User', [
            'usuarios' => $usuarios,
            'instituciones' => $instituciones,
            'roles' => $roles,
            'filters' => [
                'search' => $searchTerm,
                'perPage' => $perPage
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_institucion'    => 'required|integer',
            'ci'                => 'required',
            'rda'               => 'required',
            'name'              => 'required|string',
            'primer_apellido'   => 'required|string',
            'segundo_apellido'  => 'nullable|string',
            'item'              => 'required',
            'cargo'             => 'required',
            'horas'             => 'required',
            'email'             => 'required|email|unique:users,email',
        ]);

        $validated['uuid_user'] = Str::uuid();
        $validated['password'] = bcrypt($validated['ci']);
        $validated['estado'] = 'activo';

        ModelUser::create($validated)->assignRole($request->role);

        return back()
            ->with('success', 'Usuario creado correctamente')
            ->with('datos_array', [$validated]);
    }

    public function update(Request $request, $id)
    {
        $usuario = ModelUser::where('uuid_user', $id)->firstOrFail();

        $validated = $request->validate([
            'id_institucion'    => 'required|integer',
            'ci'                => 'required',
            'rda'               => 'nullable',
            'name'              => 'required|string',
            'primer_apellido'   => 'required|string',
            'segundo_apellido'  => 'required|string',
            'item'              => 'required',
            'cargo'             => 'required',
            'horas'             => 'required|integer',
            'email'             => 'required|email|unique:users,email,' . $usuario->id,
        ]);

        $usuario->update($validated);
        $usuario->roles()->sync($request->role);

        return back()
            ->with('success', 'Usuario actualizado correctamente')
            ->with('datos_array', [$validated]);
    }

    public function updatedelete($id, $cod)
    {
        if ($id && $cod) {
            $usuario = ModelUser::where('uuid_user', $id)->firstOrFail();

            $nuevoEstado = match($cod) {
                '1' => 'activo',
                '2' => 'inactivo',
                default => 'eliminado',
            };

            $usuario->update(['estado' => $nuevoEstado]);

            return back()->with('editado', 'ok');
        }

        return back()->with('error', 'Hubo un error con la solicitud.');
    }
}

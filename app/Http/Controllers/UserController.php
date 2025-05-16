<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Institucion;
use App\Models\User;
use App\Models\Curso;
use Spatie\Permission\Models\Role;
use App\Http\Requests\UserRequest;
use App\Models\Distrito;

use App\Models\CodigoSie;
class UserController extends Controller
{
    /**
     * Constructor que aplica middleware de permisos a las rutas del controlador.
     */
    public function __construct()
    {
        $this->middleware('permission:usuarios.index', ['only' => ['index']]);
        $this->middleware('permission:usuarios.create', ['only' => ['store']]);
        $this->middleware('permission:editarestadodeleteusuario.update', ['only' => ['updateStatus']]);
        $this->middleware('permission:usuarioseditar.update', ['only' => ['update']]);
        $this->middleware('permission:usuarios.resetearpassword', ['only' => ['resetPassword']]);
    }

    /**
     * Muestra una lista paginada de usuarios con opción de búsqueda.
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

        $query = User::with(['distrito', 'codigoSie', 'institucion', 'roles']);

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('ci', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('name', 'like', "%{$searchTerm}%")
              ->orWhere('primer_apellido', 'like', "%{$searchTerm}%");
            });
        }

        return Inertia::render('User', [
            'usuarios' => $query->paginate($perPage),
            'distritos' => Distrito::select('id_distrito', 'descripcion')->get(),
        'instituciones' => Institucion::select('id_institucion', 'nivel')->get(),
        'codigosSie' => CodigoSie::select('id_codigo_sie', 'unidad_educativa')->get(),

            'roles' => Role::orderBy('id', 'ASC')->get(),
            'filters' => [
                'search' => $searchTerm,
                'perPage' => $perPage
            ]
        ]);
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        
        // Generar UUID y crear correo electrónico automático
        $validated['uuid_user'] = Str::uuid();
        $inicial = strtoupper(substr($validated['name'], 0, 1));
        $validated['email'] = $inicial.'_'.$validated['rda'].'@fdteulp.com';
        $validated['password'] = Hash::make($validated['ci']);
        $validated['estado'] = 'activo';

        // Crear usuario y asignar rol
        $user = User::create($validated);
        $user->assignRole($request->role);

        return back()
            ->with('success', 'Usuario creado correctamente')
            ->with('datos_array', [$validated]);
    }

    /**
     * Actualiza la información de un usuario existente.
     *
     * @param UserRequest $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, $uuid)
    {
        $user = User::where('uuid_user', $uuid)->firstOrFail();
    
        $request->validate([
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);
    
        $validated = $request->validated();
    
        // 🔄 Regenerar correo automáticamente con inicial y RDA
        $inicial = strtoupper(substr($validated['name'], 0, 1));
        $validated['email'] = $inicial . '_' . $validated['rda'] . '@fdteulp.com';
    
        $user->update($validated);
        $user->roles()->sync($request->role);
    
        return back()
            ->with('success', 'Usuario actualizado correctamente')
            ->with('datos_array', [$validated]);
    }
    

    /**
     * Actualiza el estado de un usuario (activo, inactivo o eliminado).
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
        
        $user = User::where('uuid_user', $uuid)->firstOrFail();
        
        $newStatus = match($code) {
            '1' => 'activo',
            '2' => 'inactivo',
            default => 'eliminado',
        };
        
        $user->update(['estado' => $newStatus]);
        
        return back()->with('editado', 'ok');
    }

    /**
     * Restablece la contraseña de un usuario a su número de CI.
     *
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword($uuid)
    {
        $user = User::where('uuid_user', $uuid)->firstOrFail();
        $user->password = Hash::make($user->ci);
        $user->update();
        
        return back()->with('editado', 'ok');
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AsignarRolEstudiante extends Command
{
    protected $signature = 'asignar:rol-estudiante';

    protected $description = 'Asigna el rol "Estudiante" a todos los usuarios que no lo tienen aún';

    public function handle()
    {
        $role = Role::where('name', 'Estudiante')->first();

        if (!$role) {
            $this->error('⚠️ El rol "Estudiante" no existe en la base de datos.');
            return;
        }

        $usuariosSinRol = User::whereDoesntHave('roles')->get();

        if ($usuariosSinRol->isEmpty()) {
            $this->info('✅ Todos los usuarios ya tienen al menos un rol asignado.');
            return;
        }

        foreach ($usuariosSinRol as $usuario) {
            $usuario->assignRole($role);
            $this->info("✔ Rol asignado a: {$usuario->email}");
        }

        $this->info("🎉 Se asignó el rol 'Estudiante' a {$usuariosSinRol->count()} usuarios.");
    }
}

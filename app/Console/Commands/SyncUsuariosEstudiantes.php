<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SyncUsuariosEstudiantes extends Command
{
    protected $signature = 'sync:usuarios-estudiantes';

    protected $description = 'Hashea contraseñas en texto plano y asigna el rol Estudiante si no tiene ninguno.';

    public function handle()
    {
        $this->info('🚀 Iniciando sincronización de usuarios...');

        $role = Role::where('name', 'Estudiante')->first();

        if (!$role) {
            $this->error('❌ El rol "Estudiante" no existe en la base de datos.');
            return;
        }

        $users = User::with('roles')->get();
        $count = 0;

        foreach ($users as $user) {
            $actualizo = false;

            // Hashear contraseña si no está hasheada
            if (!Str::startsWith($user->password, '$2y$')) {
                $originalPassword = $user->password;
                $user->password = Hash::make($originalPassword);
                $this->info("🔐 Contraseña hasheada para: {$user->email}");
                $actualizo = true;
            }

            // Asignar rol Estudiante si no tiene roles
            if ($user->roles->isEmpty()) {
                $user->assignRole($role);
                $this->info("🎓 Rol 'Estudiante' asignado a: {$user->email}");
                $actualizo = true;
            }

            if ($actualizo) {
                $user->save();
                $count++;
            }
        }

        $this->info("✅ Proceso completado. Usuarios actualizados: {$count}");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HashPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash:passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hashea las contraseñas en texto plano de todos los usuarios si no están hasheadas.';

    /**
     * Execute the console command.
     */
public function handle()
{
    $this->info('Iniciando el proceso de hasheo de contraseñas...');

    $users = User::with('roles')->get();

    foreach ($users as $user) {
        if (!Str::startsWith($user->password, '$2y$')) {
            $originalPassword = $user->password;
            $user->password = Hash::make($user->password);
            $user->save();

            // Vuelve a asignar el rol
            $roleName = $user->roles->pluck('name')->first();
            if ($roleName) {
                $user->assignRole($roleName);
            }

            $this->info("Contraseña de usuario {$user->email} hasheada y rol re-asignado correctamente. (Antes: {$originalPassword})");
        }
    }

    $this->info('Proceso de hasheo de contraseñas completado.');
}

}

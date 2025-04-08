<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Distrito;
use App\Models\Institucion;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RoleSeeder::class);
        // Crear Distrito de prueba
        $distrito = Distrito::create([
            'uuid_distrito' => Str::uuid(),
            'codigo' => 123456,
            'descripcion' => 'Distrito Central',
            'estado' => 'activo',
        ]);

        // Crear Institución de prueba
        $institucion = Institucion::create([
            'id_distrito' => $distrito->id_distrito,
            'uuid_institucion' => Str::uuid(),
            'subsistema' => 'Educación Regular',
            'servicio' => 987654,
            'servicio_generado' => 1,
            'nivel' => 'Secundaria',
            'programa' => 456789,
            'unidad_educativa' => 'Unidad Educativa Ejemplo',
            'estado' => 'activo',
        ]);

        // Crear Usuario de prueba
        User::create([
            'id_institucion' => $institucion->id_institucion,
            'uuid_user' => Str::uuid(),
            'ci' => 12345678,
            'rda' => 87654321,
            'name' => 'Susan',
            'primer_apellido' => 'administrador',
            'segundo_apellido' => 'admin',
            'item' => 1001,
            'cargo' => 5,
            'horas' => 40,
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'estado' => 'activo',
        ])->assignRole('Administrador');
    }
}

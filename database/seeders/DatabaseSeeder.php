<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Distrito;
use App\Models\Institucion;
use App\Models\CodigoSie;
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

            // Ahora que ya existen, crea la Institución correctamente
    $institucion = Institucion::create([
        'id_distrito' => $distrito->id_distrito,
        'servicio' => 987654,
        'servicio_generado' => 1,
        'subsistema' => 'Educación Regular',
        'nivel' => 'Secundaria',
        'estado' => 'activo',
    ]);

            // Crear Código SIE después (necesita distrito)
    $codigoSie = CodigoSie::create([
        'programa' => 456789,
        'unidad_educativa' => 'Unidad Educativa Ejemplo',
        'estado' => 'activo',
        'distrito_id' => $distrito->id_distrito,
        'institucion_id' => $institucion->id_institucion,
    ]);



        // Crear Usuario de prueba
        User::create([
            'ci' => 12345678,
            'complemento_ci' => 'LP',

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
            'distrito_id' => $distrito->id_distrito,
            'institucion_id' => $institucion->id_institucion,
            'codigo_sie_id' => $codigoSie->id_codigo_sie,
        ])->assignRole('Administrador');
    }
    
}
    

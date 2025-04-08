<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Admin
        $admin = Role::create(['name' => 'Administrador']);
        //Encargado
        $encargado = Role::create(['name' => 'Encargado']);
        //Estudiante
        $estudiante = Role::create(['name' => 'Estudiante']);

        /* ---------------------------------------------------------
         ---------------------Lista de Permisos----------------------
         -----------------------------------------------------------*/
        //  Escritorio
        Permission::create(['name' => 'dashboard'])->syncRoles([$admin, $encargado]);

        // Usuarios
        Permission::create(['name' => 'usuarios.index'])->syncRoles([$admin, $encargado]);
        Permission::create(['name' => 'usuarios.create'])->syncRoles([$admin]);
        Permission::create(['name' => 'editarestadodeleteusuario.update'])->syncRoles([$admin]);
        Permission::create(['name' => 'usuarioseditar.update'])->syncRoles([$admin]);
        Permission::create(['name' => 'usuarios.resetearpassword'])->syncRoles([$admin]);


        // Recepcion
        Permission::create(['name' => 'recepciones.index'])->syncRoles([$admin, $encargado ]);
        Permission::create(['name' => 'recepcion.create'])->syncRoles([$admin, $encargado ]);
        Permission::create(['name' => 'editarestadorecepcion.update'])->syncRoles([$admin, $encargado]);
        Permission::create(['name' => 'recepcioneseditar.update'])->syncRoles([$admin, $encargado]);

         // Inscritos
         Permission::create(['name' => 'inscritos.index'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'inscritos.create'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'editarestadodeleteinscritos.update'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'inscritoeditar.update'])->syncRoles([$admin, $encargado]);

        // Cursos
         Permission::create(['name' => 'cursos.index'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'cursos.create'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'cursosimagenes.create'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'editarestadodeletecursos.update'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'cursoseditar.update'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'imagenesditar.update'])->syncRoles([$admin, $encargado]);

         // Instituciones
         Permission::create(['name' => 'instituciones.index'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'instituciones.create'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'editarestadodelete.update'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'institucioneseditar.update'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'curso.inscrito.reporte'])->syncRoles([$admin, $encargado]);

         // Distritos
         Permission::create(['name' => 'distritos.index'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'distritos.create'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'editarestadodeletedistrito.update'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'distritoseditar.update'])->syncRoles([$admin, $encargado]);
        //  Permission::create(['name' => 'curso.inscrito.reporte'])->syncRoles([$admin, $encargado]);

         // Estudioantes
         Permission::create(['name' => 'estudiantes.index'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'editarestadodeleteestudiantes.update'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'estudianteseditar.update'])->syncRoles([$admin, $encargado]);

         // Certificados
         Permission::create(['name' => 'certificados.index'])->syncRoles([$admin, $encargado ]);
         Permission::create(['name' => 'estuditarcursocerti.update'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'estuditarimagencertificado.update'])->syncRoles([$admin, $encargado]);
         Permission::create(['name' => 'imagencertificadodelete.delete'])->syncRoles([$admin, $encargado]);
    }
}

<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\User;
use App\Http\Controllers\Inscripcion;
use App\Http\Controllers\Curso;
use App\Http\Controllers\Distrito;
use App\Http\Controllers\Institucion;
use App\Http\Controllers\Recepcion;
use App\Http\Controllers\Vistaestudiante;
use App\Http\Controllers\Certificado;
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/verificacion/cursos/{uuid}', [Inscripcion::class, 'verificarCurso'])->name('verificar.curso.inscrito');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    //------------------------------------------------- Usuarios ------------------------------------------------
    Route::get('usuarios', action: [User::class, 'index'])->name('usuarios.index');
    Route::post('usuarios', action: [User::class, 'store'])->name('usuarios.create');
    Route::patch('usuarios/{id}/{cod}', action: [User::class, 'updatedelete'])->name('editarestadodeleteusuario.update');
    Route::put('usuarioseditar/{id}', [User::class, 'update'])->name('usuarioseditar.update');
    //------------------------------------------------- recepcion todo ------------------------------------------
    Route::get('recepciones', action: [Recepcion::class, 'index'])->name('recepciones.index');
    Route::post('recepciones', action: [Recepcion::class, 'store'])->name('recepcion.create');
    Route::patch('recepciones/{id}/{cod}', action: [Recepcion::class, 'updatedelete'])->name('editarestadorecepcion.update');
    Route::put('recepcioneseditar/{id}', [Recepcion::class, 'update'])->name('recepcioneseditar.update');
    //------------------------------------------------- inscritos todo ------------------------------------------

    Route::get('inscritos', action: [Inscripcion::class, 'index'])->name('inscritos.index');
    Route::post('inscritos', action: [Inscripcion::class, 'store'])->name('inscritos.create');
    Route::patch('inscritos/{id}/{cod}', action: [Inscripcion::class, 'updatedelete'])->name('editarestadodeleteinscritos.update');
    Route::put('inscritoeditar/{id}', [Inscripcion::class, 'update'])->name('inscritoeditar.update');
    Route::get('/reporte-de-curso-inscrito/{uuid}', [Inscripcion::class, 'pdfcurso'])->name('curso.inscrito.reporte');


    //------------------------------------------------- cursos todo ------------------------------------------

    Route::get('cursos', action: [Curso::class, 'index'])->name('cursos.index');
    Route::post('cursos', action: [Curso::class, 'store'])->name('cursos.create');
    Route::post('cursosimagenes',  [Curso::class, 'storeimagen'])->name('cursosimagenes.create');
    Route::patch('cursos/{id}/{cod}', action: [Curso::class, 'updatedelete'])->name('editarestadodeletecursos.update');
    Route::put('cursoseditar/{id}', [Curso::class, 'update'])->name('cursoseditar.update');
    Route::put('imagenesditar/{id}',action: [Curso::class, 'updateimagen'])->name('imagenesditar.update');

    //------------------------------------------------- institucion todo ------------------------------------------
    Route::get('instituciones', action: [Institucion::class, 'index'])->name('instituciones.index');
    Route::post('instituciones', action: [Institucion::class, 'store'])->name('instituciones.create');
    Route::patch('instituciones/{id}/{cod}', action: [Institucion::class, 'updatedelete'])->name('editarestadodelete.update');
    Route::put('institucioneseditar/{id}', [Institucion::class, 'updateinstitutucion'])->name('institucioneseditar.update');

    //------------------------------------------------- distritos todo ------------------------------------------

    Route::get('distritos', action: [Distrito::class, 'index'])->name('distritos.index');
    Route::post('distritos', action: [Distrito::class, 'store'])->name('distritos.create');
    Route::patch('distritos/{id}/{cod}', action: [Distrito::class, 'updatedelete'])->name('editarestadodeletedistrito.update');
    Route::put('distritoseditar/{id}', [Distrito::class, 'update'])->name('distritoseditar.update');

     //------------------------------------------------- vistaestudiantes todo ------------------------------------------
     Route::get('estudiantes', action: [Vistaestudiante::class, 'index'])->name('estudiantes.index');
     Route::patch('estudiantes/{id}/{cod}', action: [Vistaestudiante::class, 'updatedelete'])->name('editarestadodeleteestudiantes.update');
     Route::put('estudianteseditar/{id}', [Vistaestudiante::class, 'update'])->name('estudianteseditar.update');

    //------------------------------------------------- todo certificados  ------------------------------------------
    Route::get('certificados/{uuid_curso}', [Certificado::class, 'index'])->name('certificados.index');
    Route::put('estuditarcursocerti/{id}', [Certificado::class, 'update'])->name('estuditarcursocerti.update');
    Route::put('estuditarimagencertificado/{id}', [Certificado::class, 'update2'])->name('estuditarimagencertificado.update');
    Route::put('imagencertificadodelete/{id}', [Certificado::class, 'update3'])->name('imagencertificadodelete.delete');


});

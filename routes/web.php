<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Inscripcion;
use App\Http\Controllers\Curso;
use App\Http\Controllers\CodigoSieController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\Recepcion;
use App\Http\Controllers\Vistaestudiante;
use App\Http\Controllers\Certificado;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\TipoActividades;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    // Si ya está logueado, lo mandamos directo al dashboard
    if ( Auth::check() ) {
        return redirect()->route('dashboard');
    }

    // Si no, mostramos el login
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
    ]);
});

Route::get('/verificacion/cursos/{uuid}', [Inscripcion::class, 'verificarCurso'])->name('verificar.curso.inscrito');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {    
    Route::get('/dashboard/estadisticas', [Dashboard::class, 'getEstadisticas'])
    ->middleware(['auth', 'permission:dashboard.estadisticas'])
    ->name('dashboard.estadisticas');
Route::get('/dashboard', [Dashboard::class, 'index'])->middleware(['auth'])->name('dashboard');
// Dashboard para estudiantes
Route::get('/estudiante/dashboard', function () {
    return Inertia::render('DashboardEstudiante');
})->middleware(['auth', 'permission:dashboard'])->name('dashboard.estudiante');

// Dashboard para encargados
Route::get('/encargado/dashboard', function () {
    return Inertia::render('DashboardEncargado');
})->middleware(['auth', 'permission:dashboard'])->name('dashboard.encargado');

// Dashboard para administradores
Route::get('/admin/dashboard', function () {
    return Inertia::render('DashboardAdmin');
})->middleware(['auth', 'permission:dashboard'])->name('dashboard.admin');

//    Route::get('/dashboard', function () {
  //      return Inertia::render('Dashboard');
    //})->name('dashboard');
    //------------------------------------------------- Usuarios ------------------------------------------------
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/', [UserController::class, 'store'])->name('usuarios.create');
        Route::put('/{uuid}', [UserController::class, 'update'])->name('usuarioseditar.update');
        Route::patch('/{uuid}/estado/{code}', [UserController::class, 'updateStatus'])->name('editarestadodeleteusuario.update');
        Route::patch('/{uuid}/reiniciar-password', [UserController::class, 'resetPassword'])->name('usuarios.resetearpassword');

    });   
    

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
    Route::patch('estado-curso/{id}/{cod}', action: [Curso::class, 'estadoupdate'])->name('estado.update.curso');
    Route::get('/miscursos', [Curso::class, 'misCursos'])->name('miscursos');

    //------------------------------------------------- institucion todo ------------------------------------------
    Route::prefix('instituciones')->group(function () {
        Route::get('/', [InstitucionController::class, 'index'])->name('instituciones.index');
        Route::post('/', [InstitucionController::class, 'store'])->name('instituciones.create');
        Route::put('/{uuid}', [InstitucionController::class, 'update'])->name('institucioneseditar.update');
        Route::patch('/{uuid}/estado/{code}', [InstitucionController::class, 'updateStatus'])->name('editarestadodelete.update');
    });
    Route::prefix('api')->middleware('auth')->group(function () {
        // Rutas para datos relacionados con distritos
        Route::prefix('distritos')->group(function () {
            // Añadir la ruta correcta que espera el componente Vue
        Route::get('/{distrito}/datos-relacionados', [InstitucionController::class, 'getDatosRelacionados'])
        ->name('distritos.datos-relacionados');
            // Unidades educativas (usando controlador)
            Route::get('/{distrito}/unidades-educativas', [InstitucionController::class, 'getUnidadesEducativas'])
                ->name('distritos.unidades-educativas');
                
            // Instituciones (versión mejorada)
            Route::get('/{distrito}/instituciones', [InstitucionController::class, 'getByDistrito'])
                ->name('distritos.instituciones');
                
            // Códigos SIE (versión mejorada)
            Route::get('/{distrito}/codigos-sie', [CodigoSieController::class, 'getByDistrito'])
                ->name('distritos.codigos-sie');
        });
    });
    Route::get('/distritos/{id}/datos', [InstitucionController::class, 'datosRelacionados']);
    Route::get('api/distritos/{id}/datos', [InstitucionController::class, 'getDatosRelacionados']);

    //------------------------------------------------- distritos todo ------------------------------------------

    Route::prefix('distritos')->group(function () {
        Route::get('/', [DistritoController::class, 'index'])->name('distritos.index');
        Route::post('/', [DistritoController::class, 'store'])->name('distritos.create');
        Route::put('/{uuid}', [DistritoController::class, 'update'])->name('distritoseditar.update');
        Route::patch('/{uuid}/estado/{code}', [DistritoController::class, 'updateStatus'])->name('editarestadodeletedistrito.update');

    });

     //------------------------------------------------- vistaestudiantes todo ------------------------------------------
     Route::get('estudiantes', action: [Vistaestudiante::class, 'index'])->name('estudiantes.index');
     Route::patch('estudiantes/{id}/{cod}', action: [Vistaestudiante::class, 'updatedelete'])->name('editarestadodeleteestudiantes.update');
     Route::put('estudianteseditar/{id}', [Vistaestudiante::class, 'update'])->name('estudianteseditar.update');

    //------------------------------------------------- todo certificados  ------------------------------------------
    Route::get('certificados/{uuid_curso}', [Certificado::class, 'index'])->name('certificados.index');
    Route::put('estuditarcursocerti/{id}', [Certificado::class, 'update'])->name('estuditarcursocerti.update');
    Route::put('estuditarimagencertificado/{id}', [Certificado::class, 'update2'])->name('estuditarimagencertificado.update');
    Route::put('imagencertificadodelete/{id}', [Certificado::class, 'update3'])->name('imagencertificadodelete.delete');


// In routes/web.php or a dedicated routes file
Route::prefix('codigo-sie')->group(function () {
    Route::get('/', [CodigoSieController::class, 'index'])->name('codigosie.index');
    Route::post('/create', [CodigoSieController::class, 'store'])->name('codigosie.create');
    Route::put('/update/{codigo_sie}', [CodigoSieController::class, 'update'])->name('codigosie.update');

    Route::patch('/estado/{uuid}/{code}', [CodigoSieController::class, 'updateStatus'])->name('codigosie.estado.update');
});
    




    Route::get('tipos-actividad', [TipoActividades::class, 'index'])->name('tiposactividad.index');
    Route::post('tipos-actividad', [TipoActividades::class, 'store'])->name('tiposactividad.store');
    Route::put('tipos-actividad/{id}', [TipoActividades::class, 'update'])->name('tiposactividad.update');
    Route::delete('tipos-actividad/{id}', [TipoActividades::class, 'destroy'])->name('tiposactividad.destroy');        
    Route::patch('tipos-actividad/{uuid}/{code}', [TipoActividades::class, 'updateEstado'])->name('editarestadotipoactividad.update');


});

Route::get('/storage-link',function () {
    $targetFolder = storage_path('app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'].'/storage';
    symlink($targetFolder, $linkFolder);
});

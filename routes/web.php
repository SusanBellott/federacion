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
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\Estadisticas;
use App\Http\Controllers\ReporteCursoPorMes;
use App\Http\Controllers\ReporteCursoPorMesYAnio;
use App\Http\Controllers\ReporteInscritos;
use App\Http\Controllers\Reportes;


Route::get('/', function () {
    // Si ya está logueado, lo mandamos directo al dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Si no, mostramos el login
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
    ]);
});

Route::get('/custom-captcha', [CaptchaController::class, 'generate']);

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
    Route::get('/reporte-mensual', [Dashboard::class, 'descargarReporteMensual'])->name('dashboard.reporte.mensual');


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
   Route::get('/reporte/inscritos/curso/{uuid}', [ReporteInscritos::class, 'reporteInscritosPorCurso'])
    ->name('reporte.inscritos')
    ->middleware('permission:curso.inscrito.reporte');


    //------------------------------------------------- cursos todo ------------------------------------------

    Route::get('cursos', action: [Curso::class, 'index'])->name('cursos.index');
    Route::post('cursos', action: [Curso::class, 'store'])->name('cursos.create');
    Route::post('cursosimagenes',  [Curso::class, 'storeimagen'])->name('cursosimagenes.create');
    Route::patch('cursos/{id}/{cod}', action: [Curso::class, 'updatedelete'])->name('editarestadodeletecursos.update');
    Route::put('cursoseditar/{id}', [Curso::class, 'update'])->name('cursoseditar.update');
    Route::put('imagenesditar/{id}', action: [Curso::class, 'updateimagen'])->name('imagenesditar.update');
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
    Route::get('/instituciones/{id}/codigos-sie', [CodigoSieController::class, 'getByInstitucion']);
    Route::get('/api/instituciones/{id}/codigos-sie', [CodigoSieController::class, 'getByInstitucion']);

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

    //------------------------------------------------- Codigo Sie  ------------------------------------------

    Route::prefix('codigo-sie')->group(function () {
        Route::get('/', [CodigoSieController::class, 'index'])->name('codigosie.index');
        Route::post('/create', [CodigoSieController::class, 'store'])->name('codigosie.create');
        Route::put('/update/{codigo_sie}', [CodigoSieController::class, 'update'])->name('codigosie.update');

        Route::patch('/estado/{uuid}/{code}', [CodigoSieController::class, 'updateStatus'])->name('codigosie.estado.update');
    });
    //------------------------------------------------- tipo de actividad  ------------------------------------------

    Route::get('tipos-actividad', [TipoActividades::class, 'index'])->name('tiposactividad.index');
    Route::post('tipos-actividad', [TipoActividades::class, 'store'])->name('tiposactividad.store');
    Route::put('tipos-actividad/{id}', [TipoActividades::class, 'update'])->name('tiposactividad.update');
    Route::delete('tipos-actividad/{id}', [TipoActividades::class, 'destroy'])->name('tiposactividad.destroy');
    Route::patch('tipos-actividad/{uuid}/{code}', [TipoActividades::class, 'updateEstado'])->name('editarestadotipoactividad.update');


    //------------------------------------------------- Reportes  ------------------------------------------


    Route::get('/reportes', [Reportes::class, 'index'])
        ->middleware('can:reportes.index')
        ->name('reportes.index');


    Route::get('/estadisticas/cursos-por-mes/{año?}', [Estadisticas::class, 'cursosPorMes']);

    Route::get('/reporte/mes/{año}/{mes}', [ReporteCursoPorMesYAnio::class, 'reporteCursosPorMesYAnio'])->name('reporte.mes');
    Route::get('/reporte/inscritos/{uuidCurso}', [reporteInscritos::class, 'inscritosPorCurso'])
        ->name('reporte.inscritos');


    Route::get('/reporte/verificar-mes/{año}/{mes}', [ReporteCursoPorMesYAnio::class, 'verificarDatosMes']);
/// reporte año 

    Route::get('/reportes/cursos-por-mes/{año}', [ReporteCursoPorMes::class, 'reporteCursosPorMes'])->name('reportes.cursos.por.mes');
Route::get('/reportes/anios-disponibles', [ReporteCursoPorMes::class, 'obtenerAniosConReportes']);

/// reporte mes año
Route::get('/reporte/anios-disponibles', [ReporteCursoPorMesYAnio::class, 'obtenerAniosConReportes']);
Route::get('/reporte/meses-disponibles/{anio}', [ReporteCursoPorMesYAnio::class, 'obtenerMesesPorAnio']);

//

Route::get('/reporte-inscritos', [ReporteInscritos::class, 'index'])->name('reporte.inscritos.index');
Route::get('/reporte-inscritos/pdf/{uuidCurso}', [ReporteInscritos::class, 'reporteInscritosPorCurso'])->name('reporte.inscritos.pdf');
Route::get('/reporte/cursos-disponibles', [ReporteInscritos::class, 'obtenerCursosDisponibles']);
Route::get('/reportes', [ReporteInscritos::class, 'index'])->name('reportes.index');

});





Route::get('/storage-link', function () {
    $targetFolder = storage_path('app/public');
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
    symlink($targetFolder, $linkFolder);
});

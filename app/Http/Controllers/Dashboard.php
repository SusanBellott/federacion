<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Models\TipoActividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class Dashboard extends Controller
{
    /**
     * Constructor que aplica middleware de permisos
     */
    public function __construct()
    {
        // Solo los usuarios con el permiso 'dashboard.estadisticas' pueden ver las estadísticas
        $this->middleware('permission:dashboard.estadisticas', ['only' => ['getEstadisticas']]);
    }

    public function index()
    {
        // Verificamos si el usuario tiene el permiso para ver estadísticas
        $user = Auth::user();
        $canSeeStats = $user->hasPermissionTo('dashboard.estadisticas');
        $isStudent = $user->hasRole('Estudiante');
        $misCursosIds = [];
        $cursos = [];
        $cursosPago = []; // 👈 NECESARIO AQUI
        $stats = [];
        //esto es para el grafico activiades por tipo 
        $temasPorTipo = [];

        $estadisticasTemasPorTipo = [];

        if ($isStudent) {
            $misCursosIds = Inscripcion::where('id_user', $user->id)
                ->where('estado', 'activo')
                ->where('estado_ins', 'inscrito')
                ->pluck('id_curso')
                ->toArray();

            $cursos = Curso::inscripcionesDisponibles()
                ->where('estado', 'activo')
                ->where('estado_curso', 'abierto')
                ->where('tipo_pago', 'gratuito')
                ->with(['tipoActividad', 'imagencertificados'])
                ->orderBy('fecha_inicio_inscripcion')
                ->get();

            $cursosPago = Curso::inscripcionesDisponibles()
                ->where('estado', 'activo')
                ->where('estado_curso', 'abierto')
                ->where('tipo_pago', 'pago')
                ->with(['tipoActividad', 'imagencertificados'])
                ->orderBy('fecha_inicio_inscripcion')
                ->get();
        }

        // Si el usuario puede ver estadísticas, preparamos los datos
        if ($canSeeStats) {
            // 1) Totales globales
            $totalUsers        = User::count();
            $totalCourses      = Curso::count();
            $totalCertificates = Inscripcion::whereNotNull('certificado_numero')->count();
            $totalActivityTypes = TipoActividad::count();
            $totalInscriptions = Inscripcion::count();

            // 2) Conteo por tipo de actividad (curso, seminario, taller, etc.)
            //    Cargamos todas las inscripciones con su curso y tipo de actividad
            $inscripciones = Inscripcion::with('curso.tipoActividad')->get();

            $inscripcionesActivas = $inscripciones->filter(
                fn($i) =>
                $i->estado === 'activo' && $i->estado_ins === 'inscrito'
            );

            $inscripcionesInactivas = $inscripciones->filter(
                fn($i) =>
                $i->estado === 'inactivo'
            );

            //    Agrupamos por nombre de tipo y contamos cada grupo
            $byActivityType = $inscripciones
                ->filter(fn($i) => $i->estado === 'activo' && $i->estado_ins === 'inscrito')
                ->groupBy(fn($i) => $i->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(fn($group) => $group->count()); // cuenta inscripciones, no usuarios únicos

            // Agrupamos por tipo de actividad, luego por nombre de curso y contamos inscripciones
            $byCourseInType = $inscripciones
                ->filter(
                    fn($i) =>
                    $i->estado === 'activo' &&
                        $i->estado_ins === 'inscrito' &&
                        $i->curso && $i->curso->estado === 'activo'
                )
                ->groupBy(function ($ins) {
                    $tipo = $ins->curso->tipoActividad;
                    return $tipo ? "{$tipo->codigo} - {$tipo->nombre}" : 'Sin tipo';
                })
                ->map(function ($group) {
                    return $group
                        ->groupBy(function ($ins) {
                            $curso = $ins->curso;
                            return $curso ? "{$curso->codigo_curso} - {$curso->nombre}" : 'Sin curso';
                        })
                        ->map(fn($subgroup) => $subgroup->count());
                });

            // 3) Contamos certificados por curso
            $certificatesByCourse = $inscripciones
                ->filter(fn($ins) => !empty($ins->certificado_numero))
                ->groupBy(fn($ins) => $ins->curso->nombre ?? 'Sin curso')
                ->map(fn($group) => $group->count());

            // 4) Certificados por tipo de actividad y por curso
            $certificatesByTypeAndCourse = $inscripciones
                ->filter(fn($ins) => !empty($ins->certificado_numero))
                ->groupBy(fn($ins) => $ins->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(function ($group) {
                    return $group
                        ->groupBy(fn($ins) => $ins->curso->nombre ?? 'Sin curso')
                        ->map(fn($subgroup) => $subgroup->count());
                });

            $byActivityTypeOnlyActives = $inscripcionesActivas
                ->groupBy(fn($ins) => $ins->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(fn($group) => $group->count());
            $activosByTypeAndCourse = $inscripcionesActivas
                ->groupBy(fn($ins) => $ins->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(function ($group) {
                    return $group
                        ->groupBy(fn($ins) => $ins->curso->nombre ?? 'Sin curso')
                        ->map(fn($subgroup) => $subgroup->count());
                });

            // Desinscritos por tipo de actividad y por curso
            $inactivosByTypeAndCourse = $inscripciones
                ->filter(fn($ins) => $ins->estado === 'inactivo')
                ->groupBy(fn($ins) => $ins->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(function ($group) {
                    return $group
                        ->groupBy(fn($ins) => $ins->curso->nombre ?? 'Sin curso')
                        ->map(fn($subgroup) => $subgroup->count());
                });

            // 5) Total de inscripciones
            $totalInscriptions = $inscripciones->count();

            $totalEstudiantes = User::role('Estudiante')->count();
            $totalEncargados = User::role('Encargado')->count();
            $totalAdministradores = User::role('Administrador')->count();

            // 👇 Agrupamos las inscripciones por mes y luego por tipo y curso
            $byCourseInTypePorMes = $inscripciones
                ->filter(
                    fn($i) =>
                    $i->estado === 'activo' &&
                        $i->estado_ins === 'inscrito' &&
                        $i->curso && $i->curso->estado === 'activo'
                )
                ->groupBy(fn($i) => Carbon::parse($i->created_at)->format('Y-m')) // agrupa por año-mes
                ->map(function ($group) {
                    return $group
                        ->groupBy(function ($ins) {
                            $curso = $ins->curso;
                            $tipo = $curso->tipoActividad;
                            return $tipo && $curso
                                ? "{$tipo->nombre} - {$curso->codigo_curso}"
                                : 'Sin tipo';
                        })
                        ->map(fn($g) => $g->count());
                });

            $stats = [
                'totalUsers'         => $totalUsers,
                'totalCourses'       => $totalCourses,
                'totalCertificates'  => $inscripciones->whereNotNull('certificado_numero')->count(),
                'totalActivityTypes' => $totalActivityTypes,
                'totalInscriptions'  => $inscripciones->count(), // TOTAL global
                'inscripcionesActivas' => $inscripcionesActivas->count(),
                'inactivas'            => $inscripcionesInactivas->count(),
                'activosByTypeAndCourse' => $activosByTypeAndCourse,

                'byCourseInType'     => $byCourseInType,
                'certificatesByCourse' => $certificatesByCourse,
                'certificatesByTypeAndCourse' => $certificatesByTypeAndCourse,
                'byActivityType'     => $byActivityTypeOnlyActives, // 👈 Usamos solo activos aquí
                'allActivityType'    => $byActivityType,            // 👈 Esto sí incluye inactivos (para el gráfico detallado)
                'inactivosByTypeAndCourse' => $inactivosByTypeAndCourse,

                'totalEstudiantes' => $totalEstudiantes,
                'totalEncargados' => $totalEncargados,
                'totalAdministradores' => $totalAdministradores,

                'byCourseInTypePorMes' => $byCourseInTypePorMes,

            ];

            // esto es para el grafico por tipo de actividades 
            // Subcursos por tipo de actividad: cuántos cursos distintos hay en cada tipo 
            $temasPorTipo = Curso::with('tipoActividad')
                ->get()
                ->groupBy(fn($curso) => $curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(fn($group) => $group->count());

            // Inscripciones y certificados por curso dentro de cada tipo de actividad
            $estadisticasTemasPorTipo = [];

            $tipos = TipoActividad::with('cursos.inscripciones')->get();

            foreach ($tipos as $tipo) {
                $estadisticasTemasPorTipo[$tipo->nombre] = [];

                foreach ($tipo->cursos as $curso) {
                    $totalInscritos = $curso->inscripciones
                        ->where('estado', 'activo')
                        ->where('estado_ins', 'inscrito')
                        ->count();

                    $certificadosEmitidos = $curso->inscripciones
                        ->whereNotNull('certificado_numero')
                        ->count();

                    $desinscritos = $curso->inscripciones
                        ->where('estado', 'inactivo')
                        ->count();

                    $estadisticasTemasPorTipo[$tipo->nombre][] = [
                        'curso' => $curso->nombre,
                        'inscritos' => $curso->inscripciones->where('estado', 'activo')->where('estado_ins', 'inscrito')->count(),
                        'certificados' => $curso->inscripciones->whereNotNull('certificado_numero')->count(),
                        'desinscritos' => $curso->inscripciones->where('estado', 'inactivo')->count(),
                    ];
                }
            }
        }
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        return Inertia::render('Dashboard', [
            'isStudent' => $isStudent,
            'canSeeStats' => $canSeeStats,
            'stats' => $stats,
            'cursos' => $cursos,
            'cursos_pago' => $cursosPago, // 👈 AÑADIDO
            'misCursosIds' => $misCursosIds,
            // pertenece al grafico de actividades 
            'temasPorTipo'       => $temasPorTipo,

            'estadisticasTemasPorTipo' => $estadisticasTemasPorTipo,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Método separado para obtener las estadísticas
     * Solo accesible para usuarios con el permiso 'dashboard.estadisticas'
     */
    public function getEstadisticas()
    {
        // 1) Totales globales
        $totalUsers        = User::count();
        $totalCourses      = Curso::count();
        $totalCertificates = Inscripcion::whereNotNull('certificado_numero')->count();
        $totalActivityTypes   = TipoActividad::count();

        // 2) Conteo por tipo de actividad (curso, seminario, taller, etc.)
        //    Cargamos todas las inscripciones con su curso y tipo de actividad
        $inscripciones = Inscripcion::with('curso.tipoActividad')->get();
        $inscripcionesActivas = $inscripciones->filter(
            fn($i) =>
            $i->estado === 'activo' && $i->estado_ins === 'inscrito'
        );

        $inscripcionesInactivas = $inscripciones->filter(
            fn($i) =>
            $i->estado === 'inactivo'
        );

        //    Agrupamos por nombre de tipo y contamos cada grupo
        $byActivityType = $inscripciones
            ->groupBy(fn($ins) => $ins->curso->tipoActividad->nombre ?? 'Sin tipo')
            ->map(fn($group) => $group->count());

        // Agrupamos por tipo de actividad, luego por nombre de curso y contamos inscripciones
        $byCourseInType = $inscripciones
            ->groupBy(fn($ins) => $ins->curso->tipoActividad->nombre ?? 'Sin tipo')
            ->map(function ($group) {
                return $group
                    ->groupBy(fn($ins) => $ins->curso->nombre)
                    ->map(fn($subgroup) => $subgroup->count());
            });

        // 3) Contamos certificados por curso
        $certificatesByCourse = $inscripciones
            ->filter(fn($ins) => !empty($ins->certificado_numero))
            ->groupBy(fn($ins) => $ins->curso->nombre ?? 'Sin curso')
            ->map(fn($group) => $group->count());

        // 4) Certificados por tipo de actividad y por curso
        $certificatesByTypeAndCourse = $inscripciones
            ->filter(fn($ins) => !empty($ins->certificado_numero))
            ->groupBy(fn($ins) => $ins->curso->tipoActividad->nombre ?? 'Sin tipo')
            ->map(function ($group) {
                return $group
                    ->groupBy(fn($ins) => $ins->curso->nombre ?? 'Sin curso')
                    ->map(fn($subgroup) => $subgroup->count());
            });

        // 5) Total de inscripciones
        $totalInscriptions = $inscripciones->count();

        return [
            'totalUsers'         => $totalUsers,
            'totalCourses'       => $totalCourses,
            'totalCertificates'  => $totalCertificates,
            'totalActivityTypes' => $totalActivityTypes,
            'totalInscriptions'  => $totalInscriptions,
            'inscripcionesActivas' => $inscripcionesActivas->count(),
            'inactivas' => $inscripcionesInactivas->count(),

            'byActivityType'     => $byActivityType,
            'byCourseInType'     => $byCourseInType,
            'certificatesByCourse' => $certificatesByCourse,
            'certificatesByTypeAndCourse' => $certificatesByTypeAndCourse,

        ];
    }
    public function getStatsDelMes($año = null, $mes = null)
    {
        $año = $año ?? now()->year;
        $mes = $mes ?? now()->month;

        $inscripciones = Inscripcion::with('curso.tipoActividad')
            ->whereMonth('created_at', $mes)
            ->whereYear('created_at', $año)
            ->get();

        $activos = $inscripciones->where('estado', 'activo')->where('estado_ins', 'inscrito');
        $inactivos = $inscripciones->where('estado', 'inactivo');

        return [
            'totalInscriptions' => $inscripciones->count(),
            'inscripcionesActivas' => $activos->count(),
            'inscripcionesInactivas' => $inactivos->count(),

            'byActivityType' => $activos->groupBy(fn($i) => $i->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(fn($g) => $g->count()),

            'byCourseInType' => $inscripciones
                ->groupBy(fn($i) => $i->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(fn($g) => $g->groupBy(fn($i) => $i->curso->nombre)->map->count()),

            'certificatesByTypeAndCourse' => $inscripciones
                ->filter(fn($i) => $i->certificado_numero)
                ->groupBy(fn($i) => $i->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(fn($g) => $g->groupBy(fn($i) => $i->curso->nombre)->map->count()),

            'inactivosByTypeAndCourse' => $inactivos
                ->groupBy(fn($i) => $i->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(fn($g) => $g->groupBy(fn($i) => $i->curso->nombre)->map->count()),

            'activosByTypeAndCourse' => $activos
                ->groupBy(fn($i) => $i->curso->tipoActividad->nombre ?? 'Sin tipo')
                ->map(fn($g) => $g->groupBy(fn($i) => $i->curso->nombre)->map->count()),
        ];
    }
    public function reporteMensual(Request $request)
    {
        $año = $request->input('año');
        $mes = $request->input('mes');

        // ✅ Cargar datos del mes específico
        $stats = $this->obtenerEstadisticasPorMes($año, $mes); // crea esta función si aún no la tienes

        // 📄 Renderiza una vista especial (ej: resources/views/pdf/reporte.blade.php)
        $pdf = Pdf::loadView('pdf.reporte', compact('stats', 'año', 'mes'))->setPaper('a4', 'portrait');

        return $pdf->download("reporte-estadisticas-$mes-$año.pdf");
    }
}

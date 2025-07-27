<?php
// Reporte de cursos por mes y año
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteCursoPorMesYAnio extends Controller
{
    public function reporteCursosPorMesYAnio($año, $mes)
    {
        $cursos = DB::table('cursos')
            ->selectRaw("
            tipos_actividad.nombre as tipo_nombre,
            cursos.nombre,
            COUNT(inscripcions.id_inscripcion) as total_inscritos,
            COUNT(inscripcions.certificado_numero) as total_certificados
        ")
            ->leftJoin('inscripcions', function ($join) {
                $join->on('cursos.id_curso', '=', 'inscripcions.id_curso')
                    ->where('inscripcions.estado', '=', 'activo');
            })
            ->leftJoin('tipos_actividad', 'cursos.tipo_actividad_id', '=', 'tipos_actividad.id')
            ->whereRaw("EXTRACT(YEAR FROM fecha_inicio::timestamp) = ?", [$año])
            ->whereRaw("EXTRACT(MONTH FROM fecha_inicio::timestamp) = ?", [$mes])
            ->groupByRaw("tipos_actividad.nombre, cursos.nombre")
            ->orderBy('cursos.nombre')
            ->get();

        $pdf = new \App\Pdf\EncabezadoMensual();
        $pdf->fechaHora = Carbon::now()->format('d/m/Y - h:i:s A'); // 👈 Se asigna antes de AddPage
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        \Carbon\Carbon::setLocale('es');

        $fecha = Carbon::createFromDate($año, $mes, 1);
        $mesNombre = ucfirst($fecha->translatedFormat('F')) . ' de ' . $fecha->year;

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, utf8_decode("Mes : $mesNombre"), 0, 1, 'C');
        $pdf->Ln(4);

        $anchoTabla = 180;
        $margenIzquierdo = (210 - $anchoTabla) / 2;

        // Estilo de cabecera
        $pdf->SetFillColor(50, 50, 50);        // Fondo gris oscuro
        $pdf->SetTextColor(255, 255, 255);     // Texto blanco
        $pdf->SetDrawColor(0, 0, 0);           // Borde negro
        $pdf->SetLineWidth(0.3);
        $pdf->SetX($margenIzquierdo);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 8, 'Tipo de Actividad', 1, 0, 'L', true);
        $pdf->Cell(95, 8, 'Curso', 1, 0, 'L', true);
        $pdf->Cell(20, 8, 'Inscritos', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Certificados', 1, 1, 'C', true);

        // Reset estilos para contenido
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0);
        $pdf->SetFillColor(245, 245, 245); // gris claro para zebra
        $fill = false;

        $totalInscritos = 0;
        $totalCertificados = 0;

        foreach ($cursos as $curso) {
            $pdf->SetX($margenIzquierdo);
            $pdf->Cell(40, 8, utf8_decode($curso->tipo_nombre ?? '—'), 1, 0, 'L', $fill);
            $pdf->Cell(95, 8, utf8_decode($curso->nombre), 1, 0, 'L', $fill);
            $pdf->Cell(20, 8, $curso->total_inscritos, 1, 0, 'C', $fill);
            $pdf->Cell(25, 8, $curso->total_certificados, 1, 1, 'C', $fill);

            $fill = !$fill; // alternar color de fondo

            $totalInscritos += $curso->total_inscritos;
            $totalCertificados += $curso->total_certificados;
        }
        $pdf->SetX($margenIzquierdo);
        // Total general
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(135, 8, 'TOTAL MES', 1, 0, 'L', true);
        $pdf->Cell(20, 8, $totalInscritos, 1, 0, 'C', true);
        $pdf->Cell(25, 8, $totalCertificados, 1, 1, 'C', true);

        $pdf->Ln(6);

        return response()->stream(function () use ($pdf) {
            $pdf->Output('I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="reporte_cursos_mes.pdf"',
        ]);
    }
    public function verificarDatosMes($año, $mes)
    {
        // Verifica si hay cursos en el mes y año
        $hayDatosMes = DB::table('cursos')
            ->leftJoin('inscripcions', function ($join) {
                $join->on('cursos.id_curso', '=', 'inscripcions.id_curso')
                    ->where('inscripcions.estado', '=', 'activo');
            })
            ->leftJoin('tipos_actividad', 'cursos.tipo_actividad_id', '=', 'tipos_actividad.id')
            ->whereRaw("EXTRACT(YEAR FROM fecha_inicio::timestamp) = ?", [$año])
            ->whereRaw("EXTRACT(MONTH FROM fecha_inicio::timestamp) = ?", [$mes])
            ->exists();

        // Verifica si hay cursos en el año (aunque no haya en el mes)
        $hayDatosAnio = DB::table('cursos')
            ->whereRaw("EXTRACT(YEAR FROM fecha_inicio::timestamp) = ?", [$año])
            ->exists();

        return response()->json([
            'existe' => $hayDatosMes,    // true si hay cursos en ese mes
            'soloAnio' => $hayDatosAnio  // true si hay cursos en ese año
        ]);
    }
    public function obtenerAniosConReportes()
    {
        $años = DB::table('cursos')
            ->selectRaw("DISTINCT EXTRACT(YEAR FROM fecha_inicio::date) as año")
            ->orderBy('año', 'asc')
            ->pluck('año')
            ->map(fn($a) => (int)$a)
            ->toArray();

        return response()->json($años);
    }

    public function obtenerMesesPorAnio($anio)
    {
        $meses = DB::table('cursos')
            ->selectRaw("DISTINCT EXTRACT(MONTH FROM fecha_inicio::date) as mes")
            ->whereRaw("EXTRACT(YEAR FROM fecha_inicio::date) = ?", [$anio])
            ->orderBy('mes', 'asc')
            ->pluck('mes')
            ->map(fn($m) => (int)$m)
            ->toArray();

        return response()->json($meses);
    }
}

<?php
/// reporte realido y desteado no editar mas ..............
namespace App\Http\Controllers;

use App\Models\Curso;
use App\Pdf\EncabezadoAnual;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteCursoPorMes extends Controller
{
    // Este codigo realiza reportes generales por mes de los cursos
    // realizados realiza el conteo de inscritos y certificados por mes y genera un PDF con los resultados
    public function reporteCursosPorMes($año)
    {
        $cursos = DB::table('cursos')
            ->selectRaw("
        DATE_TRUNC('month', fecha_inicio::timestamp) as mes,
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
            ->groupByRaw("DATE_TRUNC('month', fecha_inicio::timestamp), tipos_actividad.nombre, cursos.nombre")
            ->orderBy('mes')
            ->get();

        $pdf = new EncabezadoAnual();
        $pdf->fechaHora = Carbon::now()->format('d/m/Y - h:i:s A'); // 👈 Se asigna antes de AddPage
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        \Carbon\Carbon::setLocale('es');

        foreach (
            $cursos->groupBy(function ($c) {
                $fecha = Carbon::parse($c->mes);
                return ucfirst($fecha->translatedFormat('F')) . ' de ' . $fecha->year;
            }) as $mes => $lista
        ) {
            $anchoTabla = 180;
            $margenIzquierdo = (210 - $anchoTabla) / 2;

            $pdf->SetX($margenIzquierdo);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 10, utf8_decode("Mes: $mes"), 0, 1);

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

            $totalMesInscritos = 0;
            $totalMesCertificados = 0;

            foreach ($lista as $curso) {
                $pdf->SetX($margenIzquierdo);
                $pdf->Cell(40, 8, utf8_decode($curso->tipo_nombre ?? '—'), 1, 0, 'L', $fill);
                $pdf->Cell(95, 8, utf8_decode($curso->nombre), 1, 0, 'L', $fill);
                $pdf->Cell(20, 8, $curso->total_inscritos, 1, 0, 'C', $fill);
                $pdf->Cell(25, 8, $curso->total_certificados, 1, 1, 'C', $fill);

                $fill = !$fill; // alternar color de fondo

                $totalMesInscritos += $curso->total_inscritos;
                $totalMesCertificados += $curso->total_certificados;
            }
            $pdf->SetX($margenIzquierdo);
            // Fila total por mes
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell(135, 8, 'TOTAL MES', 1, 0, 'L', true);
            $pdf->Cell(20, 8, $totalMesInscritos, 1, 0, 'C', true);
            $pdf->Cell(25, 8, $totalMesCertificados, 1, 1, 'C', true);

            $pdf->Ln(6); // Espacio entre meses

        }

        return response()->stream(function () use ($pdf) {
            $pdf->Output('I'); // 'I' muestra el PDF en el navegador
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="reporte_cursos_' . $año . '.pdf"',
        ]);
    }

    // Obtener años con reportes disponibles
    public function obtenerAniosConReportes()
    {
        $años = DB::table('cursos')
            ->whereNotNull('fecha_inicio')
            ->selectRaw("DISTINCT EXTRACT(YEAR FROM fecha_inicio::timestamp) as año")
            ->orderBy('año', 'asc')
            ->pluck('año')
            ->map(fn($a) => (int) $a)
            ->toArray();


        return response()->json($años);
    }
}

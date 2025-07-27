<?php
// estamos en el pdf de inscripcion debemos ordenar el modelo
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Inscripcion as ModelInscripcion;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ReporteInscritos extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->query('filtro');

        $cursosQuery = Curso::select('nombre', 'codigo_curso', 'uuid_curso')
            ->where('estado', 'activo');

        if ($filtro) {
            $cursosQuery->where(function ($q) use ($filtro) {
                $q->where('nombre', 'ILIKE', "%{$filtro}%")
                    ->orWhere('codigo_curso', 'ILIKE', "%{$filtro}%");
            });
        }

        $cursos = $cursosQuery
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Reportes', [
            'cursos' => $cursos,
            'stats' => [], // u otros datos

        ]);
    }

    public function reporteInscritosPorCurso($uuidCurso)
    {
        $curso = Curso::where('uuid_curso', $uuidCurso)->firstOrFail();
        $inscritos = ModelInscripcion::where('id_curso', $curso->id_curso)
            ->where('estado', 'activo')
            ->with(['user.codigoSie'])
            ->get();

        // 📏 Definimos los anchos de cada columna (total: 260mm aprox)
        $wNum     = 10;
        $wCi      = 23;
        $wNombre  = 78;
        $wUnidad  = 78;
        $wCelular = 23;
        $wFecha   = 39;
        $wCosto   = 25;

        $pdf = new \App\Pdf\CustomPDF('L', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->nombreCurso = $curso->nombre;
        $pdf->codigoCurso = $curso->codigo_curso;
        $pdf->fechaHora = Carbon::now()->format('d/m/Y - h:i:s A');
        $pdf->AddPage();

        // 🧾 Cabecera de tabla
        $drawTableHeader = function () use ($pdf, $wNum, $wCi, $wNombre, $wUnidad, $wCelular, $wFecha, $wCosto) {
            $pdf->Ln(2);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->SetFillColor(30, 30, 30); // negro
            $pdf->SetTextColor(255);       // blanco
            $pdf->SetDrawColor(0);         // borde negro

            $pdf->Cell($wNum, 8, '#', 1, 0, 'C', true);
            $pdf->Cell($wCi, 8, 'CI', 1, 0, 'C', true);
            $pdf->Cell($wNombre, 8, 'NOMBRE COMPLETO', 1, 0, 'C', true);
            $pdf->Cell($wUnidad, 8, utf8_decode('UNIDAD EDUCATIVA'), 1, 0, 'C', true);
            $pdf->Cell($wCelular, 8, 'CELULAR', 1, 0, 'C', true);
            $pdf->Cell($wFecha, 8, utf8_decode('FECHA INSCRIPCIÓN'), 1, 0, 'C', true);
            $pdf->Cell($wCosto, 8, 'COSTO (Bs)', 1, 0, 'C', true);
            $pdf->Ln();

            // Reset estilos
            $pdf->SetFont('Arial', '', 8);
            $pdf->SetTextColor(0); // volver a negro
        };

        $drawTableHeader();

        $precioCurso = $curso->tipo_pago === 'pago' ? ($curso->precio ?? 0) : 0;
        $total = 0;
        $lineHeight = 5;
        $fill = false;

        foreach ($inscritos as $i => $inscrito) {
            $user = $inscrito->user;

            $nombre = mb_strtoupper(trim($user->name . ' ' . $user->primer_apellido . ' ' . $user->segundo_apellido), 'UTF-8');
            $institucion = mb_strtoupper($user->codigoSie->unidad_educativa ?? '---', 'UTF-8');
            $celular = $user->celular ?? '---';

            $nombre = utf8_decode($nombre);
            $institucion = utf8_decode(str_replace(['“', '”', '„', '«', '»'], ['"', '"', '"', '"', '"'], $institucion));
            $celular = utf8_decode($celular);
            $fecha = Carbon::parse($inscrito->fecha_inscripcion)->format('d/m/Y');
            $costo = $precioCurso;
            $total += $costo;

            // Altura dinámica
            $temp = new \Codedge\Fpdf\Fpdf\Fpdf();
            $temp->AddPage('L', 'A4');
            $temp->SetFont('Arial', '', 8);
            $temp->SetXY(10, 10);
            $temp->MultiCell($wNombre, $lineHeight, $nombre);
            $alturaNombre = $temp->GetY() - 10;

            $temp->SetXY(10, 10);
            $temp->MultiCell($wUnidad, $lineHeight, $institucion);
            $alturaInstitucion = $temp->GetY() - 10;

            $cellHeight = max($lineHeight, $alturaNombre, $alturaInstitucion);

            if ($pdf->GetY() + $cellHeight > 190) {
                $pdf->AddPage();
                $drawTableHeader();
            }

            $pdf->SetFillColor($fill ? 240 : 255);
            $x = $pdf->GetX();
            $y = $pdf->GetY();

            // COLUMNAS
            $pdf->SetXY($x, $y);
            $pdf->Cell($wNum, $cellHeight, $i + 1, 1, 0, 'C', true);

            // CI
            $ciCompleto = $user->ci . ($user->complemento_ci ? ' -' . strtoupper($user->complemento_ci) : '');
            $pdf->SetXY($x += $wNum, $y);
            $pdf->Cell($wCi, $cellHeight, $ciCompleto, 1, 0, 'L', true);

            // NOMBRE COMPPLETO
            $pdf->SetXY($x += $wCi, $y);
            $pdf->Rect($x, $y, $wNombre, $cellHeight, 'DF');
            if ($alturaNombre <= $lineHeight + 0.5) {
                $pdf->SetXY($x, $y + ($cellHeight - $lineHeight) / 2);
                $pdf->Cell($wNombre, $lineHeight, $nombre, 0, 0, 'L');
            } else {
                $pdf->SetXY($x, $y);
                $pdf->MultiCell($wNombre, $lineHeight, $nombre, 0, 'L');
            }
            // UNIDADA EDUCATIVA
            $x += $wNombre;
            $pdf->SetXY($x, $y);
            $pdf->Rect($x, $y, $wUnidad, $cellHeight, 'DF');
            if ($alturaInstitucion <= $lineHeight + 0.5) {
                $pdf->SetXY($x, $y + ($cellHeight - $lineHeight) / 2);
                $pdf->Cell($wUnidad, $lineHeight, $institucion, 0, 0, 'L');
            } else {
                $pdf->SetXY($x, $y);
                $pdf->MultiCell($wUnidad, $lineHeight, $institucion, 0, 'L');
            }

            $x += $wUnidad;
            $pdf->SetXY($x, $y);
            $pdf->Cell($wCelular, $cellHeight, $celular, 1, 0, 'C', true);

            $x += $wCelular;
            $pdf->SetXY($x, $y);
            $pdf->Cell($wFecha, $cellHeight, $fecha, 1, 0, 'C', true);

            $x += $wFecha;
            $pdf->SetXY($x, $y);
            $pdf->Cell($wCosto, $cellHeight, number_format($costo, 2, ',', '.'), 1, 0, 'C', true);

            $pdf->SetY($y + $cellHeight);
            $fill = !$fill;
        }

        // Total final
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetFillColor(240, 240, 240);
        $totalCols = $wNum + $wCi + $wNombre + $wUnidad + $wCelular + $wFecha;
        $pdf->Cell($totalCols, 8, 'TOTAL RECAUDADO (Bs)', 1, 0, 'L', true);
        $pdf->Cell($wCosto, 8, number_format($total, 2, ',', '.'), 1, 0, 'C', true);
        $pdf->Ln();

        $pdfContent = $pdf->Output('', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => strlen($pdfContent),
            'Content-Disposition' => 'inline; filename="reporte-inscritos.pdf"',
        ]);
    }

    public function obtenerCursosDisponibles()
    {
        $cursos = DB::table('cursos')
            ->select('uuid_curso', 'nombre', 'codigo_curso')
            ->where('estado', 'activo')
            ->orderBy('nombre', 'asc')
            ->get();

        return response()->json($cursos);
    }
}

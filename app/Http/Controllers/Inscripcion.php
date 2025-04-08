<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User as ModelUser;
use App\Models\Inscripcion as ModelInscripcion;
use App\Models\Curso;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Institucion as ModelInstitucion;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;
use Codedge\Fpdf\Fpdf\Fpdf;

class Inscripcion extends Controller
{
    public function index()
    {
        //------------------------------------------------------------------------------------
        $searchTerm = request()->query('search');
        $perPage = request()->query('perPage', 10);
        $query = ModelInscripcion::query();
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('fecha_inscripcion', 'like', "%{$searchTerm}%")
                    ->orWhere('estado_ins', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%")// Buscar por nombre de usuario
                        ->orWhere('uuid_user', 'like', "%{$searchTerm}%")// Buscar por nombre de usuario
                        ;
                    })
                    ->orWhereHas('curso', function ($q) use ($searchTerm) {
                        $q->where('nombre', 'like', "%{$searchTerm}%");  // Buscar por nombre de curso
                    });

            });
        }
        $inscritos = $query->with('user')->with('curso')->paginate($perPage);
        //------------------------------------------------------------------------------------
        $searchTerm2 = request()->query('searchuser');
        $perPage2 = request()->query('perPage2', 5);
        $query2 = ModelUser::query();
        if ($searchTerm2) {
            $query2->where(function ($q) use ($searchTerm2) {
                $q->where('name', 'like', "%{$searchTerm2}%")
                    ->orWhere('primer_apellido', 'like', "%{$searchTerm2}%")
                    ->orWhere('segundo_apellido', 'like', "%{$searchTerm2}%")
                    ->orWhere('ci', 'like', "%{$searchTerm2}%");
            });
        }
        $usuarios = $query2->paginate($perPage2);
        //------------------------------------------------------------------------------------
        $searchTerm3 = request()->query('searchcurso');
        $perPage3 = request()->query('perPage3', 5);
        $query3 = Curso::query();
        if ($searchTerm3) {
            $query3->where(function ($q) use ($searchTerm3) {
                $q->where('nombre', 'like', "%{$searchTerm3}%")
                    ->orWhere('descripcion', 'like', "%{$searchTerm3}%");
            });
        }
        $cursos = $query3->paginate($perPage3);
        //------------------------------------------------------------------------------------
        return Inertia::render('Inscritos', [
            'inscritos' => $inscritos,
            'filters' => [
                'search' => $searchTerm,
                'perPage' => $perPage
            ],
            'usuarios' => $usuarios,
            'filters2' => [
                'searchuser' => $searchTerm2,
                'perPage2' => $perPage2
            ],
            'cursos' => $cursos,
            'filters3' => [
                'searchcurso' => $searchTerm3,
                'perPage3' => $perPage3
            ]

        ]);
    }

    public function store(Request $request)
    {
        $mensaje=["registro exitoso"];
        $validated = $request->validate([
            'id_user' => 'required',
            'id_curso' => 'required',
        ]);
       // dd( $validated);
        $registroguardar = ModelInscripcion::create([
            'id_user' => $request->id_user,
            'id_curso' => $request->id_curso,
            'uuid_inscripcion' =>Str::uuid(),
            'fecha_inscripcion' => Carbon::now(),
            'estado_ins'=>"inscrito"

        ]);

        // Lógica de guardado (ejemplo)
        return back()
            ->with('success', 'Usuario  inscrito')
            ->with('datos_array', [$mensaje]);
    }

    // App/Http/Controllers/InstitucionController.php
    public function update(Request $request, $id)
    {
        $mensaje=["registro editado"];

        $registro = ModelInscripcion::where('uuid_inscripcion', $id)->firstOrFail();
        //dd($id, $request->id_distrito);
        $validated = $request->validate([
            'id_user' => 'required',
            'id_curso' => 'required',
        ]);

        $registro->update($validated);
        return back()
            ->with('success', 'Inscripcion editada correctamente')
            ->with('datos_array', [$mensaje]);
    }
    public function updatedelete($id, $cod)
    {
        //dd($id, $cod);
        // Buscar la institución o fallar con 404
        // $institucion = ModelInstitucion::findOrFail($id);
        if ($id && $cod) {
            $registro = ModelInscripcion::where('uuid_inscripcion', $id)->firstOrFail();
            // Actualizar el estado
            if ($cod == '1') {
                $registro->update(['estado' => 'activo']);
                return back()->with('editado', 'ok');
            } elseif ($cod == '2') {
                $registro->update(['estado' => 'inactivo']);
                return back()->with('editado', 'ok');
            } else {
                $registro->update(['estado' => 'eliminado']);
                return back()->with('editado', 'ok');
            }
        } else {
            return back()->with('error', 'fue un error');
        }
    }



    public function pdfcurso($uuid)
    {
        $cursoinscrito = ModelInscripcion::where('uuid_inscripcion', $uuid)->firstOrFail();

        // Ruta temporal para el QR
        $qrPath = storage_path('app/public/temp_qr.png');

        // URL del QR
        $url = route('verificar.curso.inscrito', ['uuid' => $cursoinscrito->uuid_inscripcion]);

        // Crear código QR
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        file_put_contents($qrPath, $result->getString());

        // Crear PDF en horizontal (A4 Landscape)
        $pdf = new Fpdf('L', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetTitle(utf8_decode('Certificado de Curso'));

        // Obtener ruta de la imagen desde la base de datos
        $rutaRelativa = $cursoinscrito->curso->img_curso; // ej: 'storage/imagenescursos/imagen.jpg'
        $rutaCompleta = storage_path('app/public/' . Str::after($rutaRelativa, 'storage/'));

        // Colocar imagen de fondo
        if (file_exists($rutaCompleta)) {
            $pdf->Image($rutaCompleta, 0, 0, 297, 210); // Ancho total A4 landscape
        }

        // Texto principal centrado con coordenadas precisas
        $pdf->SetFont('Arial', 'B', 42);
        $pdf->SetXY(0, 95);
        $pdf->Cell(297, 12, utf8_decode($cursoinscrito->user->name . " " . $cursoinscrito->user->primer_apellido ." ". $cursoinscrito->user->segundo_apellido ?? 'Nombre del Participante'), 0, 1, 'C');

        $pdf->SetFont('Arial', '', 14);
        $cursoNombre = $cursoinscrito->curso->nombre ?? 'Curso Desconocido';
        $descripcion = "Por su participacion en el curso: \"$cursoNombre\", realizado en la Federacion Departamental de Trabajadores de Educacion Urbana de La Paz";
        $pdf->SetXY(25, 120);
        $pdf->MultiCell(247, 8, utf8_decode($descripcion), 0, 'C');

        $pdf->SetFont('Arial', '', 14);
        $fechainicio = $cursoinscrito->curso->fecha_inicio ?? 'Fecha';
        $fechafin = $cursoinscrito->curso->fecha_fin ?? 'Fecha';
        $cargahoraria = $cursoinscrito->curso->carga_horaria ?? 'Hora';
        $descripcion = "Realizado en el periodo de: \"$fechainicio\" al \"$fechafin\" con carga horaria de \"$cargahoraria\" Hrs.";
        $pdf->SetXY(25, 140);
        $pdf->MultiCell(247, 8, utf8_decode($descripcion), 0, 'C');

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(0, 150);
        $pdf->Cell(297, 10, utf8_decode('Fecha de emisión: ' . date('d/m/Y')) , 0, 1, 'C');


        // Insertar QR en esquina inferior derecha
        $pdf->Image($qrPath, 250, 35, 35);

        // Eliminar QR temporal
        unlink($qrPath);

        $pdfContent = $pdf->Output('', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => strlen($pdfContent),
            'Content-Disposition' => 'inline; filename="certificado-curso.pdf"',
        ]);
    }

    public function verificarCurso($uuid){
        $inscrito = ModelInscripcion::with(['user', 'curso'])->where('uuid_inscripcion', $uuid)->firstOrFail();
        // dd($inscrito);
        return Inertia::render('Verificacion', [
            'inscrito' => $inscrito
        ]);
    }
}

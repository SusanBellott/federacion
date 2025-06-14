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
use Illuminate\Support\Facades\Auth;
use App\Pdf\CustomPDF;

class Inscripcion extends Controller
{
    function __construct()
    {
         $this->middleware('permission:inscritos.index', ['only' => ['index']]);
         $this->middleware('permission:inscritos.create', ['only' => ['store']]);
         $this->middleware('permission:editarestadodeleteinscritos.update', ['only' => ['updatedelete']]);
         $this->middleware('permission:inscritoeditar.update', ['only' => ['update']]);
         $this->middleware('permission:curso.inscrito.reporte', ['only' => ['pdfcurso']]);
    }

    /**
     * Muestra una lista paginada de inscripciones, con opciones de búsqueda para inscripciones, usuarios y cursos.
     * Los administradores y encargados ven todas las inscripciones, mientras que otros usuarios solo ven las suyas.
     * También incluye listas paginadas de usuarios y cursos para su posible selección o visualización relacionada.
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
          $user = Auth::user(); 
        //------------------------------------------------------------------------------------
        $searchTerm = request()->query('search');
        $perPage = (int)$request->input('perPage', 10);
        $allowedPerPage = [10, 12, 24, 48];
        
        if (!in_array($perPage, $allowedPerPage, false)) {
            $perPage = 10;
        }
        
        $query = ModelInscripcion::where('estado', 'activo');
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
        $user = Auth::user();
        if ($user->hasRole('Administrador') || $user->hasRole('Encargado')) {
            $inscritos = $query->with('user')->with('curso')->paginate($perPage);
        }else{
            $inscritos = $query->with('user')->with('curso')
                                ->where('id_user',$user->id)
                                ->paginate($perPage);
        }


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
        $hoy = Carbon::now();
        $query3 = Curso::where('estado', 'activo') // Asegura que el curso esté activo
            ->whereDate('fecha_inicio_inscripcion', '<=', $hoy)
            ->whereDate('fecha_fin_inscripcion', '>=', $hoy);
// Aplicar filtro de pago según rol
if ($user->hasRole('Estudiante')) {
    $query3->where('tipo_pago', 'gratuito');
} elseif ($user->hasAnyRole(['Administrador', 'Encargado'])) {
    $query3->whereIn('tipo_pago', ['gratuito', 'pago']);
}

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

    /**
     * Almacena una nueva inscripción en la base de datos.
     * Valida que se proporcionen los IDs de usuario y curso, crea un nuevo registro de inscripción con un UUID único,
     * la fecha actual y el estado inicial 'inscrito'.
     *
     * @param  \Illuminate\Http\Request  $request El objeto de la petición HTTP que contiene los IDs de usuario y curso.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $mensaje = ["registro exitoso"];

        $validated = $request->validate([
            'id_user' => 'required',
            'id_curso' => 'required',
        ]);

            // Verifica si ya existe una inscripción activa (estado = 'activo') para el mismo curso y usuario
    $inscripcionActiva = ModelInscripcion::where('id_user', $request->id_user)
    ->where('id_curso', $request->id_curso)
    ->where('estado', 'activo')
    ->first();

if ($inscripcionActiva) {
    return back()->withErrors([
        'id_curso' => 'El usuario ya está inscrito actualmente en este curso.',
    ]);
}

        $curso = Curso::findOrFail($request->id_curso);
        $hoy = Carbon::now();
        
        if (
            $hoy->lt(Carbon::parse($curso->fecha_inicio_inscripcion)) ||
            $hoy->gt(Carbon::parse($curso->fecha_fin_inscripcion))
        ) {
            return back()->withErrors(['id_curso' => 'El curso no está vigente para inscripciones.']);
        }
        

       // dd( $validated);
        $registroguardar = ModelInscripcion::create([
            'id_user' => $request->id_user,
            'id_curso' => $request->id_curso,
            'uuid_inscripcion' =>Str::uuid(),
            'fecha_inscripcion' => Carbon::now(),
            'estado' => 'activo',
            'estado_ins'=>"inscrito",
            'codigo_curso' => $curso->codigo_curso, 
        ]);



        // Lógica de guardado (ejemplo)
        return back()
            ->with('success', 'Usuario  inscrito')
            ->with('datos_array', [$mensaje]);

            
    }

    /**
     * Actualiza la información de una inscripción existente.
     * Valida que se proporcionen los IDs de usuario y curso y actualiza el registro de inscripción correspondiente basado en su UUID.
     *
     * @param  \Illuminate\Http\Request  $request El objeto de la petición HTTP con los datos actualizados de la inscripción.
     * @param  string  $id El UUID único de la inscripción que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Actualiza el estado de una inscripción (activo, inactivo o eliminado) basado en el código proporcionado.
     * Busca la inscripción por su UUID y actualiza la columna 'estado' según el valor de '$cod'.
     *
     * @param  string  $id El UUID único de la inscripción que se va a actualizar.
     * @param  string  $cod El código que indica el nuevo estado de la inscripción ('1' para activo, '2' para inactivo, otro para eliminado).
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /** ESta es la seccion para el reporte en pdf de los cursos inscritos */

public function reporteInscritosPorCurso($uuidCurso)
{
    $curso = Curso::where('uuid_curso', $uuidCurso)->firstOrFail();
    $inscritos = ModelInscripcion::where('id_curso', $curso->id_curso)
        ->where('estado', 'activo')
        ->with(['user.codigoSie'])
        ->get();

    $pdf = new \App\Pdf\CustomPDF('P', 'mm', 'A4');
    $pdf->AliasNbPages();

    // Datos para encabezado
    $pdf->nombreCurso = $curso->nombre;
    $pdf->codigoCurso = $curso->codigo_curso;
    $pdf->fechaHora = Carbon::now()->format('d/m/Y - h:i:s A');

    $pdf->AddPage();

    // 👉 Encabezado de tabla
    $drawTableHeader = function () use ($pdf) {
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(255, 204, 204);
        $pdf->SetDrawColor(200, 0, 0);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->Cell(8, 8, '#', 1, 0, 'C');
        $pdf->Cell(22, 8, 'CI', 1, 0, 'C');
        $pdf->Cell(50, 8, 'NOMBRE COMPLETO', 1, 0, 'C');
        $pdf->Cell(55, 8, utf8_decode('UNIDAD EDUCATIVA'), 1, 0, 'C');
        $pdf->Cell(35, 8, utf8_decode('FECHA INSCRIPCIÓN'), 1, 0, 'C');
        $pdf->Cell(20, 8, 'COSTO (Bs)', 1, 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 8); // 🔁 Reestablecer fuente normal para contenido
    };

    $drawTableHeader();

    $precioCurso = $curso->tipo_pago === 'pago' ? ($curso->precio ?? 0) : 0;
    $total = 0;
    $lineHeight = 5;

    foreach ($inscritos as $i => $inscrito) {
        $user = $inscrito->user;

// Mayúsculas en UTF-8 (conserva tildes y ñ correctamente)
$nombre = mb_strtoupper(trim($user->name . ' ' . $user->primer_apellido . ' ' . $user->segundo_apellido), 'UTF-8');
$institucion = mb_strtoupper($user->codigoSie->unidad_educativa ?? '---', 'UTF-8');

// Decodifica UTF-8 a ISO-8859-1 para que FPDF lo interprete bien
$nombre = utf8_decode($nombre);
$institucion = utf8_decode(str_replace(
    ['“', '”', '„', '«', '»'], // ← comillas especiales
    ['"', '"', '"', '"', '"'], // ← las reemplazamos por comillas normales
    $institucion
));

        $fecha = Carbon::parse($inscrito->fecha_inscripcion)->format('d/m/Y');
        $costo = $precioCurso;
        $total += $costo;

        // Medir altura real de Unidad Educativa
        $temp = new \Codedge\Fpdf\Fpdf\Fpdf();
        $temp->AddPage();
        $temp->SetFont('Arial', '', 8);
        $temp->SetXY(10, 10);
        $temp->MultiCell(55, $lineHeight, $institucion);
        $alturaUnidad = $temp->GetY() - 10;

        $cellHeight = max($lineHeight, $alturaUnidad);

        // Si no hay espacio suficiente, nueva hoja
        if ($pdf->GetY() + $cellHeight > 270) {
            $pdf->AddPage();
            $drawTableHeader();
        }

        $x = $pdf->GetX();
        $y = $pdf->GetY();
// Número
$pdf->SetXY($x, $y);
$pdf->Cell(8, $cellHeight, $i + 1, 1, 0, 'C');

// CI centrado
$ciCompleto = $user->ci . ($user->complemento_ci ? ' -' . strtoupper($user->complemento_ci) : '');
$pdf->SetXY($x + 8, $y);
$pdf->Cell(22, $cellHeight, $ciCompleto, 1, 0, 'L');


// 🔍 Medimos el número de líneas que ocupará el nombre
$temp = new \Codedge\Fpdf\Fpdf\Fpdf();
$temp->AddPage();
$temp->SetFont('Arial', '', 8);
$temp->SetXY(0, 0);
$temp->MultiCell(50, $lineHeight, $nombre);
$nombreHeight = $temp->GetY(); // altura total que ocupa el nombre

// Calculamos el "padding" superior para centrar verticalmente
$paddingTop = ($cellHeight - $nombreHeight) / 2;
$paddingTop = max(0, $paddingTop); // evitar valores negativos

// ⬛ Dibujamos la celda y posicionamos el texto
$pdf->SetXY($x + 30, $y);
$pdf->Rect($x + 30, $y, 50, $cellHeight); // borde

$pdf->SetXY($x + 30, $y + $paddingTop); // desplazamiento vertical para centrar
$pdf->MultiCell(50, $lineHeight, $nombre, 0, 'L'); // 'C' para centrado horizontal


// UNIDAD EDUCATIVA (MultiCell + borde + alineado izquierda pero centrado verticalmente)
$pdf->SetXY($x + 80, $y);
$pdf->Rect($x + 80, $y, 55, $cellHeight);
$pdf->MultiCell(55, $lineHeight, $institucion, 0, 'L');

// FECHA (centrado)
$pdf->SetXY($x + 135, $y);
$pdf->Cell(35, $cellHeight, $fecha, 1, 0, 'C');

// COSTO (centrado a la derecha visualmente)
$pdf->SetXY($x + 170, $y);
$pdf->Cell(20, $cellHeight, number_format($costo, 2, ',', '.'), 1, 0, 'C');

// Avanzar Y para siguiente fila
$pdf->SetY($y + $cellHeight);

    }

    // Total
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(170, 8, 'TOTAL RECAUDADO (Bs)', 1);
    $pdf->Cell(20, 8, number_format($total, 2, ',', '.'), 1);
    $pdf->Ln();

    $pdfContent = $pdf->Output('', 'S');

    return response($pdfContent, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Length' => strlen($pdfContent),
        'Content-Disposition' => 'inline; filename="reporte-inscritos.pdf"',
    ]);
}



    /**
     * Genera un certificado en formato PDF para un curso inscrito específico, incluyendo un código QR para verificación.
     * Busca la inscripción por su UUID, genera un código QR enlazado a la página de verificación,
     * crea un documento PDF en formato horizontal con la información del participante, el curso y la fecha,
     * inserta la imagen del curso como fondo y el código QR en la esquina inferior derecha.
     * Finalmente, devuelve el PDF como una respuesta descargable en el navegador.
     *
     * @param  string  $uuid El UUID único de la inscripción para la cual se generará el certificado.
     * @return \Illuminate\Http\Response
     */
    public function pdfcurso($uuid)
    {
        $cursoinscrito = ModelInscripcion::where('uuid_inscripcion', $uuid)->firstOrFail();

        if (!$cursoinscrito->certificado_numero) {
            // Obtener el máximo para este curso
            $last = ModelInscripcion::where('id_curso', $cursoinscrito->id_curso)
                      ->max('certificado_numero') ?? 0;
            $next = $last + 1;
            $cursoinscrito->certificado_numero = $next;
            $cursoinscrito->save();
        } else {
            $next = $cursoinscrito->certificado_numero;
        }


        $numeroFormateado = str_pad($next, 5, '0', STR_PAD_LEFT);
        $cursoinscrito->load('curso'); // <-- Esto asegura que esté actualizado

        $codigoCurso = $cursoinscrito->curso->codigo_curso ?? 'CURSO';
        $textoCertificado = "{$codigoCurso} - Nro {$numeroFormateado}";

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

        $rawNombre = $cursoinscrito->curso->tipoActividad->nombre 
        ?? $cursoinscrito->curso->tipo;

$tipoTexto = mb_strtoupper($rawNombre, 'UTF-8');

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

// Construye el nombre completo en mayúsculas
$nombreCompleto = mb_strtoupper(
    $cursoinscrito->user->name . ' ' .
    $cursoinscrito->user->primer_apellido . ' ' .
    $cursoinscrito->user->segundo_apellido,
    'UTF-8'
);

// Establecer fuente inicial grande
// Construye el nombre completo en mayúsculas
$nombreCompleto = mb_strtoupper(
    $cursoinscrito->user->name . ' ' .
    $cursoinscrito->user->primer_apellido . ' ' .
    $cursoinscrito->user->segundo_apellido,
    'UTF-8'
);

// Tamaño máximo y mínimo permitido
$fontSize = 25;
$minFontSize = 10;
$maxWidth = 260;

$pdf->SetFont('Arial', 'B', $fontSize);
$nombreWidth = $pdf->GetStringWidth(utf8_decode($nombreCompleto));

// Reducir fuente hasta que quepa en el ancho permitido
while ($nombreWidth > $maxWidth && $fontSize > $minFontSize) {
    $fontSize--;
    $pdf->SetFont('Arial', 'B', $fontSize);
    $nombreWidth = $pdf->GetStringWidth(utf8_decode($nombreCompleto));
}

// Finalmente, imprimir el nombre centrado
$pdf->SetXY(0, 90);
$pdf->Cell(297, 12, utf8_decode($nombreCompleto), 0, 1, 'C');





        $pdf->SetFont('Arial', '', 14);
        $pdf->SetXY(25, 112);
        //$pdf->MultiCell(247, 8, utf8_decode('Por su participacion en el curso:'), 0, 'C');
        $pdf->MultiCell(247, 8, utf8_decode("Por su participación en el {$tipoTexto}"), 0, 'C');

        $pdf->SetFont('Arial', 'B', 15);
        //$cursoNombre = $cursoinscrito->curso->nombre ?? 'Curso Desconocido';
        $cursoNombre = mb_strtoupper($cursoinscrito->curso->nombre ?? 'Curso Desconocido', 'UTF-8');
        $pdf->SetXY(25, 122);
        $pdf->MultiCell(247, 8, utf8_decode($cursoNombre), 0, 'C');
        $y = $pdf->GetY() + 1;
        $pdf->SetFont('Arial', '', 14);
        // $descripcion = "Por su participacion en el curso: \"$cursoNombre\", realizado en la Federacion Departamental de Trabajadores de Educacion Urbana de La Paz";
        $pdf->SetXY(25, $y);
        $pdf->MultiCell(247, 8, utf8_decode('En la Federación Departamental de Trabajadores de Educación Urbana de La Paz'), 0, 'C');
        
        $pdf->SetFont('Arial', '', 14);
        $fechai = Carbon::parse($cursoinscrito->curso->fecha_inicio)->locale('es');
        $fechainciotexto = $fechai->translatedFormat('j \d\e F');
        $fechainicio = $fechainciotexto ?? 'Fecha';
        $fechaf = Carbon::parse($cursoinscrito->curso->fecha_fin)->locale('es');
        $fechafintexto = $fechaf->translatedFormat('j \d\e F');
        $fechafin = $fechafintexto ?? 'Fecha';
        $cargahoraria = $cursoinscrito->curso->carga_horaria ?? 'Hora';
        // Gestion
        $fechag = Carbon::parse($cursoinscrito->curso->fecha_incio)->locale('es');
        $gestion = $fechag->year ?? '2000';
        $cargahoraria = $cursoinscrito->curso->carga_horaria ?? 'Hora';
        $descripcion = "Realizado en el Período de $fechainicio al $fechafin del $gestion con carga horaria de $cargahoraria Hrs.";
        $y = $pdf->GetY() + 1;
        $pdf->SetFont('Arial', '', 14);
        $pdf->SetXY(25, $y);
        $pdf->MultiCell(247, 8, utf8_decode($descripcion), 0, 'C');
        $y = $pdf->GetY() + 1;
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetXY(0, $y);
        $pdf->Cell(297, 10, utf8_decode('Fecha de emisión: ' . date('d/m/Y')) , 0, 1, 'C');


        // Insertar QR en esquina inferior derecha
        $pdf->Image($qrPath, 250, 35, 35);

        $pdf->SetFont('Arial', '', 8);
        // Coloca la posición que desees; por ejemplo abajo a la izquierda:
        $pdf->SetXY(250, 35 + 35 + 2);;
        $pdf->MultiCell(35, 4, utf8_decode($textoCertificado), 0, 'C');
  
        // Eliminar QR temporal
        unlink($qrPath);
  

        $pdfContent = $pdf->Output('', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Length' => strlen($pdfContent),
            'Content-Disposition' => 'inline; filename="certificado-curso.pdf"',
        ]);
    }
    /**
     * Muestra la página de verificación del certificado de un curso inscrito.
     * Busca la inscripción por su UUID, cargando también las relaciones con el usuario y el curso,
     * y luego renderiza el componente de Inertia 'Verificacion' con la información de la inscripción.
     *
     * @param  string  $uuid El UUID único de la inscripción que se va a verificar.
     * @return \Inertia\Response
     */
    public function verificarCurso($uuid){
        $inscrito = ModelInscripcion::with(['user', 'curso'])->where('uuid_inscripcion', $uuid)->firstOrFail();
        // dd($inscrito);
        return Inertia::render('Verificacion', [
            'inscrito' => $inscrito
        ]);
    }
}

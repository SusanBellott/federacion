<?php
/// REPORTE ANUAL DE CURSOS REALIZADOS YA ESTA TESTEAODO NO EDITAR MAS.........
namespace App\Pdf;

use Codedge\Fpdf\Fpdf\Fpdf;

class EncabezadoAnual extends Fpdf
{
    public $tituloReporte = 'REPORTE ANUAL DE CURSOS REALIZADOS';
    public $fechaHora;

    public function Header()
    {
        $logoPath = public_path('assets/img/logo_instituto.png');

        // Logo más pequeño a la izquierda
        $this->Image($logoPath, 10, 8, 18); // 18 mm de ancho

        // Texto institucional
        $this->SetXY(32, 9);
        $this->SetFont('Arial', 'B', 10);
        $this->MultiCell(0, 4.5, utf8_decode("FEDERACIÓN DEPARTAMENTAL DE TRABAJADORES DE EDUCACIÓN URBANA DE LA PAZ"), 0, 'L');

        $this->SetX(32);
        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 4, utf8_decode("F.D.T.E.U.L.P."), 0, 1, 'L');

        // Título del reporte
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, utf8_decode($this->tituloReporte), 0, 1, 'C');

        // Línea horizontal de separación
        $this->SetDrawColor(0);         // color negro
        $this->SetLineWidth(0.3);       // grosor fino
        $this->Line(20, $this->GetY(), 190, $this->GetY());

        // Fecha y hora
        $this->SetFont('Arial', '', 9);
        $this->SetFont('Arial', '', 9);

        $this->Ln(4); // espacio antes del contenido
    }

    public function Footer()
    {
        $this->SetY(-15); // 15 mm desde abajo
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->AliasNbPages();

        // Celda izquierda (fecha y hora)
        $this->Cell(95, 10, utf8_decode("Fecha y hora de reporte: {$this->fechaHora}"), 0, 0, 'L');

        // Celda derecha (número de página)
        $this->Cell(95, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'R');
    }
}

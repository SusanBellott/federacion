<?php

namespace App\Pdf;

use Codedge\Fpdf\Fpdf\Fpdf;

class CustomPDF extends Fpdf
{
    public $nombreCurso;
    public $codigoCurso;
    public $fechaHora;
    public function Header()
    {
        $logoPath = public_path('assets/img/logo_instituto.png');

        // Imagen más pequeña
        $this->Image($logoPath, 60, 5, 20); // ancho de 18mm en lugar de 25

        // Posición más alta y texto más compacto
        $this->SetXY(32, 9); // más alto y más pegado al logo
        $this->SetFont('Arial', 'B', 10); // Tamaño más pequeño
        $this->MultiCell(0, 4.5, utf8_decode("FEDERACIÓN DEPARTAMENTAL DE TRABAJADORES DE EDUCACIÓN URBANA DE LA PAZ"), 0, 'C');

        $this->SetX(32);
        $this->SetFont('Arial', '', 7); // Más pequeño
        $this->Cell(0, 4, utf8_decode("F.D.T.E.U.L.P."), 0, 1, 'C');

        // 👇 NUEVO: información del curso
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 6, utf8_decode("REPORTE DE INSCRITOS AL CURSO"), 0, 1, 'C');

        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 5, utf8_decode("Curso: " . strtoupper("{$this->nombreCurso} ({$this->codigoCurso})")), 0, 1, 'L');
        $this->Cell(0, 5, utf8_decode("Fecha y hora de reporte: {$this->fechaHora}"), 0, 1, 'L');

        $this->Ln(1); //  espacio antes de la tabla
    }
    public function Footer()
    {
        $this->SetY(-15); // Posición 15 mm desde abajo
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120); // Gris claro

        // Si quieres "Página 1 de 5"
        $this->AliasNbPages(); // << Añade esto si usas {nb}
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'C');
    }
}

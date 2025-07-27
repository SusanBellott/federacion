<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Estadisticas extends Controller
{
    public function cursosPorMes($año = null)
{
    $año = $año ?? now()->year;

    // Agrupar cursos por mes (solo "curso", no talleres)
    $data = Curso::whereYear('created_at', $año)
        ->whereHas('tipoActividad', fn($q) => $q->where('nombre', 'Curso'))
        ->get()
        ->groupBy(fn($curso) => Carbon::parse($curso->created_at)->format('F')) // Enero, Febrero...
        ->map(fn($cursos) => $cursos->count());

    return response()->json($data);
}

}

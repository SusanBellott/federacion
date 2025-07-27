<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class Reportes extends Controller
{
    /**
     * Muestra la vista principal de reportes
     */
    public function index()
    {
        return Inertia::render('Reportes');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    protected $primaryKey = 'id_curso';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'uuid_curso',
        'tipo_actividad_id',
        'codigo_curso',
        'nombre',
        'descripcion',
        'carga_horaria',
        'tipo_pago',
        'fecha_inicio',
        'fecha_fin',
        'fecha_inicio_inscripcion',
        'fecha_fin_inscripcion',
        'precio',
        'img_curso',
        'encargado',
        'grado_academico',
        'estado_curso',
        'estado',
        //------------------- todo certificado
        'titulocer',
        'contenidocer',
        'img_cer',
        'img_cerlogo',
        'img_firma1',
        'img_firma2',
        'img_firma3',
        'img_firma4',
        'img_sello'

    ];
    /*
    protected $casts = [
    'fecha_inicio' => 'date',
    'fecha_fin' => 'date',
    'fecha_inicio_inscripcion' => 'date',
    'fecha_fin_inscripcion' => 'date',
];*/

    // Asegura que los atributos virtuales estén incluidos cuando el modelo se convierte a array o JSON
    protected $appends = ['total_inscritos'];

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_curso', 'id_curso');
    }
    public function inscritosActivos()
    {
        return $this->hasMany(Inscripcion::class, 'id_curso')->where('estado', 'activo');
    }

    public function imagencertificados()
    {
        return $this->hasMany(Imagencertificado::class, 'id_curso', 'id_curso');
    }
    /*  public function imagenescer()
    {
        return $this->belongsTo(Imagencertificado::class, 'id_curso','id_curso');
    } */
    public function tipoActividad()
    {
        return $this->belongsTo(TipoActividad::class, 'tipo_actividad_id')->withTrashed();
    }


    public function scopeInscripcionesDisponibles(Builder $query)
    {
        return $query
            ->whereDate('fecha_inicio_inscripcion', '<=', now())
            ->whereDate('fecha_fin_inscripcion', '>=', now());
    }

    public function getEstadoCursoAttribute()
    {
        $fecha_actual = now();
        $inicio_insc = $this->fecha_inicio_inscripcion ? Carbon::parse($this->fecha_inicio_inscripcion) : null;
        $fin_insc = $this->fecha_fin_inscripcion ? Carbon::parse($this->fecha_fin_inscripcion) : null;
        $inicio_curso = $this->fecha_inicio ? Carbon::parse($this->fecha_inicio) : null;
        $fin_curso = $this->fecha_fin ? Carbon::parse($this->fecha_fin) : null;

        if ($inicio_insc && $fecha_actual->lt($inicio_insc)) return 'no iniciado';
        if ($inicio_insc && $fin_insc && $fecha_actual->between($inicio_insc, $fin_insc)) return 'abierto';
        if ($inicio_curso && $fin_curso && $fecha_actual->between($inicio_curso, $fin_curso)) return 'curso';
        if ($fin_curso && $fecha_actual->gt($fin_curso)) return 'terminado';

        return 'cerrado';
    }
    public function getTotalInscritosAttribute()
    {
        // Devuelve el conteo de inscripciones activas asociadas al curso
        return $this->inscripciones()->where('estado', 'activo')->count();
    }
    /*

public function getFechaInicioFormattedAttribute()
{
    return optional($this->fecha_inicio)->format('d/m/Y');
}

public function getFechaFinFormattedAttribute()
{
    return optional($this->fecha_fin)->format('d/m/Y');
}

public function getFechaInicioInscripcionFormattedAttribute()
{
    return optional($this->fecha_inicio_inscripcion)->format('d/m/Y');
}

public function getFechaFinInscripcionFormattedAttribute()
{
    return optional($this->fecha_fin_inscripcion)->format('d/m/Y');
}
*/
}

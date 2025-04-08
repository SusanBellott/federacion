<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    protected $primaryKey = 'id_curso';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'uuid_curso',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'carga_horaria',
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

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id_curso', 'id_curso');
    }
    public function imagencertificados()
    {
        return $this->hasMany(Imagencertificado::class, 'id_curso', 'id_curso');
    }
   /*  public function imagenescer()
    {
        return $this->belongsTo(Imagencertificado::class, 'id_curso','id_curso');
    } */
}

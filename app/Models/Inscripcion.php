<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripcions';
    protected $primaryKey = 'id_inscripcion';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_user', 'id_curso', 'uuid_inscripcion', 'fecha_inscripcion', 'estado_ins', 'estado'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }
}

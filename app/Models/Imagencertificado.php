<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Imagencertificado extends Model
{
    use HasFactory;

    protected $table = 'imagencertificados';
    protected $primaryKey = 'id_imgcer';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [ 'id_curso', 'uuid_imgcer', 'imagenescer','descripcion', 'estado'];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso','id_imgcer');
    }
}

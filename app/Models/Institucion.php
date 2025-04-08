<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Institucion extends Model
{
    use HasFactory;

    protected $table = 'institucions';
    protected $primaryKey = 'id_institucion';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_distrito', 'uuid_institucion', 'subsistema', 'servicio', 'servicio_generado', 'nivel', 'programa', 'unidad_educativa', 'estado'];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'id_distrito', 'id_distrito');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_institucion', 'id_institucion');
    }

}

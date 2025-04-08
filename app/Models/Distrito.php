<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Distrito extends Model
{
    use HasFactory;

    protected $table = 'distritos';
    protected $primaryKey = 'id_distrito';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['uuid_distrito', 'codigo', 'descripcion', 'estado'];

    public function instituciones()
    {
        return $this->hasMany(Institucion::class, 'id_distrito', 'id_distrito');
    }
}

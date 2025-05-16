<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TipoActividad extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipos_actividad';

    protected $fillable = [
        'uuid_tipo_actividad',
        'codigo',
        'nombre',
        'horas_minimas',
        'estado',
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid_tipo_actividad = Str::uuid();
        });
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class, 'tipo_actividad_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Distrito extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'distritos';
    protected $primaryKey = 'id_distrito';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['uuid_distrito', 'codigo', 'descripcion', 'estado'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid_distrito = Str::uuid();
        });
    }
    public function instituciones()
    {
        return $this->hasMany(Institucion::class, 'distrito_id', 'id_distrito');
    }

    public function codigosSie()
    {
        return $this->hasMany(CodigoSie::class); // Laravel usará distrito_id automáticamente
    }


    public function users()
    {
        return $this->hasMany(User::class, 'distrito_id', 'id_distrito');
    }
}

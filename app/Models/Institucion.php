<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\CodigoSie;
use App\Models\Distrito;

class Institucion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'instituciones';
    protected $primaryKey = 'id_institucion';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['uuid_institucion', 'servicio', 'servicio_generado', 'subsistema', 'nivel', 'estado', 'id_distrito'];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid_institucion = Str::uuid();
        });
    }
    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'id_distrito');
    }

    public function codigosSie()
    {
        return $this->hasMany(CodigoSie::class, 'institucion_id', 'id_institucion');
    }


    // If you're using a many-to-many relationship, define it like this:


    public function users()
    {
        return $this->hasMany(User::class, 'institucion_id', 'id_institucion');
    }
}

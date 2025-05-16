<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CodigoSie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'codigo_sies';
    protected $primaryKey = 'id_codigo_sie';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'uuid_codigo_sie',
        'programa',
        'unidad_educativa',
        'estado',
        'distrito_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid_codigo_sie = Str::uuid();
        });
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id'); // ✅ correcto
    }
    
}

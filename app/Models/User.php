<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid_user',
        'ci',
        'rda',
        'name',
        'primer_apellido',
        'segundo_apellido',
        'item',
        'cargo',
        'horas',
        'email',
        'password',
        'estado',

        'distrito_id',
        'institucion_id',
        'codigo_sie_id',
    ];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'distrito_id', 'id_distrito');
    }
    
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id', 'id_institucion');
    }
    
    public function codigoSie()
    {
        return $this->belongsTo(CodigoSie::class, 'codigo_sie_id', 'id_codigo_sie');
    }
    public function getNivelAttribute()
    {
        return $this->institucion ? $this->institucion->nivel : null;
    }
    public function pertenecerADistrito($distritoId)
    {
        return $this->distrito_id == $distritoId;
    }
    public function pertenecerAInstitucion($institucionId)
    {
        return $this->institucion_id == $institucionId;
    }
    public function tenerCodigoSie($codigoSieId)
    {
        return $this->codigo_sie_id == $codigoSieId;
    }
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'id', 'id_user');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid_user = Str::uuid();
        });
    }
}

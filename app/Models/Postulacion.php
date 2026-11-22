<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $candidato_id
 * @property int $vacante_id
 * @property \Illuminate\Support\Carbon $fecha_postulacion
 * @property int $estado_postulacion_id
 * @property int $fuente_reclutamiento_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Candidato $candidato
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Vacante $vacante
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion whereCandidatoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion whereEstadoPostulacionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion whereFechaPostulacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion whereFuenteReclutamientoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Postulacion whereVacanteId($value)
 * @mixin \Eloquent
 */
class Postulacion extends Model
{

    
    use HasFactory;

    protected $table = 'postulaciones';


    protected $fillable =
        [
            'candidato_id',
            'vacante_id',
            'fecha_postulacion',
            'estado_postulacion_id',
            'fuente_reclutamiento_id',
            'user_id'
        ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
            'id' => 'integer',
            'candidato_id' => 'integer',
            'vacante_id' => 'integer',
            'fecha_postulacion' => 'date',
            'estado_postulacion_id' => 'integer',
            'fuente_reclutamiento_id' => 'integer',
            'user_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
        [
            'candidato_id' => 'required|integer',
            'vacante_id' => 'required|integer',
            'fecha_postulacion' => 'required|date',
            'estado_postulacion_id' => 'required|integer',
            'fuente_reclutamiento_id' => 'required|integer',
            'user_id' => 'required|integer',
        ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages =[

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'candidato_id', 'id');
    }

    public function estadoPostulacion()
    {
        return $this->belongsTo(EstadoPostulacion::class, 'estado_postulacion_id', 'id');
    }

    public function fuenteReclutamiento()
    {
        return $this->belongsTo(FuenteReclutamiento::class, 'fuente_reclutamiento_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function vacante()
    {
        return $this->belongsTo(Vacante::class, 'vacante_id', 'id');
    }

    public function historial()
    {
        return $this->hasMany(PostulacionHistorial::class, 'postulacion_id', 'id');
    }

    public function entrevistas()
    {
        return $this->hasMany(Entrevista::class, 'postulacion_id', 'id');
    }

}

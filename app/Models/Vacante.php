<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $unidad_id
 * @property string $titulo
 * @property string $descripcion
 * @property int $modalidad_id
 * @property float $salario_minimo
 * @property float $salario_maximo
 * @property string $ubicacion
 * @property int $estado_vacante_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereEstadoVacanteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereModalidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereSalarioMaximo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereSalarioMinimo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereTitulo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereUbicacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereUnidadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vacante withoutTrashed()
 * @mixin \Eloquent
 */
class Vacante extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'vacantes';


    protected $fillable =
        [
            'unidad_id',
            'titulo',
            'descripcion',
            'modalidad_id',
            'salario_minimo',
            'salario_maximo',
            'ubicacion',
            'estado_vacante_id',
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
            'unidad_id' => 'integer',
            'titulo' => 'string',
            'descripcion' => 'string',
            'modalidad_id' => 'integer',
            'salario_minimo' => 'float',
            'salario_maximo' => 'float',
            'ubicacion' => 'string',
            'estado_vacante_id' => 'integer',
            'user_id' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
        [
            'unidad_id' => 'required|integer',
            'titulo' => 'required|string',
            'descripcion' => 'required|string',
            'modalidad_id' => 'required|integer',
            'salario_minimo' => 'required|numeric',
            'salario_maximo' => 'required|numeric',
            'ubicacion' => 'required|string',
            'estado_vacante_id' => 'required|integer',
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
    public function estadoVacante()
    {
        return $this->belongsTo(EstadoVacante::class, 'estado_vacante_id', 'id');
    }

    public function modalidad()
    {
        return $this->belongsTo(Modalidad::class, 'modalidad_id', 'id');
    }

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function postulaciones()
    {
        return $this->hasMany(Postulacion::class, 'vacante_id', 'id');
    }

}

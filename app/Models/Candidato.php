<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $primer_nombre
 * @property string|null $segundo_nombre
 * @property string|null $tercer_nombre
 * @property string $primer_apellido
 * @property string|null $segundo_apellido
 * @property string|null $apellido_casada
 * @property string $email
 * @property string $telefono
 * @property \Illuminate\Support\Carbon $fecha_nacimiento
 * @property float $expectativa_salarial
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereApellidoCasada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereExpectativaSalarial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereFechaNacimiento($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato wherePrimerApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato wherePrimerNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereSegundoApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereSegundoNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereTercerNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Candidato withoutTrashed()
 * @mixin \Eloquent
 */
class Candidato extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'candidatos';


    protected $fillable =
        [
            'primer_nombre',
            'segundo_nombre',
            'tercer_nombre',
            'primer_apellido',
            'segundo_apellido',
            'apellido_casada',
            'email',
            'telefono',
            'fecha_nacimiento',
            'expectativa_salarial'
        ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
            'id' => 'integer',
            'primer_nombre' => 'string',
            'segundo_nombre' => 'string',
            'tercer_nombre' => 'string',
            'primer_apellido' => 'string',
            'segundo_apellido' => 'string',
            'apellido_casada' => 'string',
            'email' => 'string',
            'telefono' => 'string',
            'fecha_nacimiento' => 'date:Y-m-d',
            'expectativa_salarial' => 'float',
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
            'primer_nombre' => 'required|string',
            'segundo_nombre' => 'nullable|string',
            'tercer_nombre' => 'nullable|string',
            'primer_apellido' => 'required|string',
            'segundo_apellido' => 'nullable|string',
            'apellido_casada' => 'nullable|string',
            'email' => 'required|string|unique:candidatos,email',
            'telefono' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'expectativa_salarial' => 'required|numeric',
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
    

}

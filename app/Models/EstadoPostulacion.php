<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $nombre
 * @property int $orden
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion whereOrden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoPostulacion withoutTrashed()
 * @mixin \Eloquent
 */
class EstadoPostulacion extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'estados_postulacion';


    protected $fillable =
        [
            'nombre',
            'orden'
        ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
            'id' => 'integer',
            'nombre' => 'string',
            'orden' => 'integer',
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
            'nombre' => 'required|string|unique:estados_postulacion,nombre',
            'orden' => 'required|integer',
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

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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EstadoVacante withoutTrashed()
 * @mixin \Eloquent
 */
class EstadoVacante extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'estados_vacante';


    protected $fillable =
        [
            'nombre'
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
            'nombre' => 'required|string|unique:estados_vacante,nombre',
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

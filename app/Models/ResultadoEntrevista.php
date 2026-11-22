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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResultadoEntrevista withoutTrashed()
 * @mixin \Eloquent
 */
class ResultadoEntrevista extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'resultados_entrevista';


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
            'nombre' => 'required|string|unique:resultados_entrevista,nombre',
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

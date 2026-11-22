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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipoEntrevista withoutTrashed()
 * @mixin \Eloquent
 */
class TipoEntrevista extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'tipos_entrevista';


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
            'nombre' => 'required|string|unique:tipos_entrevista,nombre',
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

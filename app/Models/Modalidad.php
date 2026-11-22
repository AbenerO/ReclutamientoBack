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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Modalidad withoutTrashed()
 * @mixin \Eloquent
 */
class Modalidad extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'modalidades';


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
            'nombre' => 'required|string|unique:modalidades,nombre',
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

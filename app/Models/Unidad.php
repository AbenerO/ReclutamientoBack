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
 * @property string|null $descripcion
 * @property string $activo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Unidad withoutTrashed()
 * @mixin \Eloquent
 */
class Unidad extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'unidades';


    protected $fillable =
        [
            'nombre',
            'descripcion',
            'activo'
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
            'descripcion' => 'string',
            'activo' => 'string',
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
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'activo' => 'required|string',
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

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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FuenteReclutamiento withoutTrashed()
 * @mixin \Eloquent
 */
class FuenteReclutamiento extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'fuentes_reclutamiento';


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
            'nombre' => 'required|string|unique:fuentes_reclutamiento,nombre',
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

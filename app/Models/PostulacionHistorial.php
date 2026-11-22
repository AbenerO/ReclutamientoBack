<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostulacionHistorial extends Model
{

    
    use HasFactory;

    protected $table = 'postulacion_historial';

    // La tabla solo tiene created_at (el historial no se modifica)
    const UPDATED_AT = null;


    protected $fillable =
        [
            'postulacion_id',
            'estado_anterior_id',
            'estado_nuevo_id',
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
            'postulacion_id' => 'integer',
            'estado_anterior_id' => 'integer',
            'estado_nuevo_id' => 'integer',
            'user_id' => 'integer',
            'created_at' => 'datetime',
        ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
        [
            'postulacion_id' => 'required|integer',
            'estado_anterior_id' => 'nullable|integer',
            'estado_nuevo_id' => 'required|integer',
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
    public function estadoAnterior()
    {
        return $this->belongsTo(EstadoPostulacion::class, 'estado_anterior_id', 'id');
    }

    public function estadoNuevo()
    {
        return $this->belongsTo(EstadoPostulacion::class, 'estado_nuevo_id', 'id');
    }

    public function postulacion()
    {
        return $this->belongsTo(Postulacion::class, 'postulacion_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}

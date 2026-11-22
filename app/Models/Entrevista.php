<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entrevista extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'entrevistas';


    protected $fillable =
        [
            'postulacion_id',
            'fecha_hora',
            'tipo_entrevista_id',
            'entrevistador_id',
            'resultado_entrevista_id',
            'observaciones',
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
            'fecha_hora' => 'datetime',
            'tipo_entrevista_id' => 'integer',
            'entrevistador_id' => 'integer',
            'resultado_entrevista_id' => 'integer',
            'observaciones' => 'string',
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
            'postulacion_id' => 'required|integer|exists:postulaciones,id',
            'fecha_hora' => 'required|date',
            'tipo_entrevista_id' => 'required|integer|exists:tipos_entrevista,id',
            'entrevistador_id' => 'required|integer|exists:users,id',
            'resultado_entrevista_id' => 'required|integer|exists:resultados_entrevista,id',
            'observaciones' => 'nullable|string|max:500',
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
    public function postulacion()
    {
        return $this->belongsTo(Postulacion::class, 'postulacion_id', 'id');
    }

    public function tipoEntrevista()
    {
        return $this->belongsTo(TipoEntrevista::class, 'tipo_entrevista_id', 'id');
    }

    public function resultadoEntrevista()
    {
        return $this->belongsTo(ResultadoEntrevista::class, 'resultado_entrevista_id', 'id');
    }

    /** Usuario que realiza la entrevista */
    public function entrevistador()
    {
        return $this->belongsTo(User::class, 'entrevistador_id', 'id');
    }

    /** Usuario que programó la entrevista */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}

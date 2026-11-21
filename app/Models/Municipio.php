<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Municipio extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'municipios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'departamento_id',
    ];

    protected $casts = [
        'id'               => 'integer',
        'nombre'           => 'string',
        'descripcion'      => 'string',
        'departamento_id'  => 'integer',
        'created_at'       => 'timestamp',
        'updated_at'       => 'timestamp',
        'deleted_at'       => 'timestamp',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}

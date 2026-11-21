<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departamento extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'departamentos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    protected $casts = [
        'id'         => 'integer',
        'nombre'     => 'string',
        'descripcion' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'departamento_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'canal',
        'destinatario',
        'asunto',
        'cuerpo',
        'estado',
        'error',
        'origen',
        'notificable_type',
        'notificable_id',
        'wamid',
        'enviado_en',
    ];

    protected $casts = [
        'enviado_en' => 'datetime',
    ];

    /**
     * Registro que originó la notificación (opcional).
     */
    public function notificable(): MorphTo
    {
        return $this->morphTo();
    }
}

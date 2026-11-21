<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'wamid', 'direction', 'phone', 'contact_name',
        'type', 'body', 'status', 'payload', 'event_at',
    ];

    protected $casts = [
        'payload'  => 'array',
        'event_at' => 'datetime',
    ];
}

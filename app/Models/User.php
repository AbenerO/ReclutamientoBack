<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $guard_name = 'api';

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'username',
        'primer_nombre',
        'segundo_nombre',
        'tercer_nombre',
        'primer_apellido',
        'segundo_apellido',
        'apellido_casada',
        'dpi',
        'genero',
        'fecha_nacimiento',
        'telefono',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos calculados que se incluyen en toArray() / JSON.
     */
    protected $appends = [
        'nombre_completo',
        'primer_nombre_apellido',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'fecha_nacimiento'  => 'date:Y-m-d',
            'estado'            => 'boolean',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * URL pública del avatar o null si no tiene.
     */
    public function getAvatarAttribute(?string $value): ?string
    {
        return $value ? Storage::disk('public')->url($value) : null;
    }

    /**
     * Nombre completo: todos los nombres y apellidos.
     * Si aún no tiene campos personales, devuelve el campo `name`.
     */
    public function getNombreCompletoAttribute(): string
    {
        if ($this->primer_nombre) {
            return trim(implode(' ', array_filter([
                $this->primer_nombre,
                $this->segundo_nombre,
                $this->tercer_nombre,
                $this->primer_apellido,
                $this->segundo_apellido,
                $this->apellido_casada,
            ])));
        }

        return $this->attributes['name'] ?? '';
    }

    /**
     * Primer nombre + primer apellido (para mostrar en espacios reducidos).
     */
    public function getPrimerNombreApellidoAttribute(): string
    {
        if ($this->primer_nombre && $this->primer_apellido) {
            return $this->primer_nombre . ' ' . $this->primer_apellido;
        }

        return $this->attributes['name'] ?? '';
    }
}

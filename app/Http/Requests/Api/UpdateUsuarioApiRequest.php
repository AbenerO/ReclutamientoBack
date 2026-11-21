<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * El frontend (DualListBox) envía roles/permisos como objetos {id, name, ...}.
     * Los normalizamos a un arreglo de nombres antes de validar y sincronizar.
     */
    protected function prepareForValidation(): void
    {
        foreach (['roles', 'permissions'] as $campo) {
            $valor = $this->input($campo);

            if (is_array($valor)) {
                $this->merge([
                    $campo => collect($valor)
                        ->map(fn ($item) => is_array($item) ? ($item['name'] ?? null) : $item)
                        ->filter()
                        ->values()
                        ->all(),
                ]);
            }
        }
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('usuario');

        return [
            'primer_nombre' => 'required|string|max:100',
            'segundo_nombre' => 'nullable|string|max:100',
            'tercer_nombre' => 'nullable|string|max:100',
            'primer_apellido' => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'apellido_casada' => 'nullable|string|max:100',
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => 'nullable|string|min:8',
            'dpi' => ['nullable', 'string', 'max:13', Rule::unique('users', 'dpi')->ignore($id)],
            'genero' => 'nullable|in:Masculino,Femenino,Otro',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'telefono' => 'nullable|string|max:15',
            'estado' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }
}

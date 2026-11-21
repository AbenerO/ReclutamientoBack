<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PerfilController extends AppBaseController
{
    /**
     * Actualizar información personal del usuario autenticado.
     * PUT /api/perfil
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'email'            => 'required|email|max:255|unique:users,email,' . $user->id,
            'username'         => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'primer_nombre'    => 'required|string|max:100',
            'segundo_nombre'   => 'nullable|string|max:100',
            'tercer_nombre'    => 'nullable|string|max:100',
            'primer_apellido'  => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'apellido_casada'  => 'nullable|string|max:100',
            'dpi'              => 'nullable|string|max:13|unique:users,dpi,' . $user->id,
            'genero'           => 'nullable|in:Masculino,Femenino,Otro',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'telefono'         => 'nullable|string|max:15',
        ]);

        // Mantener `name` sincronizado con el nombre calculado
        $validated['name'] = trim(implode(' ', array_filter([
            $validated['primer_nombre'],
            $validated['primer_apellido'],
        ])));

        $user->update($validated);

        return $this->sendResponse($user->fresh()->toArray(), 'Perfil actualizado con éxito.');
    }

    /**
     * Subir o reemplazar el avatar del usuario autenticado.
     * POST /api/perfil/avatar
     */
    public function subirAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        $user = $request->user();

        $avatarAnterior = $user->getRawOriginal('avatar');
        if ($avatarAnterior) {
            Storage::disk('public')->delete($avatarAnterior);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return $this->sendResponse($user->fresh()->toArray(), 'Foto de perfil actualizada con éxito.');
    }

    /**
     * Eliminar el avatar del usuario autenticado.
     * DELETE /api/perfil/avatar
     */
    public function eliminarAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        $avatarAnterior = $user->getRawOriginal('avatar');
        if ($avatarAnterior) {
            Storage::disk('public')->delete($avatarAnterior);
            $user->update(['avatar' => null]);
        }

        return $this->sendSuccess('Foto de perfil eliminada.');
    }

    /**
     * Cambiar la contraseña del usuario autenticado.
     * PUT /api/perfil/cambiar-password
     */
    public function cambiarPassword(Request $request): JsonResponse
    {
        $request->validate([
            'password_actual'             => 'required|string',
            'nueva_password'              => ['required', 'string', Password::min(8), 'confirmed'],
            'nueva_password_confirmation' => 'required|string',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password_actual, $user->password)) {
            throw ValidationException::withMessages([
                'password_actual' => ['La contraseña actual no es correcta.'],
            ]);
        }

        $user->update(['password' => $request->nueva_password]);

        return $this->sendSuccess('Contraseña actualizada con éxito.');
    }
}

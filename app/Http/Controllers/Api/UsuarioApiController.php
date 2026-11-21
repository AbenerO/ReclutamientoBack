<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\CreateUsuarioApiRequest;
use App\Http\Requests\Api\UpdateUsuarioApiRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

class UsuarioApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver usuarios', only: ['index', 'show', 'getColumnas']),
            new Middleware('permission:crear usuarios', only: ['store']),
            new Middleware('permission:editar usuarios', only: ['update', 'resetPassword']),
            new Middleware('permission:eliminar usuarios', only: ['destroy']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $usuarios = QueryBuilder::for(User::class)
            ->with('roles')
            ->allowedFilters(['primer_nombre', 'primer_apellido', 'username', 'email', 'estado', 'genero'])
            ->allowedSorts(['primer_nombre', 'primer_apellido', 'username', 'email', 'estado', 'created_at'])
            ->defaultSort('-id')
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($usuarios, 'Usuarios recuperados con éxito.');
    }

    public function store(CreateUsuarioApiRequest $request): JsonResponse
    {
        $data = $request->all();

        $data['name'] = trim(implode(' ', array_filter([
            $data['primer_nombre'] ?? '',
            $data['primer_apellido'] ?? '',
        ])));

        if (empty($data['username'])) {
            $data['username'] = $this->generarUsername(
                $data['primer_nombre'] ?? '',
                $data['primer_apellido'] ?? ''
            );
        }

        $anio = date('Y');
        $data['password'] = Hash::make(!empty($data['password']) ? $data['password'] : "Cz{$anio}00");

        DB::beginTransaction();
        try {
            $usuario = User::create($data);

            if (isset($data['roles'])) {
                $usuario->syncRoles($data['roles']);
            }
            if (isset($data['permissions'])) {
                $usuario->syncPermissions($data['permissions']);
            }

            DB::commit();

            return $this->sendResponse(
                $usuario->load('roles')->toArray(),
                'Usuario creado con éxito.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error al crear usuario: ' . $e->getMessage(), 500);
        }
    }

    public function show(User $usuario): JsonResponse
    {
        $data = $usuario->load('roles')->toArray();
        $data['permissions'] = $usuario->getDirectPermissions()->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values()->toArray();
        return $this->sendResponse($data, 'Usuario recuperado con éxito.');
    }

    public function update(UpdateUsuarioApiRequest $request, $id): JsonResponse
    {
        $usuario = User::findOrFail($id);
        $data    = $request->validated();

        $data['name'] = trim(implode(' ', array_filter([
            $data['primer_nombre']   ?? $usuario->primer_nombre,
            $data['primer_apellido'] ?? $usuario->primer_apellido,
        ])));

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        DB::beginTransaction();
        try {
            $usuario->update($data);

            if (array_key_exists('roles', $data)) {
                $usuario->syncRoles($data['roles'] ?? []);
            }
            if (array_key_exists('permissions', $data)) {
                $usuario->syncPermissions($data['permissions'] ?? []);
            }

            DB::commit();

            return $this->sendResponse(
                $usuario->fresh()->load('roles')->toArray(),
                'Usuario actualizado con éxito.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Error al actualizar usuario: ' . $e->getMessage(), 500);
        }
    }

    public function resetPassword(User $usuario): JsonResponse
    {
        $nuevaPassword = 'Cz' . date('Y') . '00';
        $usuario->update(['password' => Hash::make($nuevaPassword)]);

        return $this->sendResponse(
            ['password_generada' => $nuevaPassword],
            "Contraseña restablecida para {$usuario->name}."
        );
    }

    public function destroy(User $usuario): JsonResponse
    {
        $usuario->update(['estado' => false]);
        return $this->sendSuccess('Usuario desactivado con éxito.');
    }

    public function getColumnas(): JsonResponse
    {
        $columnas = Schema::getColumnListing('users');
        return $this->sendResponse($columnas, 'Columnas recuperadas con éxito.');
    }

    private function generarUsername(string $primerNombre, string $primerApellido): string
    {
        $normalizar = fn(string $s) => strtolower(
            preg_replace('/[^a-z0-9]/', '',
                iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s)
            )
        );

        $base     = 'cz' . substr($normalizar($primerNombre), 0, 3) . $normalizar($primerApellido);
        $username = $base;
        $contador = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $contador;
            $contador++;
        }

        return $username;
    }
}
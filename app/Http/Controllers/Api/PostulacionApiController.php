<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreatePostulacionApiRequest;
use App\Http\Requests\Api\UpdatePostulacionApiRequest;
use App\Models\Postulacion;
use App\Models\PostulacionHistorial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class PostulacionApiController
 */
class PostulacionApiController extends AppbaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver postulaciones', only: ['index', 'show']),
            new Middleware('permission:crear postulaciones', only: ['store']),
            new Middleware('permission:editar postulaciones', only: ['update', 'cambiarEstado']),
        ];
    }

    /**
     * Display a listing of the PostulacionTableSeeder.
     * GET|HEAD /postulaciones
     */
    public function index(Request $request): JsonResponse
    {
        $postulaciones = QueryBuilder::for(Postulacion::class)
            ->allowedIncludes(['candidato', 'vacante', 'estadoPostulacion', 'fuenteReclutamiento'])
            ->allowedFilters(['candidato_id', 'vacante_id', 'fecha_postulacion', 'estado_postulacion_id', 'fuente_reclutamiento_id', 'user_id'])
            ->allowedSorts(['candidato_id', 'vacante_id', 'fecha_postulacion', 'estado_postulacion_id', 'fuente_reclutamiento_id', 'user_id'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($postulaciones->toArray(), 'postulaciones recuperados con éxito.');
    }


    /**
     * Store a newly created Postulacion in storage.
     * POST /postulaciones
     */
    public function store(CreatePostulacionApiRequest $request): JsonResponse
    {
        $postulacion = DB::transaction(function () use ($request) {
            $postulacion = Postulacion::create($request->all());

            $this->registrarHistorial($postulacion, null, $postulacion->estado_postulacion_id);

            return $postulacion;
        });

        return $this->sendResponse($postulacion->toArray(), 'Postulacion creada con éxito.');
    }


    /**
     * Display the specified Postulacion.
     * GET|HEAD /postulaciones/{id}
     */
    public function show($id)
    {
        $postulacion = QueryBuilder::for(Postulacion::class)
            ->allowedIncludes(['candidato', 'vacante', 'estadoPostulacion', 'fuenteReclutamiento'])
            ->findOrFail($id);

        return $this->sendResponse($postulacion->toArray(), 'Postulacion recuperada con éxito.');
    }



    /**
    * Update the specified Postulacion in storage.
    * PUT/PATCH /postulaciones/{id}
    */
    public function update(UpdatePostulacionApiRequest $request, $id): JsonResponse
    {
        $postulacion = Postulacion::findOrFail($id);
        $estadoAnterior = $postulacion->estado_postulacion_id;

        DB::transaction(function () use ($request, $postulacion, $estadoAnterior) {
            $postulacion->update($request->validated());

            if ($postulacion->estado_postulacion_id != $estadoAnterior) {
                $this->registrarHistorial($postulacion, $estadoAnterior, $postulacion->estado_postulacion_id);
            }
        });

        return $this->sendResponse($postulacion, 'Postulacion actualizada con éxito.');
    }

    public function cambiarEstado(Request $request, $id): JsonResponse
    {
        $request->validate([
            'estado_postulacion_id' => 'required|integer|exists:estados_postulacion,id',
        ]);

        $postulacion = Postulacion::findOrFail($id);
        $estadoAnterior = $postulacion->estado_postulacion_id;

        if ($postulacion->estado_postulacion_id != $request->estado_postulacion_id) {
            DB::transaction(function () use ($request, $postulacion, $estadoAnterior) {
                $postulacion->update(['estado_postulacion_id' => $request->estado_postulacion_id]);
                $this->registrarHistorial($postulacion, $estadoAnterior, $postulacion->estado_postulacion_id);
            });
        }

        return $this->sendResponse($postulacion->load('estadoPostulacion'), 'Estado actualizado con éxito.');
    }

    private function registrarHistorial(Postulacion $postulacion, $estadoAnterior, $estadoNuevo): void
    {
        PostulacionHistorial::create([
            'postulacion_id' => $postulacion->id,
            'estado_anterior_id' => $estadoAnterior,
            'estado_nuevo_id' => $estadoNuevo,
            'user_id' => usuarioAutenticado()->id,
        ]);
    }

    /**
     * Get columns of the table
     * GET /postulaciones/columns
     */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new Postulacion)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new Postulacion)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'Postulacion',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/' . $nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla postulaciones recuperadas con éxito.');
    }

}

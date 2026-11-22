<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreatePostulacionApiRequest;
use App\Http\Requests\Api\UpdatePostulacionApiRequest;
use App\Models\Postulacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class PostulacionApiController
 */
class PostulacionApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver postulaciones', only: ['index', 'show']),
  //            new Middleware('abilities:crear postulaciones', only: ['store']),
  //            new Middleware('abilities:editar postulaciones', only: ['update']),
  //            new Middleware('abilities:eliminar postulaciones', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the PostulacionTableSeeder.
     * GET|HEAD /postulaciones
     */
    public function index(Request $request): JsonResponse
    {
        $postulaciones = QueryBuilder::for(Postulacion::class)
            ->with([])
            ->allowedFilters(['candidato_id','vacante_id','fecha_postulacion','estado_postulacion_id','fuente_reclutamiento_id','user_id'])
            ->allowedSorts(['candidato_id','vacante_id','fecha_postulacion','estado_postulacion_id','fuente_reclutamiento_id','user_id'])
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
        $input = $request->all();

        $postulaciones = Postulacion::create($input);

        return $this->sendResponse($postulaciones->toArray(), 'Postulacion creado con éxito.');
    }


    /**
     * Display the specified Postulacion.
     * GET|HEAD /postulaciones/{id}
     */
    public function show(Postulacion $postulacion)
    {
        return $this->sendResponse($postulacion->toArray(), 'Postulacion recuperado con éxito.');
    }



    /**
    * Update the specified Postulacion in storage.
    * PUT/PATCH /postulaciones/{id}
    */
    public function update(UpdatePostulacionApiRequest $request, $id): JsonResponse
    {
        $postulacion = Postulacion::findOrFail($id);
        $postulacion->update($request->validated());
        return $this->sendResponse($postulacion, 'Postulacion actualizado con éxito.');
    }

    /**
    * Remove the specified Postulacion from storage.
    * DELETE /postulaciones/{id}
    */
    public function destroy(Postulacion $postulacion): JsonResponse
    {
        $postulacion->delete();
        return $this->sendResponse(null, 'Postulacion eliminado con éxito.');
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
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla postulaciones recuperadas con éxito.');
    }

}

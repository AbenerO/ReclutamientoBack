<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreatePostulacionHistorialApiRequest;
use App\Http\Requests\Api\UpdatePostulacionHistorialApiRequest;
use App\Models\PostulacionHistorial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class PostulacionHistorialApiController
 */
class PostulacionHistorialApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver postulacion_historial', only: ['index', 'show']),
  //            new Middleware('abilities:crear postulacion_historial', only: ['store']),
  //            new Middleware('abilities:editar postulacion_historial', only: ['update']),
  //            new Middleware('abilities:eliminar postulacion_historial', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the PostulacionHistorialTableSeeder.
     * GET|HEAD /postulacion_historial
     */
    public function index(Request $request): JsonResponse
    {
        $postulacion_historial = QueryBuilder::for(PostulacionHistorial::class)
            ->with([])
            ->allowedFilters(['postulacion_id','estado_anterior_id','estado_nuevo_id','user_id'])
            ->allowedSorts(['postulacion_id','estado_anterior_id','estado_nuevo_id','user_id'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($postulacion_historial->toArray(), 'postulacion_historial recuperados con éxito.');
    }


    /**
     * Store a newly created PostulacionHistorial in storage.
     * POST /postulacion_historial
     */
    public function store(CreatePostulacionHistorialApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $postulacion_historial = PostulacionHistorial::create($input);

        return $this->sendResponse($postulacion_historial->toArray(), 'PostulacionHistorial creado con éxito.');
    }


    /**
     * Display the specified PostulacionHistorial.
     * GET|HEAD /postulacion_historial/{id}
     */
    public function show(PostulacionHistorial $postulacionHistorial)
    {
        return $this->sendResponse($postulacionHistorial->toArray(), 'PostulacionHistorial recuperado con éxito.');
    }



    /**
    * Update the specified PostulacionHistorial in storage.
    * PUT/PATCH /postulacion_historial/{id}
    */
    public function update(UpdatePostulacionHistorialApiRequest $request, $id): JsonResponse
    {
        $postulacionHistorial = PostulacionHistorial::findOrFail($id);
        $postulacionHistorial->update($request->validated());
        return $this->sendResponse($postulacionHistorial, 'PostulacionHistorial actualizado con éxito.');
    }

    /**
    * Remove the specified PostulacionHistorial from storage.
    * DELETE /postulacion_historial/{id}
    */
    public function destroy(PostulacionHistorial $postulacionHistorial): JsonResponse
    {
        $postulacionHistorial->delete();
        return $this->sendResponse(null, 'PostulacionHistorial eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /postulacion_historial/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new PostulacionHistorial)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new PostulacionHistorial)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'PostulacionHistorial',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla postulacion_historial recuperadas con éxito.');
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateEstadoPostulacionApiRequest;
use App\Http\Requests\Api\UpdateEstadoPostulacionApiRequest;
use App\Models\EstadoPostulacion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class EstadoPostulacionApiController
 */
class EstadoPostulacionApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver estados_postulacion', only: ['index', 'show']),
  //            new Middleware('abilities:crear estados_postulacion', only: ['store']),
  //            new Middleware('abilities:editar estados_postulacion', only: ['update']),
  //            new Middleware('abilities:eliminar estados_postulacion', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the EstadoPostulacionTableSeeder.
     * GET|HEAD /estados_postulacion
     */
    public function index(Request $request): JsonResponse
    {
        $estados_postulacion = QueryBuilder::for(EstadoPostulacion::class)
            ->with([])
            ->allowedFilters(['nombre','orden'])
            ->allowedSorts(['nombre','orden'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($estados_postulacion->toArray(), 'estados_postulacion recuperados con éxito.');
    }


    /**
     * Store a newly created EstadoPostulacion in storage.
     * POST /estados_postulacion
     */
    public function store(CreateEstadoPostulacionApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $estados_postulacion = EstadoPostulacion::create($input);

        return $this->sendResponse($estados_postulacion->toArray(), 'EstadoPostulacion creado con éxito.');
    }


    /**
     * Display the specified EstadoPostulacion.
     * GET|HEAD /estados_postulacion/{id}
     */
    public function show(EstadoPostulacion $estadoPostulacion)
    {
        return $this->sendResponse($estadoPostulacion->toArray(), 'EstadoPostulacion recuperado con éxito.');
    }



    /**
    * Update the specified EstadoPostulacion in storage.
    * PUT/PATCH /estados_postulacion/{id}
    */
    public function update(UpdateEstadoPostulacionApiRequest $request, $id): JsonResponse
    {
        $estadoPostulacion = EstadoPostulacion::findOrFail($id);
        $estadoPostulacion->update($request->validated());
        return $this->sendResponse($estadoPostulacion, 'EstadoPostulacion actualizado con éxito.');
    }

    /**
    * Remove the specified EstadoPostulacion from storage.
    * DELETE /estados_postulacion/{id}
    */
    public function destroy(EstadoPostulacion $estadoPostulacion): JsonResponse
    {
        $estadoPostulacion->delete();
        return $this->sendResponse(null, 'EstadoPostulacion eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /estados_postulacion/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new EstadoPostulacion)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new EstadoPostulacion)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'EstadoPostulacion',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla estados_postulacion recuperadas con éxito.');
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateEntrevistaApiRequest;
use App\Http\Requests\Api\UpdateEntrevistaApiRequest;
use App\Models\Entrevista;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class EntrevistaApiController
 */
class EntrevistaApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver entrevistas', only: ['index', 'show']),
  //            new Middleware('abilities:crear entrevistas', only: ['store']),
  //            new Middleware('abilities:editar entrevistas', only: ['update']),
  //            new Middleware('abilities:eliminar entrevistas', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the EntrevistaTableSeeder.
     * GET|HEAD /entrevistas
     */
    public function index(Request $request): JsonResponse
    {
        $entrevistas = QueryBuilder::for(Entrevista::class)
            ->with([])
            ->allowedFilters(['postulacion_id','fecha_hora','tipo_entrevista_id','entrevistador_id','resultado_entrevista_id','observaciones','user_id'])
            ->allowedSorts(['postulacion_id','fecha_hora','tipo_entrevista_id','entrevistador_id','resultado_entrevista_id','observaciones','user_id'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($entrevistas->toArray(), 'entrevistas recuperados con éxito.');
    }


    /**
     * Store a newly created Entrevista in storage.
     * POST /entrevistas
     */
    public function store(CreateEntrevistaApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $entrevistas = Entrevista::create($input);

        return $this->sendResponse($entrevistas->toArray(), 'Entrevista creado con éxito.');
    }


    /**
     * Display the specified Entrevista.
     * GET|HEAD /entrevistas/{id}
     */
    public function show(Entrevista $entrevista)
    {
        return $this->sendResponse($entrevista->toArray(), 'Entrevista recuperado con éxito.');
    }



    /**
    * Update the specified Entrevista in storage.
    * PUT/PATCH /entrevistas/{id}
    */
    public function update(UpdateEntrevistaApiRequest $request, $id): JsonResponse
    {
        $entrevista = Entrevista::findOrFail($id);
        $entrevista->update($request->validated());
        return $this->sendResponse($entrevista, 'Entrevista actualizado con éxito.');
    }

    /**
    * Remove the specified Entrevista from storage.
    * DELETE /entrevistas/{id}
    */
    public function destroy(Entrevista $entrevista): JsonResponse
    {
        $entrevista->delete();
        return $this->sendResponse(null, 'Entrevista eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /entrevistas/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new Entrevista)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new Entrevista)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'Entrevista',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla entrevistas recuperadas con éxito.');
    }

}

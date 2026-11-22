<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateResultadoEntrevistaApiRequest;
use App\Http\Requests\Api\UpdateResultadoEntrevistaApiRequest;
use App\Models\ResultadoEntrevista;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ResultadoEntrevistaApiController
 */
class ResultadoEntrevistaApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver resultados_entrevista', only: ['index', 'show']),
  //            new Middleware('abilities:crear resultados_entrevista', only: ['store']),
  //            new Middleware('abilities:editar resultados_entrevista', only: ['update']),
  //            new Middleware('abilities:eliminar resultados_entrevista', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the ResultadoEntrevistaTableSeeder.
     * GET|HEAD /resultados_entrevista
     */
    public function index(Request $request): JsonResponse
    {
        $resultados_entrevista = QueryBuilder::for(ResultadoEntrevista::class)
            ->with([])
            ->allowedFilters(['nombre'])
            ->allowedSorts(['nombre'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($resultados_entrevista->toArray(), 'resultados_entrevista recuperados con éxito.');
    }


    /**
     * Store a newly created ResultadoEntrevista in storage.
     * POST /resultados_entrevista
     */
    public function store(CreateResultadoEntrevistaApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $resultados_entrevista = ResultadoEntrevista::create($input);

        return $this->sendResponse($resultados_entrevista->toArray(), 'ResultadoEntrevista creado con éxito.');
    }


    /**
     * Display the specified ResultadoEntrevista.
     * GET|HEAD /resultados_entrevista/{id}
     */
    public function show(ResultadoEntrevista $resultadoEntrevista)
    {
        return $this->sendResponse($resultadoEntrevista->toArray(), 'ResultadoEntrevista recuperado con éxito.');
    }



    /**
    * Update the specified ResultadoEntrevista in storage.
    * PUT/PATCH /resultados_entrevista/{id}
    */
    public function update(UpdateResultadoEntrevistaApiRequest $request, $id): JsonResponse
    {
        $resultadoEntrevista = ResultadoEntrevista::findOrFail($id);
        $resultadoEntrevista->update($request->validated());
        return $this->sendResponse($resultadoEntrevista, 'ResultadoEntrevista actualizado con éxito.');
    }

    /**
    * Remove the specified ResultadoEntrevista from storage.
    * DELETE /resultados_entrevista/{id}
    */
    public function destroy(ResultadoEntrevista $resultadoEntrevista): JsonResponse
    {
        $resultadoEntrevista->delete();
        return $this->sendResponse(null, 'ResultadoEntrevista eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /resultados_entrevista/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new ResultadoEntrevista)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new ResultadoEntrevista)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'ResultadoEntrevista',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla resultados_entrevista recuperadas con éxito.');
    }

}

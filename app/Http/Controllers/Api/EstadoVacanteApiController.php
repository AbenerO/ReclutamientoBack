<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateEstadoVacanteApiRequest;
use App\Http\Requests\Api\UpdateEstadoVacanteApiRequest;
use App\Models\EstadoVacante;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class EstadoVacanteApiController
 */
class EstadoVacanteApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver estados_vacante', only: ['index', 'show']),
  //            new Middleware('abilities:crear estados_vacante', only: ['store']),
  //            new Middleware('abilities:editar estados_vacante', only: ['update']),
  //            new Middleware('abilities:eliminar estados_vacante', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the EstadoVacanteTableSeeder.
     * GET|HEAD /estados_vacante
     */
    public function index(Request $request): JsonResponse
    {
        $estados_vacante = QueryBuilder::for(EstadoVacante::class)
            ->with([])
            ->allowedFilters(['nombre'])
            ->allowedSorts(['nombre'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($estados_vacante->toArray(), 'estados_vacante recuperados con éxito.');
    }


    /**
     * Store a newly created EstadoVacante in storage.
     * POST /estados_vacante
     */
    public function store(CreateEstadoVacanteApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $estados_vacante = EstadoVacante::create($input);

        return $this->sendResponse($estados_vacante->toArray(), 'EstadoVacante creado con éxito.');
    }


    /**
     * Display the specified EstadoVacante.
     * GET|HEAD /estados_vacante/{id}
     */
    public function show(EstadoVacante $estadoVacante)
    {
        return $this->sendResponse($estadoVacante->toArray(), 'EstadoVacante recuperado con éxito.');
    }



    /**
    * Update the specified EstadoVacante in storage.
    * PUT/PATCH /estados_vacante/{id}
    */
    public function update(UpdateEstadoVacanteApiRequest $request, $id): JsonResponse
    {
        $estadoVacante = EstadoVacante::findOrFail($id);
        $estadoVacante->update($request->validated());
        return $this->sendResponse($estadoVacante, 'EstadoVacante actualizado con éxito.');
    }

    /**
    * Remove the specified EstadoVacante from storage.
    * DELETE /estados_vacante/{id}
    */
    public function destroy(EstadoVacante $estadoVacante): JsonResponse
    {
        $estadoVacante->delete();
        return $this->sendResponse(null, 'EstadoVacante eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /estados_vacante/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new EstadoVacante)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new EstadoVacante)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'EstadoVacante',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla estados_vacante recuperadas con éxito.');
    }

}

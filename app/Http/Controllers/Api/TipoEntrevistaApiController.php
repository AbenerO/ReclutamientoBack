<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateTipoEntrevistaApiRequest;
use App\Http\Requests\Api\UpdateTipoEntrevistaApiRequest;
use App\Models\TipoEntrevista;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class TipoEntrevistaApiController
 */
class TipoEntrevistaApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver tipos_entrevista', only: ['index', 'show']),
  //            new Middleware('abilities:crear tipos_entrevista', only: ['store']),
  //            new Middleware('abilities:editar tipos_entrevista', only: ['update']),
  //            new Middleware('abilities:eliminar tipos_entrevista', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the TipoEntrevistaTableSeeder.
     * GET|HEAD /tipos_entrevista
     */
    public function index(Request $request): JsonResponse
    {
        $tipos_entrevista = QueryBuilder::for(TipoEntrevista::class)
            ->with([])
            ->allowedFilters(['nombre'])
            ->allowedSorts(['nombre'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($tipos_entrevista->toArray(), 'tipos_entrevista recuperados con éxito.');
    }


    /**
     * Store a newly created TipoEntrevista in storage.
     * POST /tipos_entrevista
     */
    public function store(CreateTipoEntrevistaApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $tipos_entrevista = TipoEntrevista::create($input);

        return $this->sendResponse($tipos_entrevista->toArray(), 'TipoEntrevista creado con éxito.');
    }


    /**
     * Display the specified TipoEntrevista.
     * GET|HEAD /tipos_entrevista/{id}
     */
    public function show(TipoEntrevista $tipoEntrevista)
    {
        return $this->sendResponse($tipoEntrevista->toArray(), 'TipoEntrevista recuperado con éxito.');
    }



    /**
    * Update the specified TipoEntrevista in storage.
    * PUT/PATCH /tipos_entrevista/{id}
    */
    public function update(UpdateTipoEntrevistaApiRequest $request, $id): JsonResponse
    {
        $tipoEntrevista = TipoEntrevista::findOrFail($id);
        $tipoEntrevista->update($request->validated());
        return $this->sendResponse($tipoEntrevista, 'TipoEntrevista actualizado con éxito.');
    }

    /**
    * Remove the specified TipoEntrevista from storage.
    * DELETE /tipos_entrevista/{id}
    */
    public function destroy(TipoEntrevista $tipoEntrevista): JsonResponse
    {
        $tipoEntrevista->delete();
        return $this->sendResponse(null, 'TipoEntrevista eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /tipos_entrevista/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new TipoEntrevista)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new TipoEntrevista)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'TipoEntrevista',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla tipos_entrevista recuperadas con éxito.');
    }

}

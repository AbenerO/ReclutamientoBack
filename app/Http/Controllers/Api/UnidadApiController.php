<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateUnidadApiRequest;
use App\Http\Requests\Api\UpdateUnidadApiRequest;
use App\Models\Unidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class UnidadApiController
 */
class UnidadApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver unidades', only: ['index', 'show']),
  //            new Middleware('abilities:crear unidades', only: ['store']),
  //            new Middleware('abilities:editar unidades', only: ['update']),
  //            new Middleware('abilities:eliminar unidades', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the UnidadTableSeeder.
     * GET|HEAD /unidades
     */
    public function index(Request $request): JsonResponse
    {
        $unidades = QueryBuilder::for(Unidad::class)
            ->with([])
            ->allowedFilters(['nombre','descripcion','activo'])
            ->allowedSorts(['nombre','descripcion','activo'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($unidades->toArray(), 'unidades recuperados con éxito.');
    }


    /**
     * Store a newly created Unidad in storage.
     * POST /unidades
     */
    public function store(CreateUnidadApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $unidades = Unidad::create($input);

        return $this->sendResponse($unidades->toArray(), 'Unidad creado con éxito.');
    }


    /**
     * Display the specified Unidad.
     * GET|HEAD /unidades/{id}
     */
    public function show(Unidad $unidad)
    {
        return $this->sendResponse($unidad->toArray(), 'Unidad recuperado con éxito.');
    }



    /**
    * Update the specified Unidad in storage.
    * PUT/PATCH /unidades/{id}
    */
    public function update(UpdateUnidadApiRequest $request, $id): JsonResponse
    {
        $unidad = Unidad::findOrFail($id);
        $unidad->update($request->validated());
        return $this->sendResponse($unidad, 'Unidad actualizado con éxito.');
    }

    /**
    * Remove the specified Unidad from storage.
    * DELETE /unidades/{id}
    */
    public function destroy(Unidad $unidad): JsonResponse
    {
        $unidad->delete();
        return $this->sendResponse(null, 'Unidad eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /unidades/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new Unidad)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new Unidad)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'Unidad',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla unidades recuperadas con éxito.');
    }

}

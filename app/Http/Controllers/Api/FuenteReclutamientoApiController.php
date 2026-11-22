<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateFuenteReclutamientoApiRequest;
use App\Http\Requests\Api\UpdateFuenteReclutamientoApiRequest;
use App\Models\FuenteReclutamiento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class FuenteReclutamientoApiController
 */
class FuenteReclutamientoApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver fuentes_reclutamiento', only: ['index', 'show']),
  //            new Middleware('abilities:crear fuentes_reclutamiento', only: ['store']),
  //            new Middleware('abilities:editar fuentes_reclutamiento', only: ['update']),
  //            new Middleware('abilities:eliminar fuentes_reclutamiento', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the FuenteReclutamientoTableSeeder.
     * GET|HEAD /fuentes_reclutamiento
     */
    public function index(Request $request): JsonResponse
    {
        $fuentes_reclutamiento = QueryBuilder::for(FuenteReclutamiento::class)
            ->with([])
            ->allowedFilters(['nombre'])
            ->allowedSorts(['nombre'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($fuentes_reclutamiento->toArray(), 'fuentes_reclutamiento recuperados con éxito.');
    }


    /**
     * Store a newly created FuenteReclutamiento in storage.
     * POST /fuentes_reclutamiento
     */
    public function store(CreateFuenteReclutamientoApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $fuentes_reclutamiento = FuenteReclutamiento::create($input);

        return $this->sendResponse($fuentes_reclutamiento->toArray(), 'FuenteReclutamiento creado con éxito.');
    }


    /**
     * Display the specified FuenteReclutamiento.
     * GET|HEAD /fuentes_reclutamiento/{id}
     */
    public function show(FuenteReclutamiento $fuenteReclutamiento)
    {
        return $this->sendResponse($fuenteReclutamiento->toArray(), 'FuenteReclutamiento recuperado con éxito.');
    }



    /**
    * Update the specified FuenteReclutamiento in storage.
    * PUT/PATCH /fuentes_reclutamiento/{id}
    */
    public function update(UpdateFuenteReclutamientoApiRequest $request, $id): JsonResponse
    {
        $fuenteReclutamiento = FuenteReclutamiento::findOrFail($id);
        $fuenteReclutamiento->update($request->validated());
        return $this->sendResponse($fuenteReclutamiento, 'FuenteReclutamiento actualizado con éxito.');
    }

    /**
    * Remove the specified FuenteReclutamiento from storage.
    * DELETE /fuentes_reclutamiento/{id}
    */
    public function destroy(FuenteReclutamiento $fuenteReclutamiento): JsonResponse
    {
        $fuenteReclutamiento->delete();
        return $this->sendResponse(null, 'FuenteReclutamiento eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /fuentes_reclutamiento/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new FuenteReclutamiento)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new FuenteReclutamiento)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'FuenteReclutamiento',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla fuentes_reclutamiento recuperadas con éxito.');
    }

}

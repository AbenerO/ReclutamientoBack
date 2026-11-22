<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateModalidadApiRequest;
use App\Http\Requests\Api\UpdateModalidadApiRequest;
use App\Models\Modalidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ModalidadApiController
 */
class ModalidadApiController extends AppbaseController
{

      /**
  //     * @return array
  //     */
  //    public static function middleware(): array
  //    {
  //        return [
  //            new Middleware('abilities:ver modalidades', only: ['index', 'show']),
  //            new Middleware('abilities:crear modalidades', only: ['store']),
  //            new Middleware('abilities:editar modalidades', only: ['update']),
  //            new Middleware('abilities:eliminar modalidades', only: ['destroy']),
  //        ];
  //    }

    /**
     * Display a listing of the ModalidadTableSeeder.
     * GET|HEAD /modalidades
     */
    public function index(Request $request): JsonResponse
    {
        $modalidades = QueryBuilder::for(Modalidad::class)
            ->with([])
            ->allowedFilters(['nombre'])
            ->allowedSorts(['nombre'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($modalidades->toArray(), 'modalidades recuperados con éxito.');
    }


    /**
     * Store a newly created Modalidad in storage.
     * POST /modalidades
     */
    public function store(CreateModalidadApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $modalidades = Modalidad::create($input);

        return $this->sendResponse($modalidades->toArray(), 'Modalidad creado con éxito.');
    }


    /**
     * Display the specified Modalidad.
     * GET|HEAD /modalidades/{id}
     */
    public function show(Modalidad $modalidad)
    {
        return $this->sendResponse($modalidad->toArray(), 'Modalidad recuperado con éxito.');
    }



    /**
    * Update the specified Modalidad in storage.
    * PUT/PATCH /modalidades/{id}
    */
    public function update(UpdateModalidadApiRequest $request, $id): JsonResponse
    {
        $modalidad = Modalidad::findOrFail($id);
        $modalidad->update($request->validated());
        return $this->sendResponse($modalidad, 'Modalidad actualizado con éxito.');
    }

    /**
    * Remove the specified Modalidad from storage.
    * DELETE /modalidades/{id}
    */
    public function destroy(Modalidad $modalidad): JsonResponse
    {
        $modalidad->delete();
        return $this->sendResponse(null, 'Modalidad eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /modalidades/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new Modalidad)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new Modalidad)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'Modalidad',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla modalidades recuperadas con éxito.');
    }

}

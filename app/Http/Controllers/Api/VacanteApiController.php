<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateVacanteApiRequest;
use App\Http\Requests\Api\UpdateVacanteApiRequest;
use App\Models\Vacante;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class VacanteApiController
 */
class VacanteApiController extends AppbaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver vacantes', only: ['index', 'show']),
            new Middleware('permission:crear vacantes', only: ['store']),
            new Middleware('permission:editar vacantes', only: ['update']),
            new Middleware('permission:eliminar vacantes', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the VacanteTableSeeder.
     * GET|HEAD /vacantes
     */
    public function index(Request $request): JsonResponse
    {
        $vacantes = QueryBuilder::for(Vacante::class)
            ->allowedIncludes(['unidad', 'modalidad', 'estadoVacante'])
            ->allowedFilters(['unidad_id','titulo','descripcion','modalidad_id','salario_minimo','salario_maximo','ubicacion','estado_vacante_id','user_id'])
            ->allowedSorts(['unidad_id','titulo','descripcion','modalidad_id','salario_minimo','salario_maximo','ubicacion','estado_vacante_id','user_id'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($vacantes->toArray(), 'vacantes recuperados con éxito.');
    }


    /**
     * Store a newly created Vacante in storage.
     * POST /vacantes
     */
    public function store(CreateVacanteApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $vacantes = Vacante::create($input);

        return $this->sendResponse($vacantes->toArray(), 'Vacante creado con éxito.');
    }


    /**
     * Display the specified Vacante.
     * GET|HEAD /vacantes/{id}
     */
    public function show($id)
    {
        $vacante = QueryBuilder::for(Vacante::class)
            ->allowedIncludes(['unidad', 'modalidad', 'estadoVacante'])
            ->findOrFail($id);

        return $this->sendResponse($vacante->toArray(), 'Vacante recuperado con éxito.');
    }



    /**
    * Update the specified Vacante in storage.
    * PUT/PATCH /vacantes/{id}
    */
    public function update(UpdateVacanteApiRequest $request, $id): JsonResponse
    {
        $vacante = Vacante::findOrFail($id);
        $vacante->update($request->validated());
        return $this->sendResponse($vacante, 'Vacante actualizado con éxito.');
    }

    /**
    * Remove the specified Vacante from storage.
    * DELETE /vacantes/{id}
    */
    public function destroy(Vacante $vacante): JsonResponse
    {
        $vacante->delete();
        return $this->sendResponse(null, 'Vacante eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /vacantes/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new Vacante)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new Vacante)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'Vacante',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla vacantes recuperadas con éxito.');
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateCandidatoApiRequest;
use App\Http\Requests\Api\UpdateCandidatoApiRequest;
use App\Models\Candidato;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class CandidatoApiController
 */
class CandidatoApiController extends AppbaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver candidatos', only: ['index', 'show']),
            new Middleware('permission:crear candidatos', only: ['store']),
            new Middleware('permission:editar candidatos', only: ['update']),
            new Middleware('permission:eliminar candidatos', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the CandidatoTableSeeder.
     * GET|HEAD /candidatos
     */
    public function index(Request $request): JsonResponse
    {
        $candidatos = QueryBuilder::for(Candidato::class)
            ->allowedFilters(['primer_nombre','segundo_nombre','tercer_nombre','primer_apellido','segundo_apellido','apellido_casada','email','telefono','fecha_nacimiento','expectativa_salarial'])
            ->allowedSorts(['primer_nombre','segundo_nombre','tercer_nombre','primer_apellido','segundo_apellido','apellido_casada','email','telefono','fecha_nacimiento','expectativa_salarial'])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($candidatos->toArray(), 'candidatos recuperados con éxito.');
    }


    /**
     * Store a newly created Candidato in storage.
     * POST /candidatos
     */
    public function store(CreateCandidatoApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $candidatos = Candidato::create($input);

        return $this->sendResponse($candidatos->toArray(), 'Candidato creado con éxito.');
    }


    /**
     * Display the specified Candidato.
     * GET|HEAD /candidatos/{id}
     */
    public function show(Candidato $candidato)
    {
        return $this->sendResponse($candidato->toArray(), 'Candidato recuperado con éxito.');
    }



    /**
    * Update the specified Candidato in storage.
    * PUT/PATCH /candidatos/{id}
    */
    public function update(UpdateCandidatoApiRequest $request, $id): JsonResponse
    {
        $candidato = Candidato::findOrFail($id);
        $candidato->update($request->validated());
        return $this->sendResponse($candidato, 'Candidato actualizado con éxito.');
    }

    /**
    * Remove the specified Candidato from storage.
    * DELETE /candidatos/{id}
    */
    public function destroy(Candidato $candidato): JsonResponse
    {
        $candidato->delete();
        return $this->sendResponse(null, 'Candidato eliminado con éxito.');
    }

    /**
    * Get columns of the table
    * GET /candidatos/columns
    */
    public function getColumnas(): JsonResponse
    {

        $columns = Schema::getColumnListing((new Candidato)->getTable());

        $columnasSinTimesTamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $nombreDeTabla = (new Candidato)->getTable();

        $data = [
            'columns' => array_values($columnasSinTimesTamps),
            'nombreDelModelo' => 'Candidato',
            'nombreDeTabla' => $nombreDeTabla,
            'ruta' => 'api/'.$nombreDeTabla,
        ];

        return $this->sendResponse($data, 'Columnas de la tabla candidatos recuperadas con éxito.');
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Municipio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class MunicipioApiController
 */
class MunicipioApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver municipios', only: ['index']),
        ];
    }

    /**
     * Display a listing of the Municipios.
     * GET|HEAD /municipio
     */
    public function index(Request $request): JsonResponse
    {
        $municipios = QueryBuilder::for(Municipio::class)
            ->allowedFilters(['departamento_id'])
            ->allowedSorts(['nombre'])
            ->defaultSort('nombre')
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($municipios->toArray(), 'Municipios recuperados con éxito.');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Departamento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class DepartamentoApiController
 */
class DepartamentoApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver departamentos', only: ['index']),
        ];
    }

    /**
     * Display a listing of the Departamentos.
     * GET|HEAD /departamento
     */
    public function index(Request $request): JsonResponse
    {
        $departamentos = QueryBuilder::for(Departamento::class)
            ->allowedSorts(['nombre'])
            ->defaultSort('nombre')
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($departamentos->toArray(), 'Departamentos recuperados con éxito.');
    }
}

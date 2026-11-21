<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestContext
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::withContext([
            'metodo'  => $request->method(),
            'ruta'    => $request->route()?->uri() ?? $request->path(),
            'url'     => $request->fullUrl(),
            'usuario' => $request->user()
                ? ['name' => $request->user()->name, 'email' => $request->user()->email]
                : null,
        ]);

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class DevController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver logs sistema', only: ['logs']),
            new Middleware('permission:limpiar logs sistema', only: ['limpiarLogs']),
            new Middleware('permission:ver colas sistema', only: ['colas']),
            new Middleware('permission:limpiar colas sistema', only: ['limpiarColas']),
            new Middleware('permission:ver info sistema', only: ['info']),
            new Middleware('permission:ver info sistema', only: ['redis', 'redisBorrarClave']),
        ];
    }

    public function logs(): JsonResponse
    {
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            return $this->sendResponse(['entradas' => [], 'total' => 0, 'size' => '0 KB'], 'No hay logs disponibles.');
        }

        $contenido = file_get_contents($logPath);
        $sizeKb    = round(filesize($logPath) / 1024, 1);

        // Captura cada bloque de log (inicia con [YYYY-MM-DD ...])
        $patron = '/^\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+(\w+)\.(\w+):\s*(.*?)(?=^\[\d{4}-\d{2}-\d{2}|\z)/ms';
        preg_match_all($patron, $contenido, $matches, PREG_SET_ORDER);

        $entradas = array_map(fn($m) => $this->parsearEntradaLog($m), $matches);

        // Más recientes primero, máx 500
        $entradas = array_reverse(array_slice($entradas, -500));

        return $this->sendResponse([
            'entradas' => array_values($entradas),
            'total'    => count($matches),
            'size'     => $sizeKb . ' KB',
        ], 'Logs recuperados.');
    }

    private function parsearEntradaLog(array $m): array
    {
        $fullContent = trim($m[4]);
        $mensaje     = $fullContent;
        $contexto    = null;
        $excepcion   = null;
        $frames      = [];
        $stackRaw    = null;
        $url         = null;

        // Laravel escribe: "Mensaje {json_context}"
        // El JSON empieza en el primer { de nivel raíz
        $jsonInicio = strpos($fullContent, ' {');
        if ($jsonInicio !== false) {
            $posibleJson = substr($fullContent, $jsonInicio + 1);
            $decoded = json_decode($posibleJson, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $mensaje = trim(substr($fullContent, 0, $jsonInicio)) ?: $fullContent;

                // Extraer URL del request si está en el contexto
                $url = $decoded['url'] ?? $decoded['request_url'] ?? null;

                // Contexto sin las claves ya procesadas
                $contextoSinExc = $decoded;
                unset($contextoSinExc['exception'], $contextoSinExc['url'], $contextoSinExc['request_url']);
                $contexto = !empty($contextoSinExc) ? $contextoSinExc : null;

                // Parsear excepción estructurada
                if (!empty($decoded['exception']) && is_string($decoded['exception'])) {
                    $excStr    = $decoded['exception'];
                    $stackRaw  = $excStr;
                    $excepcion = $this->parsearExcepcion($excStr);
                    $frames    = $this->parsearFrames($excStr);
                }
            } else {
                // Fallback: separar por primera línea
                if (str_contains($fullContent, "\n")) {
                    [$primera, $resto] = explode("\n", $fullContent, 2);
                    $mensaje  = trim($primera);
                    $stackRaw = trim($resto);
                }
            }
        } elseif (str_contains($fullContent, "\n")) {
            [$primera, $resto] = explode("\n", $fullContent, 2);
            $mensaje  = trim($primera);
            $stackRaw = trim($resto);
        }

        return [
            'fecha'     => $m[1],
            'env'       => $m[2],
            'nivel'     => strtoupper($m[3]),
            'mensaje'   => $mensaje,
            'url'       => $url,
            'excepcion' => $excepcion,
            'frames'    => $frames,
            'contexto'  => $contexto,
            'stack_raw' => $stackRaw,
        ];
    }

    private function parsearExcepcion(string $excStr): ?array
    {
        // Trabajar solo con la primera línea (antes del [stacktrace])
        $primeraLinea = trim(strtok($excStr, "\n"));

        // Formato: [object] (ClassName(code: N): mensaje at /ruta/archivo.php:linea)
        // Usamos (.+) greedy para el archivo para capturar path completo hasta el último :linea)
        // Anclamos con ^ y $ sobre la primera línea para evitar ambigüedades con el stacktrace
        if (!preg_match(
            '/^\[object\]\s+\((.+?)\(code:\s*(\d+)\):\s*(.*?)\s+at\s+(.+):(\d+)\)$/',
            $primeraLinea,
            $em
        )) {
            return null;
        }

        $claseCompleta   = trim($em[1]);
        $archivoCompleto = trim($em[4]);

        // Nombre corto de la clase (solo la parte final)
        $partes = explode('\\', $claseCompleta);
        $claseCorta = end($partes);

        // Ruta relativa al proyecto
        $base = rtrim(base_path(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $archivoCorto = str_starts_with($archivoCompleto, $base)
            ? substr($archivoCompleto, strlen($base))
            : $archivoCompleto;

        return [
            'clase'         => $claseCompleta,
            'clase_corta'   => $claseCorta,
            'codigo'        => (int) $em[2],
            'mensaje'       => trim($em[3]),
            'archivo'       => $archivoCompleto,
            'archivo_corto' => str_replace('\\', '/', $archivoCorto),
            'linea'         => (int) $em[5],
        ];
    }

    private function parsearFrames(string $excStr): array
    {
        // Cada frame: #N /ruta/archivo.php(linea): Llamada
        // o:          #N {main}
        preg_match_all('/^#(\d+)\s+(.+)$/m', $excStr, $fm, PREG_SET_ORDER);

        $base   = rtrim(base_path(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $frames = [];

        foreach ($fm as $f) {
            $indice = (int) $f[1];
            $resto  = trim($f[2]);

            if ($resto === '{main}') {
                $frames[] = [
                    'indice'        => $indice,
                    'archivo'       => '{main}',
                    'archivo_corto' => '{main}',
                    'linea'         => null,
                    'llamada'       => '{main}',
                    'es_vendor'     => false,
                ];
                continue;
            }

            // /ruta/archivo.php(linea): Llamada
            if (preg_match('/^(.+?)\((\d+)\):\s*(.+)$/', $resto, $pm)) {
                $archivoCompleto = $pm[1];
                $archivoCorto = str_starts_with($archivoCompleto, $base)
                    ? str_replace('\\', '/', substr($archivoCompleto, strlen($base)))
                    : $archivoCompleto;

                $frames[] = [
                    'indice'        => $indice,
                    'archivo'       => $archivoCompleto,
                    'archivo_corto' => $archivoCorto,
                    'linea'         => (int) $pm[2],
                    'llamada'       => trim($pm[3]),
                    'es_vendor'     => str_contains($archivoCorto, 'vendor/') || str_contains($archivoCorto, 'vendor\\'),
                ];
            }
        }

        return $frames;
    }

    public function limpiarLogs(): JsonResponse
    {
        $logPath = storage_path('logs/laravel.log');

        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        return $this->sendSuccess('Log limpiado con éxito.');
    }

    public function colas(): JsonResponse
    {
        $pendientes = DB::table('jobs')->count();
        $totalFallidos = DB::table('failed_jobs')->count();

        $fallidos = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->limit(50)
            ->get()
            ->map(function ($job) {
                $payload = json_decode($job->payload, true) ?? [];
                return [
                    'id'          => $job->id,
                    'uuid'        => $job->uuid,
                    'connection'  => $job->connection,
                    'queue'       => $job->queue,
                    'nombre'      => $payload['displayName'] ?? 'Desconocido',
                    'fallido_en'  => $job->failed_at,
                    'excepcion'   => substr($job->exception, 0, 500),
                ];
            });

        return $this->sendResponse([
            'pendientes'     => $pendientes,
            'total_fallidos' => $totalFallidos,
            'fallidos'       => $fallidos,
        ], 'Estado de colas recuperado.');
    }

    public function limpiarColas(): JsonResponse
    {
        DB::table('failed_jobs')->delete();
        return $this->sendSuccess('Colas fallidas limpiadas con éxito.');
    }

    public function redis(): JsonResponse
    {
        try {
            $redis = Redis::connection();
            $info  = $redis->info();

            // Estadísticas resumidas
            $stats = [
                'version'           => $info['redis_version'] ?? '—',
                'modo'              => $info['redis_mode'] ?? 'standalone',
                'uptime_dias'       => $info['uptime_in_days'] ?? 0,
                'memoria_usada'     => $info['used_memory_human'] ?? '—',
                'memoria_pico'      => $info['used_memory_peak_human'] ?? '—',
                'clientes'          => $info['connected_clients'] ?? 0,
                'total_comandos'    => $info['total_commands_processed'] ?? 0,
                'hits'              => $info['keyspace_hits'] ?? 0,
                'misses'            => $info['keyspace_misses'] ?? 0,
                'claves_expiradas'  => $info['expired_keys'] ?? 0,
            ];

            // Listar claves (máx 500)
            $prefix = config('cache.prefix', '');
            $patron = $prefix ? "{$prefix}*" : '*';
            $claves = $redis->keys($patron);
            sort($claves);
            $claves = array_slice($claves, 0, 500);

            $detalles = array_map(function (string $clave) use ($redis) {
                $tipo = $redis->type($clave);
                $ttl  = $redis->ttl($clave);
                $valor = null;

                try {
                    $valor = match ($tipo) {
                        'string' => $redis->get($clave),
                        'list'   => $redis->lrange($clave, 0, 9),
                        'set'    => $redis->smembers($clave),
                        'zset'   => $redis->zrange($clave, 0, 9, ['withscores' => true]),
                        'hash'   => $redis->hgetall($clave),
                        default  => null,
                    };

                    // Intentar decodificar JSON para mostrarlo legible
                    if (is_string($valor) && str_starts_with(ltrim($valor), '{')) {
                        $decoded = json_decode($valor, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $valor = $decoded;
                        }
                    }

                    // Truncar valores muy largos
                    $valorStr = is_string($valor) ? $valor : json_encode($valor, JSON_UNESCAPED_UNICODE);
                    if (strlen($valorStr) > 500) {
                        $valor = substr($valorStr, 0, 500) . '… [truncado]';
                    }
                } catch (\Throwable) {
                    $valor = '(error al leer)';
                }

                return [
                    'clave' => $clave,
                    'tipo'  => $tipo,
                    'ttl'   => $ttl,
                    'valor' => $valor,
                ];
            }, $claves);

            return $this->sendResponse([
                'stats'    => $stats,
                'total'    => count($claves),
                'claves'   => $detalles,
            ], 'Redis info recuperada.');
        } catch (\Throwable $e) {
            return $this->sendError('No se pudo conectar a Redis: ' . $e->getMessage(), 503);
        }
    }

    public function redisBorrarClave(string $clave): JsonResponse
    {
        try {
            Redis::connection()->del($clave);
            return $this->sendSuccess("Clave '{$clave}' eliminada.");
        } catch (\Throwable $e) {
            return $this->sendError('Error al eliminar clave: ' . $e->getMessage(), [], 500);
        }
    }

    public function info(): JsonResponse
    {
        $dbConfig = config('database.connections.' . config('database.default'));

        return $this->sendResponse([
            'app' => [
                'nombre'  => config('app.name'),
                'entorno' => app()->environment(),
                'debug'   => config('app.debug') ? 'Habilitado' : 'Deshabilitado',
                'url'     => config('app.url'),
                'version' => app()->version(),
            ],
            'sistema' => [
                'php'         => PHP_VERSION,
                'servidor'    => php_uname('s') . ' ' . php_uname('r'),
                'timezone'    => config('app.timezone'),
                'locale'      => config('app.locale'),
            ],
            'base_datos' => [
                'driver'   => config('database.default'),
                'host'     => $dbConfig['host'] ?? '—',
                'nombre'   => $dbConfig['database'] ?? '—',
                'charset'  => $dbConfig['charset'] ?? '—',
            ],
            'servicios' => [
                'queue'  => config('queue.default'),
                'cache'  => config('cache.default'),
                'mail'   => config('mail.default'),
                'sesion' => config('session.driver'),
            ],
        ], 'Información del sistema.');
    }
}
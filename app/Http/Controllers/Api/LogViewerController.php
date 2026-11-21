<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LogViewerController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver logs sistema', only: ['menu', 'show', 'download', 'delete']),
        ];
    }

    protected array $levelKeys = [
        'all', 'emergency', 'alert', 'critical',
        'error', 'warning', 'notice', 'info', 'debug',
    ];

    // ─── Listado por fecha ─────────────────────────────────────────────────────

    public function menu(Request $request): JsonResponse
    {
        $items      = $this->getMenu();
        $pageSize   = max(1, (int) $request->input('page.size', 15));
        $pageNumber = max(1, (int) $request->input('page.number', 1));

        $paginator = new LengthAwarePaginator(
            $items->slice(($pageNumber - 1) * $pageSize, $pageSize)->values(),
            $items->count(),
            $pageSize,
            $pageNumber,
        );

        return $this->sendResponse($paginator, 'Listado de logs.');
    }

    private function getMenu(): Collection
    {
        $canales = ['laravel', 'frontend'];
        $byFecha = [];

        foreach ($canales as $canal) {
            // Archivos daily: laravel-YYYY-MM-DD.log
            $archivos = glob(storage_path("logs/{$canal}-*.log")) ?: [];
            foreach ($archivos as $archivo) {
                $base = basename($archivo);
                if (!preg_match("/^{$canal}-(\d{4}-\d{2}-\d{2})\.log$/", $base, $m)) {
                    continue;
                }
                $fecha   = $m[1];
                $this->acumularEnFecha($byFecha, $fecha, $canal, $archivo);
            }

            // Archivo single: laravel.log o frontend.log (sin fecha)
            $single = storage_path("logs/{$canal}.log");
            if (file_exists($single)) {
                $fecha = date('Y-m-d', filemtime($single));
                $this->acumularEnFecha($byFecha, $fecha, $canal, $single);
            }
        }

        return collect(array_values($byFecha))->sortByDesc('fecha')->values();
    }

    private function acumularEnFecha(array &$byFecha, string $fecha, string $canal, string $archivo): void
    {
        $conteos = $this->countLevelsByFile($archivo);

        if (!isset($byFecha[$fecha])) {
            $byFecha[$fecha] = array_merge(
                ['fecha' => $fecha, 'channels' => []],
                array_fill_keys($this->levelKeys, ['count' => 0])
            );
        }

        if (!in_array($canal, $byFecha[$fecha]['channels'])) {
            $byFecha[$fecha]['channels'][] = $canal;
        }

        foreach ($this->levelKeys as $key) {
            $byFecha[$fecha][$key]['count'] += $conteos[$key] ?? 0;
        }
    }

    private function countLevelsByFile(string $path): array
    {
        $counts = array_fill_keys($this->levelKeys, 0);

        if (!file_exists($path)) return $counts;

        $fh = @fopen($path, 'r');
        if (!$fh) return $counts;

        while (($line = fgets($fh)) !== false) {
            $line = str_replace(["\r", "\n"], '', $line);
            if (preg_match('/\[[^\]]+\]\s+\w+\.(\w+):/i', $line, $m)) {
                $nivel = strtolower($m[1]);
                if (array_key_exists($nivel, $counts)) {
                    $counts[$nivel]++;
                }
                $counts['all']++;
            }
        }

        fclose($fh);
        return $counts;
    }

    // ─── Detalle de un día ─────────────────────────────────────────────────────

    public function show(string $date, Request $request): JsonResponse
    {
        $channel = $request->input('channel', 'laravel');
        $logPath = $this->getLogPath($channel, $date);

        if (!$logPath) {
            return $this->sendError("No se encontró el log para la fecha {$date} y canal {$channel}.", 404);
        }

        $allLogs = $this->parseLogFile($logPath);

        // Filtrar por nivel
        $level = strtolower($request->input('level', 'all'));
        if ($level !== 'all') {
            $allLogs = $allLogs->filter(fn($log) => strtolower($log['level']) === $level)->values();
        }

        // Estadísticas del archivo completo (sin filtro de nivel)
        $stats = $this->countLevelsByFile($logPath);

        // Paginación
        $pageSize   = max(1, (int) $request->input('page.size', 10));
        $pageNumber = max(1, (int) $request->input('page.number', 1));
        $total      = $allLogs->count();
        $sliced     = $allLogs->slice(($pageNumber - 1) * $pageSize, $pageSize)->values();

        $paginator = new LengthAwarePaginator($sliced, $total, $pageSize, $pageNumber);

        return $this->sendResponse([
            'date'        => $date,
            'environment' => config('app.env'),
            'logs'        => $sliced,
            'stats'       => $stats,
            'pagination'  => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem() ?? 0,
                'to'           => $paginator->lastItem() ?? 0,
            ],
        ], 'Logs recuperados.');
    }

    private function getLogPath(string $channel, string $date): ?string
    {
        // Primero busca daily: laravel-YYYY-MM-DD.log
        $daily = storage_path("logs/{$channel}-{$date}.log");
        if (file_exists($daily)) return $daily;

        // Fallback: archivo single (laravel.log) si su mtime coincide con la fecha
        $single = storage_path("logs/{$channel}.log");
        if (file_exists($single) && date('Y-m-d', filemtime($single)) === $date) {
            return $single;
        }

        return null;
    }

    private function parseLogFile(string $path): Collection
    {
        // Leer contenido completo y normalizar saltos de línea (Windows CRLF → LF)
        $contents = file_get_contents($path) ?: '';
        // Eliminar BOM UTF-8 si existe
        $contents = ltrim($contents, "\xEF\xBB\xBF");
        $contents = str_replace("\r\n", "\n", $contents);
        $contents = str_replace("\r", "\n", $contents);
        $lines = explode("\n", $contents);

        $entries = [];
        $current = null;

        foreach ($lines as $line) {
            if (preg_match('/^\[\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}/', $line)) {
                if ($current !== null) {
                    $entries[] = $this->parseLogEntry($current);
                }
                $current = $line;
            } elseif ($current !== null) {
                $current .= "\n" . $line;
            }
        }

        if ($current !== null) {
            $entries[] = $this->parseLogEntry($current);
        }

        return collect(array_reverse($entries));
    }

    private function parseLogEntry(string $raw): array
    {
        $raw = trim($raw);

        // ── 1. Extraer datetime y level de la primera línea únicamente ────────
        $firstNl   = strpos($raw, "\n");
        $firstLine = $firstNl !== false ? substr($raw, 0, $firstNl) : $raw;
        $restLines = $firstNl !== false ? substr($raw, $firstNl + 1) : '';

        $datetime = null;
        $level    = 'debug';
        $msgFirst = $raw;

        if (preg_match('/^\[([^\]]+)\]\s+[\w\-\.]+\.(\w+):\s*(.*)$/i', $firstLine, $m)) {
            $datetime = $m[1];
            $level    = strtolower($m[2]);
            $msgFirst = $m[3];
        }

        // Cuerpo completo = mensaje de la 1ª línea + líneas restantes
        $fullBody = $restLines !== '' ? $msgFirst . "\n" . $restLines : $msgFirst;
        $fullBody = rtrim($fullBody);
        // Quitar sufijo " []" que Monolog añade al final
        $fullBody = preg_replace('/\s+\[\]\s*$/', '', $fullBody);

        $header  = $fullBody;
        $stack   = null;
        $context = null;

        // ── 2. Separar el contexto JSON ───────────────────────────────────────
        // Monolog lo añade como ' {"key":...}' inmediatamente después del mensaje.
        // NOTA: cuando el valor "exception" contiene saltos de línea reales (JSON inválido),
        //       el marcador [stacktrace] aparece DENTRO del raw JSON, no fuera.
        $jsonPos = strpos($fullBody, ' {"');
        if ($jsonPos !== false) {
            $header  = rtrim(substr($fullBody, 0, $jsonPos));
            $rawJson = substr($fullBody, $jsonPos + 1); // desde '{'

            // Intentar decodificar (funciona cuando el JSON es válido)
            $decoded = json_decode($rawJson, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $context = $decoded;
                if (isset($context['trace'])) { $stack = $context['trace']; unset($context['trace']); }
                unset($context['exception']); // evitar duplicar stack trace
            } else {
                // JSON inválido (Monolog escribe \n reales en el valor de "exception")
                // Extraemos los campos conocidos con regex tolerante a multilínea
                $context = [];

                if (preg_match('/"userId"\s*:\s*(\d+)/', $rawJson, $mu)) {
                    $context['userId'] = (int)$mu[1];
                }
                if (preg_match('/"url"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/s', $rawJson, $mu)) {
                    $context['url'] = json_decode('"' . $mu[1] . '"') ?? $mu[1];
                }
                if (preg_match('/"metodo"\s*:\s*"([A-Z]+)"/', $rawJson, $mu)) {
                    $context['metodo'] = $mu[1];
                }
                if (preg_match('/"ruta"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/', $rawJson, $mu)) {
                    $context['ruta'] = json_decode('"' . $mu[1] . '"') ?? $mu[1];
                }
                if (preg_match('/"archivo"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/', $rawJson, $mu)) {
                    $context['archivo'] = json_decode('"' . $mu[1] . '"') ?? $mu[1];
                }
                // usuario: objeto {"name":"...","email":"..."}
                if (preg_match('/"usuario"\s*:\s*\{([^}]+)\}/', $rawJson, $mu)) {
                    $u = json_decode('{' . $mu[1] . '}', true);
                    if ($u) $context['usuario'] = $u;
                }
                if (preg_match('/"request"\s*:\s*(\{[^}]+\})/s', $rawJson, $mu)) {
                    $ctx = json_decode($mu[1], true);
                    if ($ctx) $context['request'] = $ctx;
                }
            }

            // El stack trace está entre "[stacktrace]" y el cierre del JSON ("} [])
            $stPos = strpos($rawJson, "[stacktrace]");
            if ($stPos !== false) {
                $stackRaw = substr($rawJson, $stPos + strlen("[stacktrace]"));
                // Quitar el cierre del string JSON ('"} []' o similar) al final
                $stackRaw = preg_replace('/\s*"\s*\}\s*(\[\])?.*$/s', '', $stackRaw);
                $stack    = trim($stackRaw);
            }
        } elseif (($stPos = strpos($fullBody, "\n[stacktrace]")) !== false) {
            // Caso sin JSON context pero con [stacktrace] directo en el body
            $header = rtrim(substr($fullBody, 0, $stPos));
            $stack  = trim(substr($fullBody, $stPos + strlen("\n[stacktrace]")));
        }

        return compact('datetime', 'level', 'header', 'stack', 'context');
    }

    // ─── Descarga ──────────────────────────────────────────────────────────────

    public function download(string $date, Request $request)
    {
        $channel = $request->input('channel', 'laravel');
        $path    = $this->getLogPath($channel, $date);

        if (!$path) {
            return $this->sendError('Archivo no encontrado.', 404);
        }

        return response()->download($path, basename($path));
    }

    // ─── Eliminar ──────────────────────────────────────────────────────────────

    public function delete(Request $request): JsonResponse
    {
        $fecha   = $request->input('fecha');
        $channel = $request->input('channel', 'laravel');

        if (!$fecha) {
            return $this->sendError('El campo fecha es requerido.', 422);
        }

        $path = $this->getLogPath($channel, $fecha);

        if (!$path) {
            return $this->sendError('Archivo no encontrado.', 404);
        }

        @unlink($path);
        return $this->sendResponse(true, 'Log eliminado exitosamente.');
    }

    // ─── Logs del frontend ─────────────────────────────────────────────────────

    public function frontendLogs(Request $request): JsonResponse
    {
        try {
            Log::channel('frontend')->error($request->input('message', 'Frontend error'), [
                'type'      => $request->input('type'),
                'stack'     => str_replace("\n", ' | ', (string) $request->input('stack', '')),
                'info'      => $request->input('info'),
                'url'       => $request->input('url'),
                'userAgent' => $request->input('userAgent'),
                'timestamp' => $request->input('timestamp'),
                'usuario'   => $request->user()?->toArray(),
            ]);

            return $this->sendResponse(true, 'Log registrado.');
        } catch (\Throwable $e) {
            return $this->sendError('Error al registrar log: ' . $e->getMessage(), 500);
        }
    }
}

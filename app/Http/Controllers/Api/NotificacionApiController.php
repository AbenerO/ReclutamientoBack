<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Notificacion;
use App\Services\NotificacionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Schema;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Bitácora de notificaciones enviadas (correo / WhatsApp) y envío de pruebas.
 */
class NotificacionApiController extends AppBaseController implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver notificaciones', only: ['index', 'show', 'getColumnas']),
            new Middleware('permission:enviar notificaciones', only: ['enviarPrueba']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $notificaciones = QueryBuilder::for(Notificacion::class)
            ->allowedFilters([
                'canal',
                'estado',
                'origen',
                'destinatario',
                AllowedFilter::partial('asunto'),
            ])
            ->allowedSorts(['id', 'canal', 'estado', 'origen', 'enviado_en', 'created_at'])
            ->defaultSort('-id')
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($notificaciones->toArray(), 'Notificaciones recuperadas con éxito.');
    }

    public function show(Notificacion $notificacion): JsonResponse
    {
        return $this->sendResponse($notificacion->toArray(), 'Notificación recuperada con éxito.');
    }

    /**
     * Envía un mensaje de prueba por el canal indicado.
     * POST /configuracion/notificacion/prueba
     */
    public function enviarPrueba(Request $request, NotificacionService $service): JsonResponse
    {
        $data = $request->validate([
            'canal'        => 'required|in:email,whatsapp',
            'destinatario' => 'required|string|max:255',
            'asunto'       => 'nullable|string|max:255',
            'mensaje'      => 'nullable|string|max:4000',
            // WhatsApp: si se indica plantilla se envía como plantilla aprobada por Meta;
            // si no, como texto libre (solo funciona dentro de la ventana de 24 h).
            'plantilla'    => 'nullable|string|max:255',
            'parametros'   => 'nullable|array',
            'parametros.*' => 'string|max:255',
        ]);

        if ($data['canal'] === 'email') {
            $validador = validator($data, ['destinatario' => 'email']);
            if ($validador->fails()) {
                return $this->sendError('El destinatario debe ser un correo electrónico válido.', 422);
            }

            if (empty($data['mensaje'])) {
                return $this->sendError('El mensaje es requerido para correo.', 422);
            }

            $notificacion = $service->enviarEmail(
                $data['destinatario'],
                $data['asunto'] ?? null,
                $data['mensaje'],
                ['origen' => 'prueba'],
            );
        } else {
            $telefono = preg_replace('/[^0-9]/', '', $data['destinatario']);
            if (strlen($telefono) < 8) {
                return $this->sendError('El teléfono no es válido. Incluya el código de país (ej. 502XXXXXXXX).', 422);
            }

            if (! empty($data['plantilla'])) {
                $parametros = $data['parametros'] ?? [];
                $notificacion = $service->enviarWhatsappPlantilla(
                    $telefono,
                    $data['plantilla'],
                    $parametros,
                    "Prueba de plantilla: {$data['plantilla']}".($parametros ? ' ('.implode(', ', $parametros).')' : ''),
                    ['origen' => 'prueba'],
                );
            } else {
                if (empty($data['mensaje'])) {
                    return $this->sendError('Indique una plantilla o un mensaje para WhatsApp.', 422);
                }

                $notificacion = $service->enviarWhatsapp($telefono, $data['mensaje'], ['origen' => 'prueba']);
            }
        }

        if ($notificacion->estado === 'fallido') {
            return $this->sendError(
                'No se pudo enviar el mensaje: '.($notificacion->error ?? 'error desconocido'),
                422,
            );
        }

        return $this->sendResponse($notificacion->toArray(), 'Mensaje de prueba enviado correctamente.');
    }

    public function getColumnas(): JsonResponse
    {
        $columns = Schema::getColumnListing((new Notificacion)->getTable());
        $columnasSinTimestamps = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);

        $data = [
            'columns' => array_values($columnasSinTimestamps),
            'nombreDelModelo' => 'Notificacion',
            'nombreDeTabla' => (new Notificacion)->getTable(),
            'ruta' => 'api/configuracion/notificacion',
        ];

        return $this->sendResponse($data, 'Columnas de la tabla notificaciones recuperadas con éxito.');
    }
}

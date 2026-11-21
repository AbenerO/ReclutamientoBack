<?php

namespace App\Services;

use App\Mail\NotificacionGenericaMail;
use App\Models\Notificacion;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Punto único de envío de notificaciones (correo / WhatsApp).
 * Registra cada intento en la tabla `notificaciones` para la bitácora.
 */
class NotificacionService
{
    public function __construct(protected WhatsAppService $whatsapp) {}

    /**
     * Envía un correo genérico (usado por el módulo de pruebas y envíos manuales).
     *
     * @param  array{origen?: string, notificable?: \Illuminate\Database\Eloquent\Model|null}  $meta
     */
    public function enviarEmail(string $destinatario, ?string $asunto, string $cuerpo, array $meta = []): Notificacion
    {
        $asunto = $asunto ?: 'Notificación - '.config('app.name');

        return $this->enviarMailable(
            new NotificacionGenericaMail($asunto, $cuerpo),
            $destinatario,
            $cuerpo,
            $asunto,
            $meta,
        );
    }

    /**
     * Envía un Mailable ya construido y lo registra en la bitácora.
     * Permite reutilizar plantillas de correo existentes (cualquier Mailable).
     *
     * @param  array{origen?: string, notificable?: \Illuminate\Database\Eloquent\Model|null}  $meta
     */
    public function enviarMailable(Mailable $mailable, string $destinatario, string $cuerpo, ?string $asunto, array $meta = []): Notificacion
    {
        $notificacion = $this->crear('email', $destinatario, $cuerpo, $asunto, $meta);

        try {
            Mail::to($destinatario)->send($mailable);
            $notificacion->update(['estado' => 'enviado', 'enviado_en' => now()]);
        } catch (\Throwable $e) {
            Log::error('NotificacionService: error enviando correo', ['error' => $e->getMessage()]);
            $notificacion->update(['estado' => 'fallido', 'error' => $e->getMessage()]);
        }

        return $notificacion;
    }

    /**
     * Envía un mensaje de WhatsApp usando una plantilla aprobada por Meta y lo registra en la bitácora.
     *
     * @param  string[]  $parametros  Valores de {{1}}, {{2}}, {{3}}... de la plantilla
     * @param  array{origen?: string, notificable?: \Illuminate\Database\Eloquent\Model|null}  $meta
     */
    public function enviarWhatsappPlantilla(string $telefono, string $plantilla, array $parametros, string $resumen, array $meta = []): Notificacion
    {
        $notificacion = $this->crear('whatsapp', $telefono, $resumen, null, $meta);

        $resultado = $this->whatsapp->enviarPlantilla($telefono, $plantilla, $parametros);

        if ($resultado['success']) {
            $notificacion->update([
                'estado'     => 'enviado',
                'enviado_en' => now(),
                'wamid'      => $resultado['wamid'],
            ]);
        } else {
            $notificacion->update([
                'estado' => 'fallido',
                'error'  => $resultado['error'] ?? 'Error desconocido',
            ]);
        }

        return $notificacion;
    }

    /**
     * Envía un mensaje de WhatsApp y lo registra en la bitácora.
     *
     * @param  array{origen?: string, notificable?: \Illuminate\Database\Eloquent\Model|null}  $meta
     */
    public function enviarWhatsapp(string $telefono, string $mensaje, array $meta = []): Notificacion
    {
        $notificacion = $this->crear('whatsapp', $telefono, $mensaje, null, $meta);

        $resultado = $this->whatsapp->enviarMensaje($telefono, $mensaje);

        if ($resultado['success']) {
            $notificacion->update([
                'estado' => 'enviado',
                'enviado_en' => now(),
                'wamid' => $resultado['wamid'],
            ]);
        } else {
            $notificacion->update([
                'estado' => 'fallido',
                'error' => $resultado['error'] ?? 'Error desconocido',
            ]);
        }

        return $notificacion;
    }

    private function crear(string $canal, string $destinatario, string $cuerpo, ?string $asunto, array $meta): Notificacion
    {
        return Notificacion::create([
            'canal' => $canal,
            'destinatario' => $destinatario,
            'asunto' => $asunto,
            'cuerpo' => $cuerpo,
            'estado' => 'pendiente',
            'origen' => $meta['origen'] ?? 'manual',
            'notificable_type' => isset($meta['notificable']) ? $meta['notificable']->getMorphClass() : null,
            'notificable_id' => $meta['notificable']?->getKey(),
        ]);
    }
}

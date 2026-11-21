<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * GET: verificación inicial del webhook (Meta la llama una sola vez).
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && hash_equals(config('services.whatsapp.verify_token'), (string) $token)) {
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * POST: recepción de eventos.
     */
    public function handle(Request $request)
    {
        if (! $this->validSignature($request)) {
            Log::warning('WhatsApp webhook: firma inválida');

            return response()->json(['status' => 'invalid signature'], 403);
        }

        try {
            $this->process($request->all());
        } catch (\Throwable $e) {
            // Nunca devolvemos error a Meta: reintentaría el evento en bucle.
            Log::error('WhatsApp webhook error', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    private function validSignature(Request $request): bool
    {
        $secret = config('services.whatsapp.app_secret');

        if (empty($secret)) {
            return true; // sin secret configurado, no validamos (solo dev)
        }

        $header = $request->header('X-Hub-Signature-256');

        if (! $header) {
            return false;
        }

        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $header);
    }

    private function process(array $payload): void
    {
        foreach ($payload['entry'] ?? [] as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];

                foreach ($value['messages'] ?? [] as $message) {
                    $this->storeIncoming($message, $value['contacts'] ?? []);
                }

                foreach ($value['statuses'] ?? [] as $status) {
                    $this->storeStatus($status);
                }
            }
        }
    }

    private function storeIncoming(array $message, array $contacts): void
    {
        $name = $contacts[0]['profile']['name'] ?? null;
        $type = $message['type'] ?? 'unknown';

        WhatsappMessage::updateOrCreate(
            ['wamid' => $message['id']],
            [
                'direction' => 'in',
                'phone' => $message['from'] ?? '',
                'contact_name' => $name,
                'type' => $type,
                'body' => $this->extractBody($message, $type),
                'payload' => $message,
                'event_at' => isset($message['timestamp'])
                    ? now()->setTimestamp((int) $message['timestamp'])
                    : now(),
            ]
        );
    }

    private function storeStatus(array $status): void
    {
        $wamid = $status['id'] ?? '';
        $estado = $status['status'] ?? null;
        $eventAt = isset($status['timestamp'])
            ? now()->setTimestamp((int) $status['timestamp'])
            : now();

        $record = WhatsappMessage::where('wamid', $wamid)->first();

        if ($record) {
            $record->update(['status' => $estado, 'event_at' => $eventAt]);
        }

        // Actualizar la bitácora de notificaciones salientes
        $mapa = ['delivered' => 'entregado', 'read' => 'leido', 'failed' => 'fallido'];

        if ($wamid && isset($mapa[$estado])) {
            Notificacion::where('wamid', $wamid)->update([
                'estado' => $mapa[$estado],
                'error' => $estado === 'failed'
                    ? ($status['errors'][0]['title'] ?? 'Fallo reportado por WhatsApp')
                    : null,
            ]);
        }
    }

    private function extractBody(array $message, string $type): ?string
    {
        return match ($type) {
            'text' => $message['text']['body'] ?? null,
            'button' => $message['button']['text'] ?? null,
            'interactive' => $message['interactive']['button_reply']['title']
                ?? $message['interactive']['list_reply']['title']
                ?? null,
            'image', 'document', 'audio', 'video' => $message[$type]['caption'] ?? null,
            default => null,
        };
    }
}

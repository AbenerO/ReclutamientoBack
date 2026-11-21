<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $phoneNumberId;

    protected string $accessToken;

    protected string $apiVersion;

    protected string $baseUrl;

    protected string $graphBaseUrl;

    public function __construct()
    {
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->accessToken = config('services.whatsapp.access_token');
        $this->apiVersion = config('services.whatsapp.api_version');
        $this->graphBaseUrl = "https://graph.facebook.com/{$this->apiVersion}/{$this->phoneNumberId}";
        $this->baseUrl = "{$this->graphBaseUrl}/messages";
    }

    /**
     * Envía un mensaje de texto libre.
     *
     * @return array{success: bool, wamid: ?string, error: ?string}
     */
    public function enviarMensaje(string $telefono, string $mensaje): array
    {
        $response = Http::withToken($this->accessToken)
            ->post($this->baseUrl, [
                'messaging_product' => 'whatsapp',
                'to' => $telefono,
                'type' => 'text',
                'text' => ['body' => $mensaje],
            ]);

        if ($response->failed()) {
            Log::error('WhatsApp error', $response->json() ?? []);

            return [
                'success' => false,
                'wamid' => null,
                'error' => $response->json('error.message') ?? "HTTP {$response->status()}",
            ];
        }

        return [
            'success' => true,
            'wamid' => $response->json('messages.0.id'),
            'error' => null,
        ];
    }

    /**
     * Envía un mensaje usando una plantilla aprobada por Meta.
     * Parámetros son los valores de {{1}}, {{2}}, {{3}}... en el cuerpo de la plantilla.
     *
     * @param  string[]  $parametros
     * @return array{success: bool, wamid: ?string, error: ?string}
     */
    public function enviarPlantilla(string $telefono, string $nombrePlantilla, array $parametros, string $idioma = 'es'): array
    {
        $components = [];

        if (!empty($parametros)) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(fn($v) => ['type' => 'text', 'text' => $v], $parametros),
            ];
        }

        $response = Http::withToken($this->accessToken)
            ->post($this->baseUrl, [
                'messaging_product' => 'whatsapp',
                'to'                => $telefono,
                'type'              => 'template',
                'template'          => [
                    'name'       => $nombrePlantilla,
                    'language'   => ['code' => $idioma],
                    'components' => $components,
                ],
            ]);

        if ($response->failed()) {
            Log::error('WhatsApp template error', $response->json() ?? []);

            return [
                'success' => false,
                'wamid'   => null,
                'error'   => $response->json('error.message') ?? "HTTP {$response->status()}",
            ];
        }

        return [
            'success' => true,
            'wamid'   => $response->json('messages.0.id'),
            'error'   => null,
        ];
    }

    /**
     * Registra el número de teléfono ante WhatsApp Cloud API (POST /{PHONE_NUMBER_ID}/register).
     * Requerido una sola vez cuando el número aparece como "Pendiente" en WhatsApp Manager.
     */
    public function registrarNumero(string $pin): array
    {
        $response = Http::withToken($this->accessToken)
            ->post("{$this->graphBaseUrl}/register", [
                'messaging_product' => 'whatsapp',
                'pin' => $pin,
            ]);

        if ($response->failed()) {
            Log::error('WhatsApp registration error', $response->json() ?? []);
        }

        return [
            'success' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }
}

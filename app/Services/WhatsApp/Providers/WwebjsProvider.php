<?php

namespace App\Services\WhatsApp\Providers;

use App\Services\WhatsApp\Contracts\WhatsAppProvider;
use App\Services\WhatsApp\Exceptions\WhatsAppException;
use Illuminate\Support\Facades\Http;

class WwebjsProvider implements WhatsAppProvider
{
    private string $baseUrl;
    private string $apiKey;
    private string $session;

    public function __construct()
    {
        $this->baseUrl = config('whatsapp.wwebjs.base_url');
        $this->apiKey  = config('whatsapp.wwebjs.api_key');
        $this->session = config('whatsapp.wwebjs.session');
    }

    public function send(string $to, string $message): string
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
        ])->post("{$this->baseUrl}/client/sendMessage/{$this->session}", [
            'chatId'      => $to,
            'contentType' => 'string',
            'content'     => $message,
        ]);

        if ($response->failed()) {
            throw new WhatsAppException('Error al enviar mensaje: ' . $response->body());
        }

        return $response->body();
    }

    public function getStatus(string $messageId): string
    {
        return 'sent';
    }

    public function handleWebhook(array $payload): ?array
    {
        $dataType = $payload['dataType'] ?? '';
        $message  = $payload['data']['message'] ?? [];

        if ($dataType !== 'message') {
            return null;
        }

        $fromMe = $message['fromMe'] ?? true;

        if ($fromMe) {
            return null;
        }

        $body = strtolower(trim($message['body'] ?? ''));
        $from = $message['from'] ?? '';

        if (empty($body)) {
            return null;
        }

        return [
            'remitente'     => $from,
            'respuesta'     => $body,
            'es_aceptacion' => in_array($body, ['1', 'acepto', 'si', 'sí']),
            'es_rechazo'    => in_array($body, ['2', 'no', 'no estoy disponible', 'no disponible']),
        ];
    }
}

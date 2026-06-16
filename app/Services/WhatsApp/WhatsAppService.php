<?php

namespace App\Services\WhatsApp;

use App\Models\SolicitudDestinatario;
use App\Models\SolicitudCobertura;
use App\Services\WhatsApp\Contracts\WhatsAppProvider;
use App\Services\WhatsApp\Exceptions\WhatsAppException;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private WhatsAppProvider $provider;

    public function __construct(WhatsAppProvider $provider)
    {
        $this->provider = $provider;
    }

    public function sendToDestinatario(SolicitudDestinatario $destinatario, SolicitudCobertura $solicitud): void
    {
        $empleado = $destinatario->empleado;
        if (!$empleado || !$empleado->telefono) {
            throw new WhatsAppException("El empleado {$empleado?->id} no tiene teléfono registrado.");
        }

        $to = $this->formatPhone($empleado->telefono);
        $message = $this->buildMessage($solicitud);

        $this->provider->send($to, $message);

        $destinatario->update([
            'notificado_at' => now(),
            'chat_id'       => $to,
        ]);
    }

    public function handleWebhookResponse(array $payload): void
    {
        Log::info('handleWebhookResponse payload', ['payload' => $payload]);

        $result = $this->provider->handleWebhook($payload);

        if ($result === null) {
            Log::info('handleWebhookResponse: result null (fromMe=true or wrong dataType)');
            return;
        }

        $remitente = $result['remitente'];
        $respuesta = $result['respuesta'];

        Log::info('handleWebhookResponse: result ok', ['result' => $result]);

        $destinatario = $this->findDestinatarioByPhone($remitente);

        if (!$destinatario) {
            Log::warning("WhatsApp: destinatario no encontrado para remitente {$remitente}");
            return;
        }

        Log::info('handleWebhookResponse: destinatario encontrado', ['destinatario_id' => $destinatario->id]);

        $data = [
            'respondido_at' => now(),
        ];

        if ($result['es_aceptacion']) {
            $data['estado'] = 'aceptada';
        } elseif ($result['es_rechazo']) {
            $data['estado'] = 'rechazada';
        } else {
            $data['motivo_rechazo'] = $respuesta;
        }

        $destinatario->update($data);
        Log::info('handleWebhookResponse: actualizado', ['data' => $data]);
    }

    private function formatPhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        return $digits . '@c.us';
    }

    private function buildMessage(SolicitudCobertura $solicitud): string
    {
        $fecha = $solicitud->fecha_inicio;
        $horaInicio = $solicitud->hora_inicio->format('H:i');
        $horaFin = $solicitud->hora_fin->format('H:i');
        $centro = $solicitud->centro?->nombre ?? '—';
        $especialidad = $solicitud->especialidad?->nombre ?? '—';

        return "Hola! ¿Podés cubrir el turno del {$fecha} de {$horaInicio} - {$horaFin}hs en {$centro} como ({$especialidad})?\n\nRespondé con:\n1️⃣ Acepto\n2️⃣ No estoy disponible";
    }

    private function findDestinatarioByPhone(string $remitente): ?SolicitudDestinatario
    {
        $parts = explode('@', $remitente);
        $phone = $parts[0] ?? '';
        $domain = $parts[1] ?? '';

        if ($domain === 'lid') {
            // LID format: buscar por chat_id o el más reciente pendiente
            return SolicitudDestinatario::whereNull('respondido_at')
                ->whereHas('solicitud', fn ($q) => $q->where('estado', 'pendiente'))
                ->latest('id')
                ->first();
        }

        $phone = ltrim($phone, '549');

        return SolicitudDestinatario::whereHas('empleado', function ($q) use ($phone) {
            $q->where('telefono', 'like', "%{$phone}");
        })->whereNull('respondido_at')->latest('id')->first();
    }
}

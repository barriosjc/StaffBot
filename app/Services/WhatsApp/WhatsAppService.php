<?php

namespace App\Services\WhatsApp;

use App\Models\SolicitudDestinatario;
use App\Models\SolicitudCobertura;
use App\Models\User;
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

    public function sendRaw(string $phone, string $message): void
    {
        $to = $this->formatPhone($phone);
        $this->provider->send($to, $message);
    }

    public function handleWebhookResponse(array $payload): void
    {
        $result = $this->provider->handleWebhook($payload);
        if ($result === null) {
            return;
        }

        $remitente = $result['remitente'];
        $respuesta = $result['respuesta'];
        $esAceptacion = $result['es_aceptacion'];
        $esRechazo = $result['es_rechazo'];

        $destinatario = $this->findDestinatarioByPhone($remitente);
        if (!$destinatario) {
            Log::warning("WhatsApp: destinatario no encontrado para remitente {$remitente}");
            return;
        }

        $solicitud = $destinatario->solicitud;
        $empleado = $destinatario->empleado;

        $this->procesarRespuesta($destinatario, $solicitud, $respuesta, $esAceptacion, $esRechazo);
    }

    private function procesarRespuesta(
        SolicitudDestinatario $destinatario,
        SolicitudCobertura $solicitud,
        string $respuesta,
        bool $esAceptacion,
        bool $esRechazo
    ): void {
        if ($destinatario->estado !== 'no_respondio') {
            return;
        }

        if ($esAceptacion) {
            $this->aceptar($destinatario, $solicitud);
        } elseif ($esRechazo) {
            $this->solicitarMotivo($destinatario, $solicitud);
        } elseif ($destinatario->motivo_solicitado_at) {
            $this->rechazarConMotivo($destinatario, $solicitud, $respuesta);
        } else {
            $this->enviarInvalido($destinatario, $solicitud);
        }
    }

    private function aceptar(SolicitudDestinatario $destinatario, SolicitudCobertura $solicitud): void
    {
        $destinatario->update([
            'estado'        => 'aceptada',
            'respondido_at' => now(),
        ]);

        $solicitud->update(['estado' => 'aceptada']);

        $this->enviarMensaje($destinatario, $solicitud, 'aceptada_confirmation');

        $this->notificarSupervisor($destinatario, $solicitud, 'supervisor_aceptada', [
            '{empleado}' => $destinatario->empleado?->name ?? '—',
        ]);
    }

    private function solicitarMotivo(SolicitudDestinatario $destinatario, SolicitudCobertura $solicitud): void
    {
        $destinatario->update([
            'motivo_solicitado_at' => now(),
            'respondido_at'        => now(),
        ]);

        $this->enviarMensaje($destinatario, $solicitud, 'motivo_request');
    }

    private function rechazarConMotivo(SolicitudDestinatario $destinatario, SolicitudCobertura $solicitud, string $motivo): void
    {
        $destinatario->update([
            'estado'         => 'rechazada',
            'motivo_rechazo' => $motivo,
            'respondido_at'  => now(),
        ]);

        $solicitud->update(['estado' => 'rechazada']);

        $this->enviarMensaje($destinatario, $solicitud, 'motivo_thanks');

        $this->notificarSupervisor($destinatario, $solicitud, 'supervisor_rechazada', [
            '{empleado}' => $destinatario->empleado?->name ?? '—',
            '{motivo}'   => $motivo,
        ]);
    }

    private function enviarInvalido(SolicitudDestinatario $destinatario, SolicitudCobertura $solicitud): void
    {
        $this->enviarMensaje($destinatario, $solicitud, 'invalid_option');
    }

    private function enviarMensaje(SolicitudDestinatario $destinatario, SolicitudCobertura $solicitud, string $templateKey): void
    {
        try {
            $empleado = $destinatario->empleado;
            if (!$empleado || !$empleado->telefono) {
                return;
            }

            $to = $this->formatPhone($empleado->telefono);
            $message = $this->buildTemplateMessage($templateKey, $solicitud, $destinatario);

            if ($message === null) {
                return;
            }

            $this->provider->send($to, $message);
        } catch (\Throwable $e) {
            Log::error("Error al enviar mensaje '{$templateKey}': " . $e->getMessage());
        }
    }

    private function notificarSupervisor(
        SolicitudDestinatario $destinatario,
        SolicitudCobertura $solicitud,
        string $templateKey,
        array $extra = []
    ): void {
        try {
            $supervisor = $solicitud->supervisor;
            if (!$supervisor || !$supervisor->telefono) {
                return;
            }

            $to = $this->formatPhone($supervisor->telefono);
            $message = $this->buildTemplateMessage($templateKey, $solicitud, $destinatario, $extra);

            if ($message === null) {
                return;
            }

            $this->provider->send($to, $message);
        } catch (\Throwable $e) {
            Log::warning("Error al notificar supervisor: " . $e->getMessage());
        }
    }

    public function cancelarSolicitudAnterior(SolicitudCobertura $solicitudNueva, int $empleadoAnteriorId): void
    {
        $anterior = SolicitudDestinatario::where('empleado_id', $empleadoAnteriorId)
            ->whereHas('solicitud', function ($q) use ($solicitudNueva) {
                $q->where('centro_id', $solicitudNueva->centro_id)
                  ->where('especialidad_id', $solicitudNueva->especialidad_id)
                  ->where('fecha_inicio', $solicitudNueva->fecha_inicio)
                  ->where('estado', 'pendiente')
                  ->where('id', '!=', $solicitudNueva->id);
            })
            ->whereNull('respondido_at')
            ->first();

        if (!$anterior) {
            return;
        }

        $solicitudAnterior = $anterior->solicitud;

        $anterior->update(['estado' => 'cancelado', 'respondido_at' => now()]);
        $solicitudAnterior->update(['estado' => 'cancelado']);

        try {
            $empleado = $anterior->empleado;
            if ($empleado && $empleado->telefono) {
                $to = $this->formatPhone($empleado->telefono);
                $message = $this->buildTemplateMessage('cancelled_notification', $solicitudNueva);
                if ($message) {
                    $this->provider->send($to, $message);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Error al notificar cancelación: " . $e->getMessage());
        }
    }

    public function verificarTimeoutMotivo(): void
    {
        $timeout = (int) config('whatsapp.motivo_timeout_minutes', 10);

        $destinatarios = SolicitudDestinatario::where('estado', 'no_respondio')
            ->whereNotNull('motivo_solicitado_at')
            ->whereNull('motivo_rechazo')
            ->where('motivo_solicitado_at', '<', now()->subMinutes($timeout))
            ->get();

        foreach ($destinatarios as $destinatario) {
            $solicitud = $destinatario->solicitud;
            $empleado = $destinatario->empleado;

            if ($solicitud->estado !== 'pendiente') {
                continue;
            }

            $this->notificarSupervisor($destinatario, $solicitud, 'supervisor_rechazada_sin_motivo', [
                '{empleado}' => $empleado?->name ?? '—',
                '{timeout}'  => $timeout,
            ]);
        }
    }

    private function buildTemplateMessage(
        string $key,
        SolicitudCobertura $solicitud,
        ?SolicitudDestinatario $destinatario = null,
        array $extra = []
    ): ?string {
        $template = config("whatsapp.templates.{$key}");

        if ($key === 'message_template' || $key === null) {
            $template = config('whatsapp.message_template');
        }

        if (!$template) {
            return null;
        }

        $replaces = [
            '{fecha}'          => $solicitud->fecha_inicio,
            '{hora_inicio}'    => $solicitud->hora_inicio->format('H:i'),
            '{hora_fin}'       => $solicitud->hora_fin->format('H:i'),
            '{centro}'         => $solicitud->centro?->nombre ?? '—',
            '{especialidad}'   => $solicitud->especialidad?->nombre ?? '—',
            '{supervisor}'     => $solicitud->supervisor?->name ?? '—',
            '{empleado}'       => $destinatario?->empleado?->name ?? '—',
            '{motivo}'         => '',
            '{timeout}'        => (string) config('whatsapp.motivo_timeout_minutes', 10),
        ];

        $replaces = array_merge($replaces, $extra);

        return str_replace(array_keys($replaces), array_values($replaces), $template);
    }

    private function buildMessage(SolicitudCobertura $solicitud): string
    {
        return $this->buildTemplateMessage('message_template', $solicitud)
            ?? "Turno del {$solicitud->fecha_inicio} de {$solicitud->hora_inicio->format('H:i')} a {$solicitud->hora_fin->format('H:i')} en {$solicitud->centro?->nombre ?? '—'} ({$solicitud->especialidad?->nombre ?? '—'})";
    }

    private function formatPhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        return $digits . '@c.us';
    }

    private function findDestinatarioByPhone(string $remitente): ?SolicitudDestinatario
    {
        $parts = explode('@', $remitente);
        $phone = $parts[0] ?? '';
        $domain = $parts[1] ?? '';

        if ($domain === 'lid') {
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

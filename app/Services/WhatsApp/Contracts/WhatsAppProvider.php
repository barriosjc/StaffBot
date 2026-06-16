<?php

namespace App\Services\WhatsApp\Contracts;

interface WhatsAppProvider
{
    public function send(string $to, string $message): string;

    public function getStatus(string $messageId): string;

    public function handleWebhook(array $payload): ?array;
}

<?php

namespace App\Http\Controllers;

use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __invoke(Request $request, WhatsAppService $whatsApp): void
    {
        Log::info('Webhook recibido', ['payload' => $request->all()]);
        $whatsApp->handleWebhookResponse($request->all());
    }
}

<?php

return [
    'driver' => env('WHATSAPP_DRIVER', 'wwebjs'),

    'wwebjs' => [
        'base_url' => env('WHATSAPP_WWEBJS_URL', 'http://localhost:3000'),
        'api_key'  => env('WHATSAPP_WWEBJS_API_KEY', 'miclaveapi123'),
        'session'  => env('WHATSAPP_WWEBJS_SESSION', 'mysession'),
    ],

    'whatsapp_cloud' => [
        'phone_number_id' => env('WHATSAPP_CLOUD_PHONE_ID'),
        'token'           => env('WHATSAPP_CLOUD_TOKEN'),
    ],

    'message_template' => env('WHATSAPP_MESSAGE_TEMPLATE', "Hola! ¿Podés cubrir el turno del {fecha} de {hora_inicio} - {hora_fin}hs en {centro} como ({especialidad})?\n\nRespondé con:\n1️⃣ Acepto\n2️⃣ No estoy disponible"),

    'motivo_timeout_minutes' => env('WHATSAPP_MOTIVO_TIMEOUT', 10),

    'templates' => [
        'aceptada_confirmation' => env('WHATSAPP_TEMPLATE_ACEPTADA', "✅ ¡Gracias por aceptar! Te esperamos el {fecha} de {hora_inicio} a {hora_fin}hs en {centro}."),

        'motivo_request' => env('WHATSAPP_TEMPLATE_MOTIVO', "Entendemos que no puedas. ¿Podrías contarnos brevemente el motivo?\n\nEscribí el motivo y te lo agradeceremos."),

        'motivo_thanks' => env('WHATSAPP_TEMPLATE_MOTIVO_THANKS', "Gracias por tu respuesta. Tomamos nota de tu motivo."),

        'invalid_option' => env('WHATSAPP_TEMPLATE_INVALID', "⚠️ Respondé con:\n1️⃣ Acepto\n2️⃣ No estoy disponible"),

        'cancelled_notification' => env('WHATSAPP_TEMPLATE_CANCELLED', "La solicitud de cobertura del {fecha} de {hora_inicio} a {hora_fin}hs en {centro} ({especialidad}) fue cancelada. Disculpá las molestias."),

        'supervisor_aceptada' => env('WHATSAPP_SUP_ACEPTADA', "✅ El empleado {empleado} ACEPTÓ la solicitud de cobertura del {fecha} de {hora_inicio} a {hora_fin}hs en {centro} ({especialidad})."),

        'supervisor_rechazada' => env('WHATSAPP_SUP_RECHAZADA', "❌ El empleado {empleado} RECHAZÓ la solicitud de cobertura del {fecha} de {hora_inicio} a {hora_fin}hs en {centro} ({especialidad}).\nMotivo: {motivo}"),

        'supervisor_rechazada_sin_motivo' => env('WHATSAPP_SUP_RECHAZADA_SM', "❌ El empleado {empleado} RECHAZÓ la solicitud del {fecha} en {centro} ({especialidad}) pero aún no indicó el motivo. Pasaron más de {timeout} minutos."),
    ],
];

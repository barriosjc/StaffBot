# Contexto: solicitudes de cobertura y WhatsApp

Este archivo resume el flujo actual para no perder contexto entre sesiones.

## README

El `README.md` actual es el README base de Laravel. No contiene todavia documentacion especifica de Staffbot.

## Flujo al enviar la primera solicitud

El alta de una solicitud se realiza desde `App\Livewire\SolicitudCoberturas\Form`.

Al ejecutar `guardar()`:

1. Valida centro, especialidad, fecha, horario, empleado y modo de envio.
2. Calcula `fecha_fin`; si `hora_inicio >= hora_fin`, asume turno nocturno y suma un dia.
3. Crea una `SolicitudCobertura` con `estado = pendiente`.
4. Crea un `SolicitudDestinatario` con:
   - `estado = no_respondio`
   - `orden = 1`
   - `notificado_at = now()`
5. Envia WhatsApp al empleado con `WhatsAppService::sendToDestinatario()`.
6. Si el modo del formulario es `crear` o `enviar_otro`, llama a `cancelarSolicitudAnterior()`.
7. Redirige al listado de solicitudes.

## Respuestas por WhatsApp

Las respuestas entran por `WhatsAppWebhookController`, que delega en `WhatsAppService::handleWebhookResponse()`.

El provider `WwebjsProvider` interpreta:

- Aceptacion: `1`, `acepto`, `si`.
- Rechazo: `2`, `no`, `no estoy disponible`, `no disponible`.

Solo se procesa una respuesta si el destinatario esta en `estado = no_respondio`.

## Variante: aceptacion

Cuando el empleado acepta:

1. El destinatario pasa a `estado = aceptada`.
2. Se guarda `respondido_at`.
3. La solicitud pasa a `estado = aceptada`.
4. Se envia confirmacion al empleado.
5. Se notifica al supervisor con el template `supervisor_aceptada`.

## Variante: rechazo con motivo

Cuando el empleado rechaza por primera vez:

1. No se marca todavia como rechazada.
2. Se guarda `motivo_solicitado_at`.
3. Se guarda `respondido_at`.
4. Se envia el template `motivo_request` pidiendo el motivo.

Cuando despues responde cualquier texto:

1. El destinatario pasa a `estado = rechazada`.
2. Se guarda `motivo_rechazo`.
3. Se actualiza `respondido_at`.
4. La solicitud pasa a `estado = rechazada`.
5. Se envia agradecimiento al empleado.
6. Se notifica al supervisor con `supervisor_rechazada`, incluyendo el motivo.

## Variante: respuesta invalida

Si responde algo que no es aceptacion ni rechazo, y todavia no se pidio motivo:

1. Se envia el template `invalid_option`.
2. La solicitud sigue `pendiente`.
3. El destinatario sigue `no_respondio`.

## Variante: rechazo sin motivo

Existe `WhatsAppService::verificarTimeoutMotivo()`.

Busca destinatarios:

- con `estado = no_respondio`
- con `motivo_solicitado_at`
- sin `motivo_rechazo`
- con timeout vencido segun `whatsapp.motivo_timeout_minutes`

Si encuentra casos y la solicitud sigue `pendiente`, notifica al supervisor con `supervisor_rechazada_sin_motivo`.

Nota: actualmente este proceso no cambia el estado del destinatario ni de la solicitud a `rechazada`; solo notifica.

## Variante: reenviar

Desde `App\Livewire\SolicitudCoberturas\Index`:

- `reenviar($id)` redirige al formulario con `modo = reenviar`.
- El formulario carga los datos de la solicitud original.
- Mantiene el primer destinatario si existe.
- Al guardar, se crea una nueva solicitud y un nuevo destinatario.

## Variante: enviar a otro empleado

Desde `App\Livewire\SolicitudCoberturas\Index`:

- `enviarOtro($id)` redirige al formulario con `modo = enviar_otro`.
- El formulario carga centro, especialidad, fecha, horario y modo de envio.
- Limpia `empleadoId` para seleccionar otro empleado.
- Al guardar, se crea una nueva solicitud y un nuevo destinatario.
- Luego intenta cancelar una solicitud anterior pendiente para el mismo empleado elegido, misma fecha, centro y especialidad.

## Variante: cancelar

Desde `App\Livewire\SolicitudCoberturas\Index::cancelar()`:

1. Busca la solicitud.
2. Calcula el mayor `orden` de destinatarios.
3. Crea un nuevo `SolicitudDestinatario` con:
   - `empleado_id = Auth::id()`
   - `estado = cancelado`
   - `respondido_at = now()`
4. Cambia la solicitud a `estado = cancelado`.

## Modos de envio

La base de datos y el formulario admiten:

- `manual_uno`
- `secuencial`
- `broadcast`

Estado actual: la logica implementada manda a un solo empleado. `secuencial` y `broadcast` existen como valores, pero no tienen comportamiento diferenciado todavia.

## Mensajes configurables

Los mensajes de WhatsApp estan definidos actualmente en `config/whatsapp.php`.

No estan guardados en base de datos. Se leen con `config('whatsapp...')` y pueden sobrescribirse desde `.env` usando las variables `WHATSAPP_MESSAGE_TEMPLATE`, `WHATSAPP_TEMPLATE_ACEPTADA`, `WHATSAPP_TEMPLATE_MOTIVO`, `WHATSAPP_TEMPLATE_MOTIVO_THANKS`, `WHATSAPP_TEMPLATE_INVALID`, `WHATSAPP_TEMPLATE_CANCELLED`, `WHATSAPP_SUP_ACEPTADA`, `WHATSAPP_SUP_RECHAZADA` y `WHATSAPP_SUP_RECHAZADA_SM`.

## Archivos clave

- `app/Livewire/SolicitudCoberturas/form.php`
- `app/Livewire/SolicitudCoberturas/Index.php`
- `app/Services/WhatsApp/WhatsAppService.php`
- `app/Services/WhatsApp/Providers/WwebjsProvider.php`
- `app/Http/Controllers/WhatsAppWebhookController.php`
- `config/whatsapp.php`
- `database/migrations/2024_01_01_000007_create_solicitudes_cobertura_table.php`
- `database/migrations/2024_01_01_000008_create_solicitud_destinatarios_table.php`
- `database/migrations/2024_01_01_000011_add_cancelado_to_solicitudes_coberturas_estado.php`
- `database/migrations/2024_01_01_000012_add_motivo_rechazo_to_solicitud_destinatarios.php`
- `database/migrations/2024_01_01_000013_add_cancelado_to_solicitud_destinatarios_estado.php`
- `database/migrations/2024_01_01_000014_remove_pendiente_from_solicitud_destinatarios_estado.php`

## Pendientes / puntos a revisar

- Definir e implementar comportamiento real para `secuencial`.
- Definir e implementar comportamiento real para `broadcast`.
- Decidir si `verificarTimeoutMotivo()` debe cambiar estados o solo notificar.
- Revisar si `cancelarSolicitudAnterior()` debe cancelar por empleado anterior o por empleado nuevo elegido.
- Revisar que existan columnas usadas por el servicio, como `chat_id` y `motivo_solicitado_at`, en las migraciones actuales.

<x-mail::message>
# Resolución de Consejo Interno

@if ($notificacion->destinatario_nombre)
Hola, {{ $notificacion->destinatario_nombre }}.
@else
Hola.
@endif

Se ha registrado una resolución del Consejo Interno para una solicitud.

<x-mail::panel>
**Folio:** {{ $notificacion->payload['folio'] ?? 'Sin folio' }}

**Tipo de solicitud:** {{ $notificacion->payload['tipo_solicitud'] ?? 'Sin tipo registrado' }}

**Resolución:** {{ $notificacion->payload['resolucion'] }}

**Estatus:** {{ $notificacion->payload['estatus'] ?? 'Sin estatus' }}
</x-mail::panel>

@if (($notificacion->payload['resolucion'] ?? null) === 'ACEPTAR')
La solicitud fue aceptada por el Consejo Interno.

@if ($notificacion->payload['requiere_recursos'] ?? false)
El expediente continuará con el seguimiento administrativo correspondiente por los recursos solicitados.
@else
Al no requerir recursos, el expediente queda cerrado.
@endif
@endif

@if (($notificacion->payload['resolucion'] ?? null) === 'RECHAZAR')
La solicitud fue rechazada por el Consejo Interno y el expediente queda cerrado.
@endif

@if ($notificacion->payload['nombre_evento'] ?? null)
## Actividad o evento

{{ $notificacion->payload['nombre_evento'] }}
@endif

@if ($notificacion->payload['institucion'] ?? null)
**Institución:** {{ $notificacion->payload['institucion'] }}
@endif

@if ($notificacion->payload['observaciones_sacad'] ?? null)
## Observaciones SACAD

{{ $notificacion->payload['observaciones_sacad'] }}
@endif

@if ($notificacion->payload['observaciones_administracion'] ?? null)
## Observaciones Administración

{{ $notificacion->payload['observaciones_administracion'] }}
@endif

@if ($notificacion->payload['reunion'] ?? null)
## Reunión

**{{ $notificacion->payload['reunion'] }}**

@if ($notificacion->payload['fecha_reunion'] ?? null)
Fecha: {{ $notificacion->payload['fecha_reunion'] }}
@endif
@endif

@if (! empty($notificacion->adjuntos))
Se incluyen documentos anexos a esta notificación.
@endif

Gracias,
{{ config('app.name') }}
</x-mail::message>

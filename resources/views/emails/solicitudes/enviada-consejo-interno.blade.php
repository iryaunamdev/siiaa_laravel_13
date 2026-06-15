@php
    $folio = $solicitud->folioDisplay();
    $tipo = $solicitud->tipoSolicitud?->nombre ?? 'Solicitud';
    $solicitante = $solicitud->owner?->nombre_completo ?? ($solicitud->owner?->nombre ?? 'Solicitante no identificado');
@endphp

<p>Consejo Interno:</p>

<p>
    Se recibió una nueva solicitud para revisión institucional.
</p>

<p>
    <strong>Folio:</strong> {{ $folio }}<br>
    <strong>Tipo de solicitud:</strong> {{ $tipo }}<br>
    <strong>Solicitante:</strong> {{ $solicitante }}<br>
    <strong>Estatus:</strong> {{ $solicitud->estatusNombre() }}
</p>

@if ($solicitud->submitted_at)
    <p>
        <strong>Fecha de envío:</strong>
        {{ $solicitud->submitted_at->format('d/m/Y H:i') }}
    </p>
@endif

@if (!empty($solicitud->informacion_adicional))
    <p>
        <strong>Información adicional:</strong><br>
        {{ $solicitud->informacion_adicional }}
    </p>
@endif

<p>
    Favor de ingresar al SIIAA para revisar el expediente completo.
</p>

<p>
    Atentamente,<br>
    Sistema SIIAA
</p>
tounch

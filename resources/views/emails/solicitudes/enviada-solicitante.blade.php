@php
    $folio = $solicitud->folioDisplay();
    $tipo = $solicitud->tipoSolicitud?->nombre ?? 'Solicitud';
@endphp

<p>Estimado/a solicitante:</p>

<p>
    Su solicitud <strong>{{ $folio }}</strong> fue enviada correctamente
    y quedó registrada para revisión institucional.
</p>

<p>
    <strong>Tipo de solicitud:</strong> {{ $tipo }}<br>
    <strong>Estatus:</strong> {{ $solicitud->estatusNombre() }}
</p>

@if ($solicitud->submitted_at)
    <p>
        <strong>Fecha de envío:</strong>
        {{ $solicitud->submitted_at->format('d/m/Y H:i') }}
    </p>
@endif

<p>
    Recibirá notificaciones conforme avance el proceso de revisión.
</p>

<p>
    Atentamente,<br>
    Sistema SIIAA
</p>

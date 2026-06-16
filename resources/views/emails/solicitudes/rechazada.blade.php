@php
    $folio = $solicitud->folioDisplay();
@endphp

<p>Estimado/a solicitante:</p>

<p>
    Su solicitud <strong>{{ $folio }}</strong> no fue aprobada.
</p>

@if (!empty($solicitud->observaciones_sacad))
    <p>
        <strong>Motivo:</strong><br>
        {{ $solicitud->observaciones_sacad }}
    </p>
@endif

<p>
    Atentamente,<br>
    Sistema SIIAA
</p>

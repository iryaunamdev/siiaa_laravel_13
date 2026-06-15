@php
    $folio = $solicitud->folioDisplay();
@endphp

<p>Estimado/a solicitante:</p>

<p>
    Su solicitud <strong>{{ $folio }}</strong> fue cerrada.
</p>

@if (!empty($solicitud->observaciones_administracion))
    <p>
        <strong>Observaciones administrativas:</strong><br>
        {{ $solicitud->observaciones_administracion }}
    </p>
@endif

<p>
    Atentamente,<br>
    Sistema SIIAA
</p>

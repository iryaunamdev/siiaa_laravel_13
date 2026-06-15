@php
    $folio = $solicitud->folioDisplay();
@endphp

<p>Estimado/a solicitante:</p>

<p>
    Su solicitud <strong>{{ $folio }}</strong> no fue aprobada.
</p>

@if (!empty($solicitud->reject_reason))
    <p>
        <strong>Motivo:</strong><br>
        {{ $solicitud->reject_reason }}
    </p>
@endif

<p>
    Atentamente,<br>
    Sistema SIIAA
</p>

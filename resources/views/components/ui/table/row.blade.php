@props([
    'hover' => true,
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.table.row
    |--------------------------------------------------------------------------
    | Fila de tabla.
    |
    | Detecta la variante striped desde el contenedor padre mediante CSS.
    */

    $hoverClass = $hover ? 'hover:bg-slate-50/70' : '';
@endphp

<tr
    {{ $attributes->merge([
        'class' => "transition {$hoverClass} odd:[.\\[data-table-variant\\=striped\\]_tbody_&]:bg-white even:[.\\[data-table-variant\\=striped\\]_tbody_&]:bg-slate-50/40",
    ]) }}>
    {{ $slot }}
</tr>

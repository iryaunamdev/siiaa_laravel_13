@props([])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.table.body
    |--------------------------------------------------------------------------
    | Cuerpo de tabla.
    */
@endphp

<tbody {{ $attributes->merge([
    'class' => 'divide-y divide-slate-100 bg-white',
]) }}>
    {{ $slot }}
</tbody>

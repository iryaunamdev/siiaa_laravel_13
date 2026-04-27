@props([])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.table.head
    |--------------------------------------------------------------------------
    | Encabezado de tabla.
    |
    | Uso:
    | <x-ui.table.head>
    |     <x-ui.table.row>
    |         <x-ui.table.header>Nombre</x-ui.table.header>
    |     </x-ui.table.row>
    | </x-ui.table.head>
    */
@endphp

<thead {{ $attributes->merge([
    'class' => 'bg-slate-50/80',
]) }}>
    {{ $slot }}
</thead>

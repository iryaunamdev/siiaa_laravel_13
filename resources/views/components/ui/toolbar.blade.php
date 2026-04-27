{{--
|--------------------------------------------------------------------------
| UI Component: Toolbar
|--------------------------------------------------------------------------
|
| Propósito:
| Contenedor visual para herramientas de listados, tablas y catálogos.
|
| Uso principal:
| - Buscadores
| - Filtros
| - Selectores de cantidad por página
| - Botones de acción
| - Acciones secundarias
|
| Props:
| - align: left | center | right | between
| - stacked: bool
| - bordered: bool
| - padding: bool
|
| Slots:
| - default: contenido principal
| - actions: acciones alineadas a la derecha
|
|--------------------------------------------------------------------------
--}}

@props([
    'align' => 'between',
    'stacked' => true,
    'bordered' => true,
    'padding' => true,
])

@php
    $alignClasses =
        [
            'left' => 'justify-start',
            'center' => 'justify-center',
            'right' => 'justify-end',
            'between' => 'justify-between',
        ][$align] ?? 'justify-between';

    $stackClasses = $stacked ? 'flex-col gap-3 sm:flex-row sm:items-center' : 'flex-row items-center gap-3';

    $borderClass = $bordered ? 'border-b border-slate-100' : '';
    $paddingClass = $padding ? 'px-4 py-3' : '';
@endphp

<div
    {{ $attributes->merge([
        'class' => trim("flex {$stackClasses} {$alignClasses} {$borderClass} {$paddingClass}"),
    ]) }}>
    <div class="flex min-w-0 flex-1 flex-col gap-3 sm:flex-row sm:items-center">
        {{ $slot }}
    </div>

    @isset($actions)
        <div class="flex shrink-0 items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>

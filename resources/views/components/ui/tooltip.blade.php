{{--
|--------------------------------------------------------------------------
| UI Component: Tooltip
|--------------------------------------------------------------------------
|
| Propósito:
| Mostrar información breve contextual al hacer hover o focus.
|
| Uso principal:
| - Inputs (help)
| - Botones
| - Iconos
| - Tablas
|
| Props:
| - content: string
| - position: top | bottom | left | right
| - delay: int (ms)
|
| Slots:
| - default (trigger)
|
|--------------------------------------------------------------------------
--}}

@props([
    'content' => '',
    'position' => 'top',
    'delay' => 150,
])

@php
    $positions = [
        'top' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
        'bottom' => 'top-full left-1/2 -translate-x-1/2 mt-2',
        'left' => 'right-full top-1/2 -translate-y-1/2 mr-2',
        'right' => 'left-full top-1/2 -translate-y-1/2 ml-2',
    ];

    $positionClass = $positions[$position] ?? $positions['top'];
@endphp

<div x-data="{
    show: false,
    timeout: null,
    delay: {{ $delay }},
    open() {
        this.timeout = setTimeout(() => this.show = true, this.delay)
    },
    close() {
        clearTimeout(this.timeout)
        this.show = false
    }
}" class="relative inline-flex" x-on:mouseenter="open" x-on:mouseleave="close" x-on:focusin="open"
    x-on:focusout="close">
    {{-- Trigger --}}
    <div class="inline-flex">
        {{ $slot }}
    </div>

    {{-- Tooltip --}}
    <div x-show="show" x-transition.opacity.scale.95 x-cloak
        class="pointer-events-none absolute z-50 {{ $positionClass }}">
        <div class="rounded-md bg-slate-900 px-2.5 py-1.5 text-xs text-white shadow-lg">
            {{ $content }}
        </div>
    </div>
</div>

@props([
    'align' => 'right', // left | center | right
    'position' => 'bottom', // top | bottom | left | right
    'width' => 'w-56', // clases Tailwind directas
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.dropdown
    |--------------------------------------------------------------------------
    | Dropdown flexible para múltiples contextos.
    |
    | align:
    | - left
    | - center
    | - right
    |
    | position:
    | - top
    | - bottom
    | - left
    | - right
    |
    | width:
    | Clase Tailwind directa (w-48, w-64, w-72, etc)
    */

    // Posición base
    $positionMap = [
        'bottom' => 'top-full mt-2',
        'top' => 'bottom-full mb-2',
        'left' => 'right-full mr-2',
        'right' => 'left-full ml-2',
    ];

    // Alineación
    $alignMap = [
        'left' => 'left-0',
        'center' => 'left-1/2 -translate-x-1/2',
        'right' => 'right-0',
    ];

    // Transform origin (para animaciones más naturales)
    $originMap = [
        'bottom' => [
            'left' => 'origin-top-left',
            'center' => 'origin-top',
            'right' => 'origin-top-right',
        ],
        'top' => [
            'left' => 'origin-bottom-left',
            'center' => 'origin-bottom',
            'right' => 'origin-bottom-right',
        ],
        'left' => 'origin-right',
        'right' => 'origin-left',
    ];

    $positionClass = $positionMap[$position] ?? $positionMap['bottom'];
    $alignClass = $alignMap[$align] ?? $alignMap['right'];

    $originClass = is_array($originMap[$position] ?? null)
        ? $originMap[$position][$align] ?? 'origin-top-right'
        : $originMap[$position] ?? 'origin-top-right';
@endphp

<div x-data="{ open: false }" class="relative inline-flex">
    {{-- Trigger --}}
    <div x-on:click="open = !open">
        {{ $trigger }}
    </div>

    {{-- Dropdown --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        x-on:click.outside="open = false" x-on:keydown.escape.window="open = false"
        class="absolute z-50 {{ $positionClass }} {{ $alignClass }} {{ $originClass }} {{ $width }} overflow-hidden rounded-lg border border-slate-200 bg-white shadow-lg ring-1 ring-slate-900/5"
        style="display: none;">
        {{ $slot }}
    </div>
</div>

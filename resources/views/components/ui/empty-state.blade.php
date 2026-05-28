{{--
|--------------------------------------------------------------------------
| UI Component: Empty State
|--------------------------------------------------------------------------
|
| Propósito:
| Mostrar estados vacíos en tablas, listados y módulos.
|
| Uso principal:
| - Sin registros
| - Sin resultados de búsqueda
| - Módulos sin datos iniciales
|
| Props:
| - title: string
| - description: string|null
| - icon: bool
| - size: sm | md | lg
| - variant: default | subtle
|
| Slots:
| - icon
| - actions
|
|--------------------------------------------------------------------------
--}}

@props([
    'title' => 'Sin información disponible',
    'description' => null,
    'icon' => false,
    'variant' => 'default',
])

@php
    $variantClasses = [
        'default' => 'text-center',
        'subtle' => 'text-center text-zinc-500',
    ];

    $variantClass = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

<div
    {{ $attributes->merge([
        'class' => "flex flex-col items-center justify-center px-4 py-6 {$variantClass}",
    ]) }}>
    {{-- Icon --}}
    @if ($icon)
        <div class="mb-4 text-zinc-300">
            {{ $icon }}
        </div>
    @endif

    {{-- Title --}}
    <h3 class="text-sm font-semibold text-zinc-900">
        {{ $title }}
    </h3>

    {{-- Description --}}
    @isset($description)
        <p class="mt-1 max-w-md text-xs text-zinc-500">
            {{ $description }}
        </p>
    @endisset

    {{-- Actions --}}
    @isset($actions)
        <div class="mt-4 flex items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>

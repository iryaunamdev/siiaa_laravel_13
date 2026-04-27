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
    'icon' => true,
    'size' => 'md',
    'variant' => 'default',
])

@php
    $sizes = [
        'sm' => 'py-6',
        'md' => 'py-10',
        'lg' => 'py-16',
    ];

    $variantClasses = [
        'default' => 'text-center',
        'subtle' => 'text-center text-slate-500',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $variantClass = $variantClasses[$variant] ?? $variantClasses['default'];
@endphp

<div
    {{ $attributes->merge([
        'class' => "flex flex-col items-center justify-center px-4 {$sizeClass} {$variantClass}",
    ]) }}>
    {{-- Icon --}}
    @if ($icon)
        <div class="mb-4 text-slate-300">
            @isset($icon)
                {{ $icon }}
            @else
                <svg class="h-10 w-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 7.5A2.25 2.25 0 015.25 5.25h13.5A2.25 2.25 0 0121 7.5v9A2.25 2.25 0 0118.75 18.75H5.25A2.25 2.25 0 013 16.5v-9z" />
                </svg>
            @endisset
        </div>
    @endif

    {{-- Title --}}
    <h3 class="text-base font-semibold text-slate-900">
        {{ $title }}
    </h3>

    {{-- Description --}}
    @if ($description)
        <p class="mt-1 max-w-md text-sm text-slate-500">
            {{ $description }}
        </p>
    @endif

    {{-- Actions --}}
    @isset($actions)
        <div class="mt-4 flex items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>

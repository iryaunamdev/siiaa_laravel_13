{{--
|--------------------------------------------------------------------------
| UI Component: Tabs (contenedor)
|--------------------------------------------------------------------------
|
| Propósito:
| Contenedor de elementos tipo pestañas para navegación o control de vistas.
|
| Características:
| - Soporta orientación horizontal y vertical
| - Variantes visuales: line (default), pills, buttons
| - No maneja estado directamente (lo controla Livewire o lógica externa)
|
| Props:
| - variant: string (line | pills | buttons)
| - orientation: string (horizontal | vertical)
|
| Uso:
| <x-ui.tabs variant="pills">
|     <x-ui.tabs.item ...>General</x-ui.tabs.item>
| </x-ui.tabs>
|
|--------------------------------------------------------------------------
--}}

@props([
    'variant' => 'line',
    'orientation' => 'horizontal',
])

@php
    // Definición de layout según orientación
    $orientationClasses = $orientation === 'vertical' ? 'flex flex-col gap-1' : 'flex flex-wrap items-center gap-2';

    // Estilos base por variante
    $baseClasses = match ($variant) {
        'pills' => 'rounded-2xl bg-slate-100 p-1',
        'buttons' => 'gap-2',
        default => 'border-b border-slate-200',
    };
@endphp

<div {{ $attributes->merge([
    'class' => trim("{$orientationClasses} {$baseClasses}"),
]) }}>
    {{ $slot }}
</div>

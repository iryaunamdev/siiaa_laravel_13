{{--
|--------------------------------------------------------------------------
| UI Component: Tabs Item
|--------------------------------------------------------------------------
|
| Propósito:
| Representa una pestaña individual dentro de x-ui.tabs.
|
| Características:
| - Puede funcionar como enlace (href) o acción (wire:click)
| - Soporta estados: activo, deshabilitado
| - Hereda automáticamente la variante visual desde x-ui.tabs mediante @aware
|
| Props:
| - active: bool
| - href: string|null
| - wireClick: string|null
| - disabled: bool
|
| Props heredadas:
| - variant: string (line | pills | buttons)
|
|--------------------------------------------------------------------------
--}}

@aware([
    'variant' => 'line',
])

@props([
    'active' => false,
    'href' => null,
    'wireClick' => null,
    'disabled' => false,
])

@php
    $base =
        'inline-flex items-center justify-center gap-2 text-sm font-medium transition duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500/30 disabled:pointer-events-none disabled:opacity-50';

    $classes = match ($variant) {
        'pills' => $active
            ? 'rounded-xl bg-white px-4 py-2 text-blue-700 shadow-sm'
            : 'rounded-xl px-4 py-2 text-slate-600 hover:bg-white/70 hover:text-slate-900',

        'buttons' => $active
            ? 'rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 text-blue-700 shadow-sm'
            : 'rounded-xl border border-slate-200 bg-white px-4 py-2 text-slate-600 hover:border-blue-200 hover:text-blue-700',

        default => $active
            ? 'border-b-2 border-blue-600 px-4 py-3 text-blue-700'
            : 'border-b-2 border-transparent px-4 py-3 text-slate-500 hover:border-slate-300 hover:text-slate-800',
    };

    $finalClasses = "{$base} {$classes}";
@endphp

@if ($href && !$disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $finalClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button type="button" @if ($wireClick && !$disabled) wire:click="{{ $wireClick }}" @endif
        @disabled($disabled) {{ $attributes->merge(['class' => $finalClasses]) }}>
        {{ $slot }}
    </button>
@endif

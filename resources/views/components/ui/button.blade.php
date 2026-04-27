{{--
|--------------------------------------------------------------------------
| UI Button - SIIAA
|--------------------------------------------------------------------------
| Props:
| - variant: primary | secondary | danger | ghost | link
| - size: sm | md
| - href: string|null (si existe → renderiza <a>)
| - type: button | submit | reset (solo cuando es <button>)
| - disabled: bool
| - iconOnly: bool
|
| Slots:
| - default: contenido del botón
| - icon: icono opcional (svg)
|
|--------------------------------------------------------------------------
| Uso básico:
|
| <x-ui.button>Guardar</x-ui.button>
|
| <x-ui.button variant="secondary">Cancelar</x-ui.button>
|
| <x-ui.button variant="danger">Eliminar</x-ui.button>
|
|--------------------------------------------------------------------------
| Tamaños:
|
| <x-ui.button size="sm">Pequeño</x-ui.button>
|
|--------------------------------------------------------------------------
| Como enlace:
|
| <x-ui.button href="{{ route('dashboard') }}">
|     Ir al dashboard
| </x-ui.button>
|
|--------------------------------------------------------------------------
| Link visual (sin fondo):
|
| <x-ui.button variant="link" href="#">
|     Volver
| </x-ui.button>
|
|--------------------------------------------------------------------------
| Icono:
|
| <x-ui.button>
|     <x-slot:icon>
|         <svg class="h-4 w-4">...</svg>
|     </x-slot:icon>
|     Guardar
| </x-ui.button>
|
|--------------------------------------------------------------------------
| Solo icono:
|
| <x-ui.button variant="link" icon-only>
|     <x-slot:icon>
|         <svg class="h-4 w-4">...</svg>
|     </x-slot:icon>
| </x-ui.button>
|
|--------------------------------------------------------------------------
| Livewire:
|
| <x-ui.button wire:click="save">
|     Guardar
| </x-ui.button>
|
|--------------------------------------------------------------------------
| Disabled:
|
| <x-ui.button disabled>Guardar</x-ui.button>
|
| <x-ui.button :disabled="$isSaving">Procesar</x-ui.button>
|
|--------------------------------------------------------------------------
| Loading recomendado (Livewire):
|
| <x-ui.button wire:click="save" wire:loading.attr="disabled">
|     Guardar
| </x-ui.button>
|
|--------------------------------------------------------------------------
--}}

@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'iconOnly' => false,
])

@php
    // Tamaños
    $sizeClasses = match ($size) {
        'sm' => $iconOnly ? 'p-2' : 'px-3 py-2 text-sm',
        default => $iconOnly ? 'p-2.5' : 'px-4 py-2 text-sm',
    };

    // Variantes
    $variantClasses = match ($variant) {
        'secondary' => 'bg-slate-200 text-slate-800 hover:bg-slate-300',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        'ghost' => 'text-slate-700 hover:bg-slate-100',
        'link' => 'text-slate-600 hover:text-slate-900 hover:bg-slate-100',
        default => 'bg-blue-600 text-white hover:bg-blue-700', // primary
    };

    // Disabled
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '';

    // Base común
    $baseClasses = "inline-flex items-center justify-center gap-2 rounded-lg transition-all duration-200 ease-out focus:outline-none {$sizeClasses} {$variantClasses} {$disabledClasses}";
@endphp

@if ($href)
    {{-- LINK --}}
    <a href="{{ $disabled ? '#' : $href }}" {{ $attributes->merge([
        'class' => $baseClasses,
    ]) }}
        @if ($disabled) aria-disabled="true" @endif>
        @isset($icon)
            <span class="shrink-0">
                {{ $icon }}
            </span>
        @endisset

        @unless ($iconOnly)
            <span>{{ $slot }}</span>
        @endunless
    </a>
@else
    {{-- BUTTON --}}
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => $baseClasses,
        ]) }}>
        @isset($icon)
            <span class="shrink-0">
                {{ $icon }}
            </span>
        @endisset

        @unless ($iconOnly)
            <span>{{ $slot }}</span>
        @endunless
    </button>
@endif

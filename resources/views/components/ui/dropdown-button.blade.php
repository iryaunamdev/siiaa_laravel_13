@props([
    'danger' => false,
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.dropdown-button
    |--------------------------------------------------------------------------
    | Elemento tipo botón para menús desplegables.
    | Útil para logout, acciones Livewire o formularios.
    |
    | Uso:
    | <x-ui.dropdown-button wire:click="delete" danger>
    |     Eliminar
    | </x-ui.dropdown-button>
    */

    $classes = $danger
        ? 'text-red-600 hover:bg-red-50 hover:text-red-700'
        : 'text-zinc-700 hover:bg-zinc-50 hover:text-zinc-900';
@endphp

<button type="button"
    {{ $attributes->merge([
        'class' => "block w-full px-4 py-2.5 text-left text-sm transition {$classes} focus:outline-none focus-visible:bg-zinc-50",
    ]) }}>
    {{ $slot }}
</button>

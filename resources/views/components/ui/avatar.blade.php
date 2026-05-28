@props([
    'size' => 'h-8 w-8',
    'variant' => 'default',
    'name' => '',
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.avatar
    |--------------------------------------------------------------------------
    | Representación visual de usuario.
    |
    | Usa iniciales desde el modelo:
    | $user->initials()
    |
    | Uso:
    | <x-ui.avatar :user="auth()->user()" />
    |
    | Variantes:
    | default, neutral, dark, success, warning, danger
    */

    $variants = [
        'default' => 'bg-sky-100 text-sky-700 ring-sky-200',
        'neutral' => 'bg-zinc-100 text-zinc-700 ring-zinc-200',
        'dark' => 'bg-zinc-800 text-white ring-zinc-700',
        'success' => 'bg-green-100 text-green-700 ring-green-200',
        'warning' => 'bg-yellow-100 text-yellow-700 ring-yellow-200',
        'danger' => 'bg-red-100 text-red-700 ring-red-200',
    ];

    $variantClass = $variants[$variant] ?? $variants['default'];

@endphp

<div {{ $attributes->merge([
    'class' => "flex {$size} shrink-0 items-center justify-center rounded-full font-semibold uppercase ring-1 {$variantClass}",
]) }}
    title="{{ $name }}" aria-label="{{ $name }}">
    {{ $slot }}
</div>

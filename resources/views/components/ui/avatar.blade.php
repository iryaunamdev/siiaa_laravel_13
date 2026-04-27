@props([
    'user' => null,
    'size' => 'md',
    'variant' => 'default',
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

    $sizes = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-9 w-9 text-sm',
        'lg' => 'h-11 w-11 text-base',
    ];

    $variants = [
        'default' => 'bg-sky-100 text-sky-700 ring-sky-200',
        'neutral' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'dark' => 'bg-slate-800 text-white ring-slate-700',
        'success' => 'bg-green-100 text-green-700 ring-green-200',
        'warning' => 'bg-yellow-100 text-yellow-700 ring-yellow-200',
        'danger' => 'bg-red-100 text-red-700 ring-red-200',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $variantClass = $variants[$variant] ?? $variants['default'];

    $name = $user?->name ?? 'Usuario';
    $initials = $user ? $user->initials() : 'U';
@endphp

<div {{ $attributes->merge([
    'class' => "flex {$sizeClass} shrink-0 items-center justify-center rounded-full font-semibold uppercase ring-1 {$variantClass}",
]) }}
    title="{{ $name }}" aria-label="{{ $name }}">
    {{ $initials }}
</div>

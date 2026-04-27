@props([
    'href' => '#',
    'danger' => false,
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.dropdown-link
    |--------------------------------------------------------------------------
    | Elemento tipo enlace para menús desplegables.
    |
    | Uso:
    | <x-ui.dropdown-link href="{{ route('profile.show') }}">
    |     Perfil
    | </x-ui.dropdown-link>
    |
    | Uso peligroso:
    | <x-ui.dropdown-link danger>
    |     Eliminar
    | </x-ui.dropdown-link>
    */

    $classes = $danger
        ? 'text-red-600 hover:bg-red-50 hover:text-red-700'
        : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900';
@endphp

<a href="{{ $href }}"
    {{ $attributes->merge([
        'class' => "block px-4 py-2.5 text-sm transition {$classes}",
    ]) }}>
    {{ $slot }}
</a>

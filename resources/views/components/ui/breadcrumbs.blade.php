@props([
    'items' => [],
    'homeLabel' => 'Inicio',
    'homeRoute' => 'dashboard',
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.breadcrumbs
    |--------------------------------------------------------------------------
    | Componente atómico para navegación jerárquica.
    |
    | Uso básico automático:
    | <x-ui.breadcrumbs />
    |
    | Uso con items:
    | <x-ui.breadcrumbs :items="[
    |     ['label' => 'Usuarios', 'url' => route('users.index')],
    |     ['label' => 'Crear usuario'],
    | ]" />
    |
    | Estructura de cada item:
    | [
    |     'label' => 'Texto visible',
    |     'url' => 'ruta opcional'
    | ]
    |
    | Si no se pasa url, se muestra como elemento actual.
    */

    $homeUrl = Route::has($homeRoute) ? route($homeRoute) : '#';
@endphp

<nav {{ $attributes->merge([
    'class' => 'hidden min-w-0 items-center gap-2 text-sm text-zinc-500 sm:flex',
]) }}
    aria-label="Breadcrumb">
    <a href="{{ $homeUrl }}" class="transition hover:text-zinc-800">
        {{ $homeLabel }}
    </a>

    @foreach ($items as $item)
        <span class="text-zinc-300">/</span>

        @if (!empty($item['url']))
            <a href="{{ $item['url'] }}" class="truncate transition hover:text-zinc-800">
                {{ $item['label'] }}
            </a>
        @else
            <span class="truncate font-medium text-zinc-700">
                {{ $item['label'] }}
            </span>
        @endif
    @endforeach
</nav>

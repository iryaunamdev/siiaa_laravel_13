@props([
    'title' => null,
    'description' => null,
    'variant' => 'default',
    'padding' => true,
    'size' => 'full',

    /*
    |--------------------------------------------------------------------------
    | Collapsible
    |--------------------------------------------------------------------------
    | Permite contraer/expandir el contenido del panel.
    |
    | - collapsible: activa el comportamiento colapsable.
    | - defaultOpen: define si inicia abierto o cerrado.
    | - persist: guarda el estado en localStorage.
    | - persistKey: llave única para persistir el estado.
    */
    'collapsible' => false,
    'defaultOpen' => true,
    'persist' => false,
    'persistKey' => null,
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.panel
    |--------------------------------------------------------------------------
    | Contenedor base para secciones del sistema SIIAA.
    |
    | Por defecto ocupa todo el ancho disponible del main.
    |
    | Ejemplo:
    | <x-ui.panel title="Usuarios">
    |     ...
    | </x-ui.panel>
    |
    | Ejemplo colapsable:
    | <x-ui.panel
    |     title="Filtros"
    |     collapsible
    |     :default-open="false"
    |     persist
    |     persist-key="users.filters"
    | >
    |     ...
    | </x-ui.panel>
    */

    $sizes = [
        'ch' => 'max-w-screen-sm',
        'md' => 'max-w-screen-md',
        'lg' => 'max-w-screen-lg',
        'xl' => 'max-w-screen-xl',
        '2xl' => 'max-w-screen-2xl',
        '3xl' => 'max-w-screen-3xl',
        'full' => 'w-full',
    ];

    $variants = [
        'default' => [
            'wrapper' => 'rounded-md border-slate-200 bg-white',
            'header' => 'rounded-t-md border-slate-200 bg-white',
            'title' => 'text-slate-900',
            'description' => 'text-slate-500',
        ],
        'form' => [
            'wrapper' => 'rounded-md border-slate-200 bg-white',
            'header' => 'rounded-t-md border-slate-200 bg-slate-50',
            'title' => 'text-slate-900',
            'description' => 'text-slate-500',
        ],
        'subtle' => [
            'wrapper' => 'rounded-md border-transparent bg-slate-50',
            'header' => 'rounded-t-md border-transparent bg-slate-50',
            'title' => 'text-slate-800',
            'description' => 'text-slate-500',
        ],
    ];

    $sizeClass = $sizes[$size] ?? $sizes['full'];
    $variantClass = $variants[$variant] ?? $variants['default'];
    $paddingClass = $padding ? 'p-5' : '';

    $hasHeader = $title || $description || isset($header) || isset($actions) || $collapsible;
    $resolvedPersistKey = $persistKey ?: 'siiaa.panel.' . md5($attributes->get('id') ?? ($title ?? uniqid()));
@endphp

<div @if ($collapsible) x-data="{
            open: {{ $defaultOpen ? 'true' : 'false' }},
            persist: {{ $persist ? 'true' : 'false' }},
            key: @js($resolvedPersistKey),

            init() {
                if (this.persist) {
                    const stored = localStorage.getItem(this.key);

                    if (stored !== null) {
                        this.open = stored === 'true';
                    }

                    this.$watch('open', value => {
                        localStorage.setItem(this.key, value ? 'true' : 'false');
                    });
                }
            },

            toggle() {
                this.open = ! this.open;
            }
        }" @endif
    {{ $attributes->merge([
        'class' => "w-full mx-auto {$sizeClass} rounded-md border shadow-sm {$variantClass['wrapper']}",
    ]) }}>
    {{-- Header --}}
    @if ($hasHeader)
        <div class="flex items-start justify-between gap-4 border-b px-5 py-4 {{ $variantClass['header'] }}">
            <div class="min-w-0">
                @isset($header)
                    {{ $header }}
                @else
                    @if ($title)
                        <h3 class="text-lg font-semibold {{ $variantClass['title'] }}">
                            {{ $title }}
                        </h3>
                    @endif

                    @if ($description)
                        <p class="mt-1 text-sm {{ $variantClass['description'] }}">
                            {{ $description }}
                        </p>
                    @endif
                @endisset
            </div>

            <div class="flex items-center gap-2">
                @isset($actions)
                    {{ $actions }}
                @endisset

                @if ($collapsible)
                    <button type="button" x-on:click="toggle()"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                        :aria-expanded="open.toString()" aria-label="Contraer o expandir panel">
                        <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- Body --}}
    <div @if ($collapsible) x-show="open"
            x-collapse @endif class="{{ $paddingClass }}">
        {{ $slot }}
    </div>

    {{-- Footer --}}
    @isset($footer)
        <div @if ($collapsible) x-show="open"
                x-collapse @endif
            class="border-t border-slate-200 bg-slate-50 px-5 py-4">
            {{ $footer }}
        </div>
    @endisset
</div>

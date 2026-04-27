{{--
|--------------------------------------------------------------------------
| UI Component: Accordion Item
|--------------------------------------------------------------------------
|
| Propósito:
| Representa una sección individual colapsable dentro de x-ui.accordion.
|
| Características:
| - Usa x-collapse para animación suave.
| - Puede incluir título, descripción, icono y contenido extenso.
| - Hereda variant desde el contenedor mediante @aware.
|
| Props:
| - name: string|null
|   Identificador único del item dentro del accordion.
|
| - title: string|null
|   Título visible del item.
|
| - description: string|null
|   Texto secundario opcional.
|
| - open: bool
|   Define si inicia abierto cuando no hay estado persistido.
|
| - disabled: bool
|   Deshabilita interacción.
|
| Slots:
| - icon
| - actions
| - default
|
|--------------------------------------------------------------------------
--}}

@aware([
    'variant' => 'default',
])

@props([
    'name' => null,
    'title' => null,
    'description' => null,
    'open' => false,
    'disabled' => false,
])

@php
    $itemName = $name ?: 'item-' . uniqid();

    $styles = [
        'default' => [
            'wrapper' => 'rounded-xl border border-slate-200 bg-white shadow-sm',
            'button' => 'px-5 py-4 hover:bg-slate-50',
            'title' => 'text-slate-900',
            'description' => 'text-slate-500',
            'body' => 'border-t border-slate-100 px-5 py-4',
        ],

        'form' => [
            'wrapper' => 'rounded-xl border border-slate-200 bg-white shadow-sm',
            'button' => 'px-5 py-4 hover:bg-slate-50',
            'title' => 'text-slate-900',
            'description' => 'text-slate-500',
            'body' => 'border-t border-slate-100 px-5 py-5',
        ],

        'filter' => [
            'wrapper' => 'rounded-xl border border-slate-200 bg-slate-50',
            'button' => 'px-4 py-3 hover:bg-slate-100',
            'title' => 'text-slate-800',
            'description' => 'text-slate-500',
            'body' => 'border-t border-slate-200 px-4 py-4 bg-white',
        ],

        'nav' => [
            'wrapper' => 'rounded-lg border border-transparent bg-transparent shadow-none',
            'button' => 'px-3 py-2 hover:bg-slate-100',
            'title' => 'text-slate-700',
            'description' => 'text-slate-400',
            'body' => 'pl-5 pr-2 py-1',
        ],
    ];

    $style = $styles[$variant] ?? $styles['default'];
@endphp

<div x-init="@if ($open) openItems = multiple ? [...new Set([...openItems, @js($itemName)])] : [@js($itemName)] @endif" class="{{ $style['wrapper'] }}">
    <button type="button" x-on:click="@if (!$disabled) toggle(@js($itemName)) @endif"
        class="flex w-full items-center justify-between gap-4 rounded-xl text-left transition {{ $style['button'] }} disabled:pointer-events-none disabled:opacity-50"
        @disabled($disabled) :aria-expanded="isOpen(@js($itemName)).toString()">
        <div class="flex min-w-0 items-start gap-3">
            @isset($icon)
                <div class="mt-0.5 shrink-0 text-slate-400">
                    {{ $icon }}
                </div>
            @endisset

            <div class="min-w-0">
                @if ($title)
                    <div class="text-sm font-semibold {{ $style['title'] }}">
                        {{ $title }}
                    </div>
                @endif

                @if ($description)
                    <div class="mt-0.5 text-xs {{ $style['description'] }}">
                        {{ $description }}
                    </div>
                @endif
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2">
            @isset($actions)
                <div x-on:click.stop>
                    {{ $actions }}
                </div>
            @endisset

            <svg class="h-4 w-4 text-slate-400 transition-transform duration-200"
                :class="{ 'rotate-180': isOpen(@js($itemName)) }" xmlns="http://www.w3.org/2000/svg"
                fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>

    <div x-show="isOpen(@js($itemName))" x-collapse>
        <div class="{{ $style['body'] }}">
            {{ $slot }}
        </div>
    </div>
</div>

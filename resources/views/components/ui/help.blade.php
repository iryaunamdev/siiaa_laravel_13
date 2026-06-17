@props([
    'text' => null,
    'position' => 'top',
    'icon' => true,
    'width' => 'w-72',
    'html' => false,
])

@php
    $positionClasses = match ($position) {
        'bottom' => 'top-full mt-2',
        'left' => 'right-full mr-2 top-1/2 -translate-y-1/2',
        'right' => 'left-full ml-2 top-1/2 -translate-y-1/2',
        default => 'bottom-full mb-2',
    };
@endphp

<span class="relative inline-flex items-center" x-data="{ open: false }">
    <button type="button"
        class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-zinc-300 bg-white text-[11px] font-semibold text-zinc-500 shadow-sm transition hover:border-blue-400 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
        x-on:mouseenter="open = true" x-on:mouseleave="open = false" x-on:focus="open = true" x-on:blur="open = false"
        x-on:click="open = !open" aria-label="Mostrar ayuda">
        @if ($icon)
            ?
        @endif
    </button>

    <span x-cloak x-show="open" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
        class="absolute z-50 {{ $positionClasses }} {{ $width }} rounded-xl border border-zinc-200 bg-white px-3 py-2 text-left text-xs leading-relaxed text-zinc-600 shadow-xl ring-1 ring-zinc-900/5">
        @if ($text)
            @if ($html)
                {!! $text !!}
            @else
                {{ $text }}
            @endif
        @else
            {{ $slot }}
        @endif
    </span>
</span>

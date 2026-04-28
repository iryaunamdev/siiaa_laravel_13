@props([
    'maxWidth' => '2xl',
])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        '3xl' => 'max-w-3xl',
        '4xl' => 'max-w-4xl',
        '5xl' => 'max-w-5xl',
        default => 'max-w-2xl',
    };
@endphp

<div x-data="{ show: @entangle($attributes->wire('model')) }" x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6"
    style="display: none;">
    {{-- Overlay --}}
    <div x-show="show" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="absolute inset-0 bg-slate-900/45 backdrop-blur-[2px]" x-on:click="show = false"></div>

    {{-- Contenedor del modal --}}
    <div x-show="show" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        class="relative w-full {{ $maxWidthClass }} rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200/70">
        {{-- Header --}}
        @isset($title)
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">
                    {{ $title }}
                </h2>
            </div>
        @endisset

        {{-- Body --}}
        <div class="px-6 py-5">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        @isset($footer)
            <div class="flex items-center justify-end gap-2 border-t border-slate-100 px-6 py-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>

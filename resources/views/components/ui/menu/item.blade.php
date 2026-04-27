@props([
    'href' => '#',
    'active' => false,
])

@php
    $classes = $active ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';

    $base = 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors duration-200';
@endphp

<li>
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $base . ' ' . $classes]) }}>
        @isset($icon)
            <span class="shrink-0 text-slate-500">
                {{ $icon }}
            </span>
        @endisset

        <span class="truncate">
            {{ $slot }}
        </span>

        @isset($badge)
            <span class="ml-auto text-xs text-slate-400">
                {{ $badge }}
            </span>
        @endisset
    </a>
</li>

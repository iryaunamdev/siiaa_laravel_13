@props([
    'style' => 'inline',
    'align' => 'center',
    'size' => 'size-12',
    'text' => 'text-4xl',
    'color' => 'text-zinc-400',
])

@php
    $styleCSS = match ($style) {
        'inline' => 'flex-row',
        'block' => 'flex-col',
        default => 'flex-row',
    };

    $alignCSS = match ($align) {
        'left' => $style === 'block' ? 'items-start' : 'justify-start',

        'right' => $style === 'block' ? 'items-end' : 'justify-end',

        default => $style === 'block' ? 'items-center' : 'justify-center',
    };
@endphp

<div {{ $attributes->class(['flex w-full overflow-visible leading-none', $styleCSS, $alignCSS]) }}>
    <svg class="{{ $size }} {{ $color }} shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
        fill="none" aria-hidden="true">
        <path
            d="M21 5C21 6.657 16.971 8 12 8S3 6.657 3 5m18 0c0-1.657-4.029-3-9-3S3 3.343 3 5m18 0v14c0 1.66-4 3-9 3s-9-1.34-9-3V5m18 7c0 1.66-4 3-9 3s-9-1.34-9-3"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>

    <span class="font-light {{ $text }} {{ $color }}">
        SIIAA
    </span>
</div>

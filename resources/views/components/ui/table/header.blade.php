@props([
    'align' => 'left',
    'width' => null,
    'nowrap' => true,

    // Sort
    'sortable' => false,
    'sortField' => null,
    'currentSort' => null,
    'currentDirection' => 'asc',
])

@php
    $alignClasses =
        [
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right',
        ][$align] ?? 'text-left';

    $nowrapClass = $nowrap ? 'whitespace-nowrap' : '';

    $isSorted = $sortable && $sortField && $currentSort === $sortField;

    $nextDirection = $isSorted && $currentDirection === 'asc' ? 'desc' : 'asc';

    $classes = trim("
        px-[var(--table-cell-px)]
        py-[var(--table-cell-py)]
        text-xs font-semibold uppercase tracking-wide text-slate-500
        {$alignClasses}
        {$nowrapClass}
        {$width}
    ");
@endphp

<th {{ $attributes->merge(['class' => $classes]) }}>
    @if ($sortable && $sortField)
        <button type="button" wire:click="sortBy('{{ $sortField }}', '{{ $nextDirection }}')"
            class="inline-flex items-center gap-1 rounded-md text-inherit transition hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
            <span>{{ $slot }}</span>

            @if ($isSorted)
                <span class="text-[0.65rem]">
                    {{ $currentDirection === 'asc' ? '▲' : '▼' }}
                </span>
            @else
                <span class="text-[0.65rem] text-slate-300">
                    ⇅
                </span>
            @endif
        </button>
    @else
        {{ $slot }}
    @endif
</th>

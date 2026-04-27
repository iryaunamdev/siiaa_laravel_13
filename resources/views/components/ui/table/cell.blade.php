@props([
    'align' => 'left',
    'width' => null,
    'nowrap' => false,
])

@php
    $alignClasses =
        [
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right',
        ][$align] ?? 'text-left';

    $nowrapClass = $nowrap ? 'whitespace-nowrap' : '';
@endphp

<td
    {{ $attributes->merge([
        'class' => trim(
            "px-[var(--table-cell-px)] py-[var(--table-cell-py)] align-middle text-sm text-slate-700 {$alignClasses} {$nowrapClass} {$width}",
        ),
    ]) }}>
    {{ $slot }}
</td>

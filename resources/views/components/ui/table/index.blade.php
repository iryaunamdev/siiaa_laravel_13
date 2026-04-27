@props([
    'density' => 'md',
    'layout' => 'auto', // auto | fixed
    'striped' => false,
    'hover' => true,
])

@php
    $densityClasses =
        [
            'xs' => '[--table-cell-px:0.5rem] [--table-cell-py:0.35rem]',
            'sm' => '[--table-cell-px:0.75rem] [--table-cell-py:0.5rem]',
            'md' => '[--table-cell-px:1rem] [--table-cell-py:0.75rem]',
            'lg' => '[--table-cell-px:1.25rem] [--table-cell-py:1rem]',
        ][$density] ?? '[--table-cell-px:1rem] [--table-cell-py:0.75rem]';

    $layoutClass = $layout === 'fixed' ? 'table-fixed' : 'table-auto';
@endphp

<div
    {{ $attributes->merge([
        'class' => 'w-full overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm',
    ]) }}>
    <table class="w-full {{ $layoutClass }} {{ $densityClasses }}">
        {{ $slot }}
    </table>
</div>

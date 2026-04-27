{{--
|--------------------------------------------------------------------------
| UI Component: Pagination
|--------------------------------------------------------------------------
|
| Propósito:
| Wrapper visual para paginadores de Laravel/Livewire.
|
| Uso principal:
| - Tablas
| - Listados administrativos
| - Catálogos
|
| Props:
| - paginator: LengthAwarePaginator|null
| - simple: bool
| - align: left | center | right | between
|
| Nota:
| Este componente NO reimplementa la lógica de paginación.
| Solo envuelve y estandariza la presentación visual.
|
|--------------------------------------------------------------------------
--}}

@props([
    'paginator' => null,
    'simple' => false,
    'align' => 'between',
])

@php
    $alignClasses =
        [
            'left' => 'justify-start',
            'center' => 'justify-center',
            'right' => 'justify-end',
            'between' => 'justify-between',
        ][$align] ?? 'justify-between';
@endphp

@if ($paginator && $paginator->hasPages())
    <div
        {{ $attributes->merge([
            'class' => "flex flex-col gap-3 border-t border-slate-100 px-4 py-3 sm:flex-row sm:items-center {$alignClasses}",
        ]) }}>
        @if (!$simple && method_exists($paginator, 'firstItem'))
            <p class="text-sm text-slate-500">
                Mostrando
                <span class="font-medium text-slate-700">{{ $paginator->firstItem() }}</span>
                a
                <span class="font-medium text-slate-700">{{ $paginator->lastItem() }}</span>
                de
                <span class="font-medium text-slate-700">{{ $paginator->total() }}</span>
                registros
            </p>
        @endif

        <div class="flex justify-end">
            {{ $paginator->links() }}
        </div>
    </div>
@endif

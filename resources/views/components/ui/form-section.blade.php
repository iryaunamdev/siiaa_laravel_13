{{--
|--------------------------------------------------------------------------
| UI Component: Form Section
|--------------------------------------------------------------------------
|
| Propósito:
| Agrupar campos de formulario bajo una sección clara y reutilizable.
|
| Uso principal:
| - Formularios largos
| - Formularios administrativos
| - Secciones dentro de paneles, tabs o accordions
|
| Props:
| - title: string|null
| - description: string|null
| - columns: int|string
| - bordered: bool
| - padding: bool
|
| Slots:
| - default: campos del formulario
| - actions: botones o acciones secundarias
|
|--------------------------------------------------------------------------
--}}

@props([
    'title' => null,
    'description' => null,
    'columns' => 2,
    'bordered' => true,
    'padding' => true,
])

@php
    $columnsClass = match ((string) $columns) {
        '1' => 'grid-cols-1',
        '2' => 'grid-cols-1 md:grid-cols-2',
        '3' => 'grid-cols-1 md:grid-cols-3',
        '4' => 'grid-cols-1 md:grid-cols-4',
        default => 'grid-cols-1 md:grid-cols-2',
    };

    $borderClass = $bordered ? 'border border-slate-200 rounded-xl' : '';
    $paddingClass = $padding ? 'p-5' : '';
@endphp

<section {{ $attributes->merge([
    'class' => trim("bg-white {$borderClass} {$paddingClass}"),
]) }}>
    @if ($title || $description || isset($actions))
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                @if ($title)
                    <h3 class="text-base font-semibold text-slate-900">
                        {{ $title }}
                    </h3>
                @endif

                @if ($description)
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $description }}
                    </p>
                @endif
            </div>

            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    <div class="grid {{ $columnsClass }} gap-4">
        {{ $slot }}
    </div>
</section>

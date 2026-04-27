@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'value' => null,

    'help' => null,
    'helpText' => null,
    'error' => null,

    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'compact' => false,

    'min' => null,
    'max' => null,

    'disablePast' => false,
    'disableFuture' => false,

    // Días bloqueados: 0 = domingo, 1 = lunes, ..., 6 = sábado
    'disabledDays' => [],
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.input-date
    |--------------------------------------------------------------------------
    | Input nativo para fechas. No depende de librerías externas.
    |
    | Formato recomendado para Laravel:
    | YYYY-MM-DD
    |
    | Uso básico:
    | <x-ui.input-date
    |     name="fecha_inicio"
    |     label="Fecha de inicio"
    |     wire:model="fecha_inicio"
    | />
    |
    | Con rango permitido:
    | <x-ui.input-date
    |     name="fecha_inicio"
    |     label="Fecha inicial"
    |     min="2026-01-01"
    |     max="2026-12-31"
    |     wire:model="fecha_inicio"
    | />
    |
    | Bloquear fechas pasadas:
    | <x-ui.input-date
    |     name="fecha_solicitada"
    |     label="Fecha solicitada"
    |     disable-past
    |     wire:model="fecha_solicitada"
    | />
    |
    | Bloquear fechas futuras:
    | <x-ui.input-date
    |     name="fecha_nacimiento"
    |     label="Fecha de nacimiento"
    |     disable-future
    |     wire:model="fecha_nacimiento"
    | />
    |
    | Bloquear días específicos:
    | <x-ui.input-date
    |     name="fecha_evento"
    |     label="Fecha del evento"
    |     :disabled-days="[0, 6]"
    |     wire:model="fecha_evento"
    | />
    |
    | Nota:
    | El calendario visual depende del navegador/sistema operativo.
    | Para calendario visual avanzado en español, se evaluará un datepicker externo
    | únicamente si el proyecto lo requiere más adelante.
    */

    $id = $id ?? $name;
    $fieldError = $error ?? ($name ? $errors->first($name) : null);

    $today = now()->toDateString();

    if ($disablePast && !$min) {
        $min = $today;
    }

    if ($disableFuture && !$max) {
        $max = $today;
    }

    $inputClasses = $compact ? 'h-9 px-3 py-1.5 text-sm' : 'h-10 px-3 py-2 text-sm';

    $baseClasses =
        'block w-full rounded-xl border bg-white text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500';

    $stateClasses = $fieldError
        ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20'
        : 'border-slate-300 focus:border-sky-500 focus:ring-sky-500/20';
@endphp

<div x-data="{
    disabledDays: @js($disabledDays),

    validateDay(event) {
        if (!event.target.value || this.disabledDays.length === 0) return;

        const selected = new Date(event.target.value + 'T00:00:00');
        const day = selected.getDay();

        if (this.disabledDays.includes(day)) {
            event.target.value = '';
            event.target.dispatchEvent(new Event('input', { bubbles: true }));

            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: 'warning',
                    title: 'Fecha no permitida',
                    message: 'El día seleccionado no está disponible.'
                }
            }));
        }
    }
}" class="space-y-1.5">
    @if ($label)
        <div class="flex items-center gap-2">
            <label for="{{ $id }}" class="block text-sm font-medium text-slate-700">
                {{ $label }}

                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            @if ($helpText)
                <div class="relative inline-flex" x-data="{ open: false }">
                    <button type="button" x-on:mouseenter="open = true" x-on:mouseleave="open = false"
                        x-on:focus="open = true" x-on:blur="open = false"
                        class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-slate-300 text-xs font-semibold text-slate-500 hover:bg-slate-50"
                        aria-label="Ayuda">
                        ?
                    </button>

                    <div x-show="open" x-transition.opacity.duration.150ms
                        class="absolute left-0 top-6 z-30 w-64 rounded-xl border border-slate-200 bg-white p-3 text-xs leading-5 text-slate-600 shadow-lg">
                        {{ $helpText }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    <input id="{{ $id }}" name="{{ $name }}" type="date" value="{{ old($name, $value) }}"
        @if ($min) min="{{ $min }}" @endif
        @if ($max) max="{{ $max }}" @endif @required($required)
        @disabled($disabled) @readonly($readonly) x-on:change="validateDay($event)"
        {{ $attributes->merge([
            'class' => $baseClasses . ' ' . $inputClasses . ' ' . $stateClasses,
        ]) }}>

    @if ($help)
        <p class="text-xs leading-5 text-slate-500">
            {{ $help }}
        </p>
    @endif

    @if ($fieldError)
        <p class="text-xs leading-5 text-red-600">
            {{ $fieldError }}
        </p>
    @endif
</div>

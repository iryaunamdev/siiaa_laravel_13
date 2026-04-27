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

    // Step en segundos:
    // 60 = 1 minuto
    // 300 = 5 minutos
    // 900 = 15 minutos
    // 1800 = 30 minutos
    'step' => 60,
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.input-time
    |--------------------------------------------------------------------------
    | Input nativo para captura de hora. No depende de librerías externas.
    |
    | Formato recomendado para Laravel:
    | HH:MM
    |
    | Uso básico:
    | <x-ui.input-time
    |     name="hora_inicio"
    |     label="Hora de inicio"
    |     wire:model="hora_inicio"
    | />
    |
    | Con rango permitido:
    | <x-ui.input-time
    |     name="hora_inicio"
    |     label="Hora inicial"
    |     min="08:00"
    |     max="18:00"
    |     wire:model="hora_inicio"
    | />
    |
    | Hora final dependiente de hora inicial:
    | <x-ui.input-time
    |     name="hora_fin"
    |     label="Hora final"
    |     min="{{ $hora_inicio }}"
    |     max="18:00"
    |     wire:model="hora_fin"
    | />
    |
    | Intervalos de 15 minutos:
    | <x-ui.input-time
    |     name="hora_cita"
    |     label="Hora de la cita"
    |     step="900"
    |     wire:model="hora_cita"
    | />
    |
    | Nota:
    | El selector visual de hora depende del navegador/sistema operativo.
    | Para una experiencia visual avanzada se evaluará un timepicker externo
    | únicamente si el proyecto lo requiere más adelante.
    */

    $id = $id ?? $name;
    $fieldError = $error ?? ($name ? $errors->first($name) : null);

    $inputClasses = $compact ? 'h-9 px-3 py-1.5 text-sm' : 'h-10 px-3 py-2 text-sm';

    $baseClasses =
        'block w-full rounded-xl border bg-white text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500';

    $stateClasses = $fieldError
        ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20'
        : 'border-slate-300 focus:border-sky-500 focus:ring-sky-500/20';
@endphp

<div class="space-y-1.5">
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

    <input id="{{ $id }}" name="{{ $name }}" type="time" value="{{ old($name, $value) }}"
        @if ($min) min="{{ $min }}" @endif
        @if ($max) max="{{ $max }}" @endif
        @if ($step) step="{{ $step }}" @endif @required($required)
        @disabled($disabled) @readonly($readonly)
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

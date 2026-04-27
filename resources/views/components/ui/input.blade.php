{{--
|--------------------------------------------------------------------------
| UI Input - SIIAA
|--------------------------------------------------------------------------
| Props:
| - label: string|null
| - name: string|null
| - type: text | email | password | number | date | etc.
| - value: mixed
| - placeholder: string|null
| - help: string|null
| - helpPopover: string|null
| - helpPopoverTitle: string|null
| - error: string|null
| - required: bool
| - disabled: bool
| - size: sm | md
|--------------------------------------------------------------------------
| Uso con ayuda emergente
|--------------------------------------------------------------------------
| <x-ui.input
|     name="curp"
|     label="CURP"
|     helpPopover="Ingrese la CURP exactamente como aparece en el documento oficial. Verifique que no existan espacios adicionales y que el formato sea válido."
| />
|--------------------------------------------------------------------------
--}}

@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => null,
    'help' => null,
    'helpPopover' => null,
    'helpPopoverTitle' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'size' => 'md',
])

@php
    $inputId = $attributes->get('id') ?? ($name ?? 'input_' . uniqid());
    $popoverId = 'popover_' . str_replace(['.', '[', ']'], '_', $inputId);

    $resolvedError = $error;
    if (is_array($resolvedError)) {
        $resolvedError = collect($resolvedError)->first();
    }

    if (!$resolvedError && $name) {
        $resolvedError = $errors->first($name);
    }

    $resolvedHelp = is_array($help) ? collect($help)->filter()->implode(' ') : $help;

    $sizeClasses = match ($size) {
        'sm' => 'h-9 px-3 text-sm',
        default => 'h-10 px-3 text-sm',
    };

    $stateClasses = $resolvedError
        ? 'border-red-300 text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500'
        : 'border-slate-300 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500';

    $disabledClasses = $disabled ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white';

    $classes = implode(
        ' ',
        array_filter([
            'block w-full rounded-lg border shadow-sm transition-colors duration-200 ease-out focus:outline-none focus:ring-2/20',
            $sizeClasses,
            $stateClasses,
            $disabledClasses,
        ]),
    );
@endphp

<div class="w-full">
    @if ($label)
        <div class="mb-1.5 flex items-center gap-2">
            <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700">
                {{ $label }}

                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            @if ($helpPopover)
                <div x-data="{ open: false }" class="relative inline-flex items-center">
                    <button type="button"
                        class="inline-flex h-5 w-5 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        @mouseenter="open = true" @mouseleave="open = false" @focus="open = true" @blur="open = false"
                        @click="open = !open" :aria-expanded="open.toString()" aria-controls="{{ $popoverId }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"
                            aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7-3a1 1 0 10-2 0 1 1 0 002 0zm-2 3a1 1 0 000 2v2a1 1 0 102 0v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div id="{{ $popoverId }}" x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1" @mouseenter="open = true"
                        @mouseleave="open = false" x-cloak
                        class="absolute left-0 top-full z-30 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-4 shadow-lg">
                        @if ($helpPopoverTitle)
                            <div class="mb-1 text-sm font-semibold text-slate-800">
                                {{ $helpPopoverTitle }}
                            </div>
                        @endif

                        <div class="text-sm leading-6 text-slate-600">
                            {{ $helpPopover }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    @php
        $resolvedValue = $name ? old($name, $value) : $value;

        if (is_array($resolvedValue) || is_object($resolvedValue)) {
            $resolvedValue = '';
        }
    @endphp

    <input id="{{ $inputId }}" @if ($name) name="{{ $name }}" @endif
        type="{{ $type }}" @if (!$attributes->whereStartsWith('wire:model')->first()) value="{{ $resolvedValue }}" @endif
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif @disabled($disabled)
        @required($required) {{ $attributes->merge(['class' => $classes])->except(['id']) }} />

    @if ($resolvedError)
        <p class="mt-1.5 text-sm text-red-600">
            {{ $resolvedError }}
        </p>
    @elseif ($resolvedHelp)
        <p class="mt-1.5 text-sm text-slate-500">
            {{ $resolvedHelp }}
        </p>
    @endif
</div>

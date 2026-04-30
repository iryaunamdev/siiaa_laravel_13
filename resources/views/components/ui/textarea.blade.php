{{--
|--------------------------------------------------------------------------
| UI Textarea - SIIAA
|--------------------------------------------------------------------------
| Props:
| - label: string|null
| - name: string|null
| - value: mixed
| - placeholder: string|null
| - help: string|null
| - helpPopover: string|null
| - helpPopoverTitle: string|null
| - error: string|null
| - required: bool
| - disabled: bool
| - rows: int
| - size: sm | md
|
|--------------------------------------------------------------------------
| Uso básico
|--------------------------------------------------------------------------
| <x-ui.textarea
|     name="description"
|     label="Descripción"
|     placeholder="Ingrese la descripción"
| />
|
|--------------------------------------------------------------------------
| Con value
|--------------------------------------------------------------------------
| <x-ui.textarea
|     name="notes"
|     label="Observaciones"
|     :value="old('notes', $record->notes ?? '')"
| />
|
|--------------------------------------------------------------------------
| Con help
|--------------------------------------------------------------------------
| <x-ui.textarea
|     name="comments"
|     label="Comentarios"
|     help="Procure redactar de forma clara y breve."
| />
|
|--------------------------------------------------------------------------
| Con popover
|--------------------------------------------------------------------------
| <x-ui.textarea
|     name="justification"
|     label="Justificación"
|     helpPopoverTitle="Criterio de llenado"
|     helpPopover="Explique de manera precisa el motivo de la solicitud, incluyendo contexto suficiente para su revisión."
| />
|
|--------------------------------------------------------------------------
| Livewire
|--------------------------------------------------------------------------
| <x-ui.textarea
|     wire:model.defer="description"
|     name="description"
|     label="Descripción"
| />
|--------------------------------------------------------------------------
--}}

@props([
    'label' => null,
    'name' => null,
    'value' => '',
    'placeholder' => null,
    'help' => null,
    'helpPopover' => null,
    'helpPopoverTitle' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
    'rows' => 4,
    'size' => 'md',
])

@php
    $textareaId = $attributes->get('id') ?? ($name ?? 'textarea_' . uniqid());
    $popoverId = 'popover_' . str_replace(['.', '[', ']'], '_', $textareaId);

    $resolvedError = $error;

    if (is_array($resolvedError)) {
        $resolvedError = implode(' ', array_filter($resolvedError));
    } elseif (is_object($resolvedError)) {
        $resolvedError = method_exists($resolvedError, '__toString') ? (string) $resolvedError : null;
    }

    if (!$resolvedError && $name) {
        $resolvedError = $errors->first($name);
    }

    $resolvedHelp = $help;

    if (is_array($resolvedHelp)) {
        $resolvedHelp = implode(' ', array_filter($resolvedHelp));
    } elseif (is_object($resolvedHelp)) {
        $resolvedHelp = method_exists($resolvedHelp, '__toString') ? (string) $resolvedHelp : null;
    }

    $resolvedHelpPopover = $helpPopover;

    if (is_array($resolvedHelpPopover)) {
        $resolvedHelpPopover = implode(' ', array_filter($resolvedHelpPopover));
    } elseif (is_object($resolvedHelpPopover)) {
        $resolvedHelpPopover = method_exists($resolvedHelpPopover, '__toString') ? (string) $resolvedHelpPopover : null;
    }

    $resolvedValue = $name ? old($name, $value) : $value;

    if (is_array($resolvedValue) || is_object($resolvedValue)) {
        $resolvedValue = '';
    }

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-2 text-sm',
        default => 'px-3 py-2.5 text-sm',
    };

    $stateClasses = $resolvedError
        ? 'border-red-300 text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-red-500'
        : 'border-slate-300 text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:ring-blue-500';

    $disabledClasses = $disabled ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white';

    $classes = implode(
        ' ',
        array_filter([
            'block w-full rounded-lg border shadow-sm transition-colors duration-200 ease-out focus:outline-none focus:ring-2/20',
            'resize-y',
            $sizeClasses,
            $stateClasses,
            $disabledClasses,
        ]),
    );
@endphp

<div class="w-full">
    @if ($label)
        <div class="mb-1.5 flex items-center gap-2">
            <label for="{{ $textareaId }}" class="block text-sm font-medium text-slate-700">
                {{ $label }}

                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            @if ($resolvedHelpPopover)
                <x-ui.help :text="$resolvedHelpPopover" position="bottom" />
            @endif
        </div>
    @endif

    <textarea id="{{ $textareaId }}" @if ($name) name="{{ $name }}" @endif
        rows="{{ $rows }}" @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @disabled($disabled) @required($required) {{ $attributes->merge(['class' => $classes])->except(['id']) }}>{{ $resolvedValue }}</textarea>

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

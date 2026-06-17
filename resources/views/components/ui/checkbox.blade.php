{{--
|--------------------------------------------------------------------------
| UI Checkbox - SIIAA
|--------------------------------------------------------------------------
| Props:
| - label: string|null
| - name: string|null
| - value: mixed
| - checked: bool|null
| - help: string|null
| - helpPopover: string|null
| - helpPopoverTitle: string|null
| - error: string|null
| - disabled: bool
|
|--------------------------------------------------------------------------
| Uso básico
|--------------------------------------------------------------------------
| <x-ui.checkbox
|     name="active"
|     label="Activo"
| />
|
|--------------------------------------------------------------------------
| Checked manual
|--------------------------------------------------------------------------
| <x-ui.checkbox
|     name="active"
|     label="Activo"
|     :checked="$user->active"
| />
|
|--------------------------------------------------------------------------
| Con value (arrays)
|--------------------------------------------------------------------------
| <x-ui.checkbox
|     name="roles[]"
|     value="admin"
|     label="Administrador"
| />
|
|--------------------------------------------------------------------------
| Livewire
|--------------------------------------------------------------------------
| <x-ui.checkbox
|     wire:model="active"
|     name="active"
|     label="Activo"
| />
|--------------------------------------------------------------------------
--}}

@props([
    'label' => null,
    'name' => null,
    'value' => 1,
    'checked' => null,
    'help' => null,
    'helpPopover' => null,
    'helpPopoverTitle' => null,
    'error' => null,
    'disabled' => false,
    'align' => 'left', // left | center | right
])

@php
    $inputId = $attributes->get('id') ?? ($name ?? 'checkbox_' . uniqid());

    $resolvedError = $error;

    if (!$resolvedError && $name) {
        $resolvedError = $errors->first($name);
    }

    $isChecked = $checked;

    if (is_null($isChecked) && $name) {
        $oldValue = old($name);

        if (is_array($oldValue)) {
            $isChecked = in_array($value, $oldValue);
        } else {
            $isChecked = (bool) $oldValue;
        }
    }

    $baseClasses = 'h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500/30 transition';
    $stateClasses = $resolvedError ? 'border-red-300 focus:ring-red-500/30' : '';
    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';

    $classes = implode(' ', array_filter([$baseClasses, $stateClasses, $disabledClasses]));

    $alignmentClasses = match ($align) {
        'center' => 'justify-center',
        'right' => 'justify-end',
        default => 'justify-start',
    };

    $hasContent = $label || $help || $helpPopover || $resolvedError;
@endphp

<div class="{{ $hasContent ? 'w-full' : 'inline-flex shrink-0' }}">
    <div class="flex items-start gap-3 {{ $alignmentClasses }}">

        <div class="{{ $hasContent ? 'pt-0.5' : '' }}">
            <input id="{{ $inputId }}" type="checkbox"
                @if ($name) name="{{ $name }}" @endif value="{{ $value }}"
                @checked($isChecked) @disabled($disabled)
                {{ $attributes->merge(['class' => $classes])->except(['id']) }} />
        </div>

        @if ($hasContent)
            <div class="flex-1">
                @if ($label || $helpPopover)
                    <div class="flex items-center gap-2">
                        @if ($label)
                            <label for="{{ $inputId }}" class="text-sm text-zinc-700">
                                {{ $label }}
                            </label>
                        @endif

                        @if ($helpPopover)
                            <x-ui.help position="bottom">
                                @if ($helpPopoverTitle)
                                    <span class="mb-1 block font-semibold text-zinc-800">
                                        {{ $helpPopoverTitle }}
                                    </span>
                                @endif

                                <span>{{ $helpPopover }}</span>
                            </x-ui.help>
                        @endif
                    </div>
                @endif

                @if ($resolvedError)
                    <p class="mt-1 text-sm text-red-600">
                        {{ $resolvedError }}
                    </p>
                @elseif ($help)
                    <p class="mt-1 text-sm text-zinc-500">
                        {{ $help }}
                    </p>
                @endif
            </div>
        @endif

    </div>
</div>

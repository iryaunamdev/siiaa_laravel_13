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
])

@php
    $inputId = $attributes->get('id') ?? ($name ?? 'checkbox_' . uniqid());
    $popoverId = 'popover_' . str_replace(['.', '[', ']'], '_', $inputId);

    $resolvedError = $error;
    if (!$resolvedError && $name) {
        $resolvedError = $errors->first($name);
    }

    // Determinar checked (soporta old() y valor explícito)
    $isChecked = $checked;

    if (is_null($isChecked) && $name) {
        $oldValue = old($name);

        if (is_array($oldValue)) {
            $isChecked = in_array($value, $oldValue);
        } else {
            $isChecked = (bool) $oldValue;
        }
    }

    $baseClasses = 'h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/30 transition';

    $stateClasses = $resolvedError ? 'border-red-300 focus:ring-red-500/30' : '';

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';

    $classes = implode(' ', array_filter([$baseClasses, $stateClasses, $disabledClasses]));
@endphp

<div class="w-full">
    <div class="flex items-start gap-3">
        <div class="pt-0.5">
            <input id="{{ $inputId }}" type="checkbox"
                @if ($name) name="{{ $name }}" @endif value="{{ $value }}"
                @checked($isChecked) @disabled($disabled)
                {{ $attributes->merge(['class' => $classes])->except(['id']) }} />
        </div>

        <div class="flex-1">
            @if ($label || $helpPopover)
                <div class="flex items-center gap-2">
                    @if ($label)
                        <label for="{{ $inputId }}" class="text-sm text-slate-700">
                            {{ $label }}
                        </label>
                    @endif

                    @if ($helpPopover)
                        <div x-data="{ open: false }" class="relative">
                            <button type="button"
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                @mouseenter="open = true" @mouseleave="open = false" @focus="open = true"
                                @blur="open = false" @click="open = !open">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7-3a1 1 0 10-2 0 1 1 0 002 0zm-2 3a1 1 0 000 2v2a1 1 0 102 0v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition @mouseenter="open = true" @mouseleave="open = false"
                                x-cloak
                                class="absolute left-0 top-full z-30 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-4 shadow-lg">
                                @if ($helpPopoverTitle)
                                    <div class="mb-1 text-sm font-semibold text-slate-800">
                                        {{ $helpPopoverTitle }}
                                    </div>
                                @endif

                                <div class="text-sm text-slate-600">
                                    {{ $helpPopover }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if ($resolvedError)
                <p class="mt-1 text-sm text-red-600">
                    {{ $resolvedError }}
                </p>
            @elseif ($help)
                <p class="mt-1 text-sm text-slate-500">
                    {{ $help }}
                </p>
            @endif
        </div>
    </div>
</div>

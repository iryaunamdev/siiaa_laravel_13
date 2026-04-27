{{--
|--------------------------------------------------------------------------
| UI Radio - SIIAA
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
| <x-ui.radio
|     name="status"
|     value="active"
|     label="Activo"
| />
|
|--------------------------------------------------------------------------
| Checked manual
|--------------------------------------------------------------------------
| <x-ui.radio
|     name="status"
|     value="active"
|     label="Activo"
|     :checked="$user->status === 'active'"
| />
|
|--------------------------------------------------------------------------
| Livewire
|--------------------------------------------------------------------------
| <x-ui.radio
|     wire:model="status"
|     name="status"
|     value="active"
|     label="Activo"
| />
|--------------------------------------------------------------------------
--}}

@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'checked' => null,
    'help' => null,
    'helpPopover' => null,
    'helpPopoverTitle' => null,
    'error' => null,
    'disabled' => false,
])

@php
    $inputId = $attributes->get('id') ?? ($name ? str_replace(['[', ']', '.'], '_', $name) : 'radio') . '_' . uniqid();
    $popoverId = 'popover_' . str_replace(['.', '[', ']'], '_', $inputId);

    $resolvedError = $error;
    if (!$resolvedError && $name) {
        $resolvedError = $errors->first($name);
    }

    $isChecked = $checked;

    if (is_null($isChecked) && $name) {
        $oldValue = old($name);
        $isChecked = !is_null($oldValue) ? (string) $oldValue === (string) $value : false;
    }

    $baseClasses = 'h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500/30 transition';
    $stateClasses = $resolvedError ? 'border-red-300 focus:ring-red-500/30' : '';

    $disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';

    $classes = implode(' ', array_filter([$baseClasses, $stateClasses, $disabledClasses]));
@endphp

<div class="w-full">
    <div class="flex items-start gap-3">
        <div class="pt-0.5">
            <input id="{{ $inputId }}" type="radio"
                @if ($name) name="{{ $name }}" @endif
                @if (!is_null($value)) value="{{ $value }}" @endif @checked($isChecked)
                @disabled($disabled) {{ $attributes->merge(['class' => $classes])->except(['id']) }} />
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
                                @blur="open = false" @click="open = !open" :aria-expanded="open.toString()"
                                aria-controls="{{ $popoverId }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7-3a1 1 0 10-2 0 1 1 0 002 0zm-2 3a1 1 0 000 2v2a1 1 0 102 0v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div id="{{ $popoverId }}" x-show="open"
                                x-transition:enter="transition ease-out duration-150"
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

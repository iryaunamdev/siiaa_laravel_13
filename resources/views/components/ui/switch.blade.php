{{--
|--------------------------------------------------------------------------
| Componente: ui.switch
|--------------------------------------------------------------------------
| Descripción:
| Componente reutilizable tipo interruptor para SIIAA.
| Se recomienda para campos booleanos o estados activado/desactivado.
|
| Casos de uso:
| - Usuario activo / inactivo
| - Visible / oculto
| - Requiere autorización
| - Habilitar notificaciones
| - Permitir acceso
| - Activar configuración
|
|--------------------------------------------------------------------------
| Props principales:
|--------------------------------------------------------------------------
| name          string      Nombre del campo
| id            string|null ID HTML opcional
| label         string|null Etiqueta visible
| help          string|null Texto de ayuda inferior
| helpPopover   string|null Ayuda extendida tipo popover
| error         string|null Error manual
| disabled      bool        Desactiva el componente
| required      bool        Marca visualmente como requerido
| checked       bool        Estado inicial
| value         string      Valor enviado cuando está activo
| size          sm|md|lg    Tamaño visual
|
|--------------------------------------------------------------------------
| Ejemplos de uso:
|--------------------------------------------------------------------------
|
| 1. Uso básico:
|
| <x-ui.switch
|     name="is_active"
|     label="Usuario activo"
| />
|
| 2. Con Livewire:
|
| <x-ui.switch
|     name="is_active"
|     label="Usuario activo"
|     wire:model.live="isActive"
| />
|
| 3. Con ayuda:
|
| <x-ui.switch
|     name="visible"
|     label="Visible en el sistema"
|     help="Si está desactivado, el registro no aparecerá para los usuarios."
| />
|
| 4. Con ayuda extendida:
|
| <x-ui.switch
|     name="requires_authorization"
|     label="Requiere autorización"
|     help-popover="Active esta opción cuando el trámite deba ser revisado por una instancia institucional antes de continuar."
| />
|
| 5. Deshabilitado:
|
| <x-ui.switch
|     name="locked"
|     label="Registro bloqueado"
|     disabled
|     checked
| />
|--------------------------------------------------------------------------
--}}

@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'help' => null,
    'helpPopover' => null,
    'error' => null,
    'disabled' => false,
    'required' => false,
    'checked' => false,
    'value' => '1',
    'size' => 'md',
])

@php
    use Illuminate\Support\Str;

    $fieldId = $id ?: $name ?: 'switch-' . Str::uuid();
    $fieldName = $name ?: $fieldId;

    $hasError = filled($error) || ($name && $errors->has($name));
    $errorMessage = $error ?: ($name ? $errors->first($name) : null);

    $oldValue = old($fieldName);

    if (!is_null($oldValue)) {
        $isChecked = filter_var($oldValue, FILTER_VALIDATE_BOOLEAN);
    } else {
        $isChecked = (bool) $checked;
    }

    $sizes = match ($size) {
        'sm' => [
            'track' => 'h-5 w-9',
            'thumb' => 'h-4 w-4',
            'translate' => 'translate-x-4',
            'text' => 'text-sm',
        ],
        'lg' => [
            'track' => 'h-7 w-12',
            'thumb' => 'h-6 w-6',
            'translate' => 'translate-x-5',
            'text' => 'text-base',
        ],
        default => [
            'track' => 'h-6 w-11',
            'thumb' => 'h-5 w-5',
            'translate' => 'translate-x-5',
            'text' => 'text-sm',
        ],
    };

    $trackBase =
        'relative inline-flex shrink-0 items-center rounded-full border transition duration-200 ease-out focus-within:outline-none';
    $trackState = $hasError ? 'border-red-300 ring-red-100' : 'border-slate-300 ring-sky-100';

    $disabledClasses = $disabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer';
@endphp

<div {{ $attributes->except(['class', 'checked', 'value'])->class(['w-full']) }}>
    <div x-data="{ checked: @js($isChecked), disabled: @js($disabled) }" class="flex items-start gap-3">
        <input type="hidden" name="{{ $fieldName }}" value="0" @disabled($disabled)>

        <button type="button" id="{{ $fieldId }}" role="switch" :aria-checked="checked.toString()"
            @click="if (!disabled) checked = !checked"
            class="{{ $trackBase }} {{ $trackState }} {{ $disabledClasses }} {{ $sizes['track'] }}"
            :class="checked
                ?
                'bg-sky-600 border-sky-600 shadow-sm hover:bg-sky-700' :
                'bg-slate-200 hover:bg-slate-300'"
            @disabled($disabled)>
            <span
                class="inline-block rounded-full bg-white shadow-sm ring-1 ring-slate-200 transition duration-200 ease-out {{ $sizes['thumb'] }}"
                :class="checked ? '{{ $sizes['translate'] }}' : 'translate-x-0.5'"></span>
        </button>

        <input type="checkbox" name="{{ $fieldName }}" value="{{ $value }}" class="sr-only" x-model="checked"
            @checked($isChecked) @disabled($disabled) {{ $attributes->whereStartsWith('wire:model') }}>

        <div class="min-w-0 flex-1">
            @if ($label)
                <div class="flex items-center gap-2">
                    <label for="{{ $fieldId }}"
                        class="inline-flex items-center gap-1 font-medium text-slate-700 {{ $sizes['text'] }}">
                        <span>{{ $label }}</span>

                        @if ($required)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>

                    @if ($helpPopover)
                        <div class="relative inline-flex" x-data="{ helpOpen: false }">
                            <button type="button"
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-slate-300 bg-white text-[11px] text-slate-500 shadow-sm transition duration-200 ease-out hover:border-sky-400 hover:text-sky-700 hover:shadow focus:outline-none focus:ring-4 focus:ring-sky-100"
                                @mouseenter="helpOpen = true" @mouseleave="helpOpen = false" @focus="helpOpen = true"
                                @blur="helpOpen = false" aria-label="Mostrar ayuda">
                                ?
                            </button>

                            <div x-cloak x-show="helpOpen" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute left-0 top-full z-30 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-3 text-sm leading-relaxed text-slate-600 shadow-lg">
                                {{ $helpPopover }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if ($errorMessage)
                <p class="mt-1 text-sm text-red-600">
                    {{ $errorMessage }}
                </p>
            @elseif($help)
                <p class="mt-1 text-sm text-slate-500">
                    {{ $help }}
                </p>
            @endif
        </div>
    </div>
</div>

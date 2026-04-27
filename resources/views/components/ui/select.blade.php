{{--
|--------------------------------------------------------------------------
| Componente: ui.select
|--------------------------------------------------------------------------
| Descripción:
| Componente reutilizable para campos tipo <select> en SIIAA.
| Soporta selección simple y múltiple, con opciones provenientes de:
| - Array
| - Collection
| - Eloquent Collection
|
| No procesa JSON directamente. Si se requiere usar JSON, debe convertirse
| previamente a array o Collection desde PHP/Livewire.
|
|--------------------------------------------------------------------------
| Defaults para catálogos SIIAA:
|--------------------------------------------------------------------------
| Estructura esperada habitual:
|
| [
|     'catalogo_id' => 1,
|     'id' => 15,
|     'clave' => 'ABC',
|     'nombre' => 'Nombre visible',
| ]
|
| Por defecto:
| - optionValue: id
| - optionLabel: nombre
| - optionDescription: null
|
|--------------------------------------------------------------------------
| Ejemplos de uso:
|--------------------------------------------------------------------------
|
| <x-ui.select
|     name="tipo_solicitud_id"
|     label="Tipo de solicitud"
|     :options="$tiposSolicitud"
| />
|
| <x-ui.select
|     name="tipo_usuario"
|     label="Tipo de usuario"
|     :options="$tiposUsuario"
|     option-value="clave"
| />
|
| <x-ui.select
|     name="programa_id"
|     label="Programa"
|     :options="$programas"
|     option-value="id"
|     option-label="nombre"
|     option-description="clave"
|     help="Seleccione el programa correspondiente."
|     help-popover="Si no encuentra el programa, verifique que esté activo en el catálogo."
| />
|
| <x-ui.select
|     name="roles"
|     label="Roles"
|     :options="$roles"
|     option-value="id"
|     option-label="name"
|     multiple
| />
|--------------------------------------------------------------------------
--}}

@props([
    'id' => null,
    'name' => null,
    'label' => null,

    'placeholder' => 'Seleccione una opción',
    'help' => null,
    'helpPopover' => null,
    'error' => null,

    'disabled' => false,
    'required' => false,
    'multiple' => false,

    'options' => [],

    // Defaults pensados para catálogos SIIAA
    'optionValue' => 'id',
    'optionLabel' => 'nombre',
    'optionDescription' => null,

    'emptyText' => 'No hay opciones disponibles',
    'size' => 'md',
])

@php
    use Illuminate\Support\Collection;
    use Illuminate\Support\Str;

    $fieldId = $id ?: $name ?: 'select-' . Str::uuid();
    $fieldName = $name ?: $fieldId;

    /*
    |--------------------------------------------------------------------------
    | Valor enlazado
    |--------------------------------------------------------------------------
    | Para formularios Blade se toma old($name). Para Livewire, normalmente
    | wire:model controlará el valor; aun así, se permite value como respaldo.
    */
    $boundValue = old($fieldName, $attributes->get('value'));

    if ($multiple) {
        if (is_null($boundValue)) {
            $boundValue = [];
        } elseif (!is_array($boundValue)) {
            $boundValue = (array) $boundValue;
        }

        if (!str_ends_with($fieldName, '[]')) {
            $fieldName .= '[]';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Error
    |--------------------------------------------------------------------------
    | Puede recibirse manualmente por prop error o desde la bolsa de errores
    | estándar de Laravel usando el name del campo.
    */
    $hasError = filled($error) || ($name && $errors->has($name));
    $errorMessage = $error ?: ($name ? $errors->first($name) : null);

    /*
    |--------------------------------------------------------------------------
    | Tamaños
    |--------------------------------------------------------------------------
    */
    $sizeClasses = match ($size) {
        'sm' => 'min-h-[2.25rem] px-3 py-2 text-sm rounded-lg',
        'lg' => 'min-h-[3rem] px-4 py-3 text-base rounded-xl',
        default => 'min-h-[2.625rem] px-3.5 py-2.5 text-sm rounded-xl',
    };

    /*
    |--------------------------------------------------------------------------
    | Clases visuales
    |--------------------------------------------------------------------------
    | Estética SIIAA: interacción sutil, elegante, viva y no recargada.
    */
    $baseClasses =
        'w-full appearance-none border bg-white text-slate-800 shadow-sm outline-none transition duration-200 ease-out';

    $stateClasses = $hasError
        ? 'border-red-300 focus:border-red-500 focus:ring-4 focus:ring-red-100'
        : 'border-slate-300 hover:border-slate-400 focus:border-sky-600 focus:ring-4 focus:ring-sky-100';

    $disabledClasses = $disabled
        ? 'cursor-not-allowed bg-slate-100 text-slate-400 border-slate-200 opacity-80'
        : 'hover:bg-slate-50/60';

    $selectClasses = implode(' ', [
        $baseClasses,
        $sizeClasses,
        $stateClasses,
        $disabledClasses,
        $multiple ? 'pr-3' : 'pr-10',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Normalización de opciones
    |--------------------------------------------------------------------------
    | Entrada aceptada:
    | - array
    | - Collection
    | - Eloquent Collection
    |
    | Estructura interna:
    | [
    |     'value' => '',
    |     'label' => '',
    |     'description' => null,
    |     'disabled' => false,
    | ]
    */
    $normalizedOptions = [];

    if ($options instanceof Collection) {
        $options = $options->all();
    }

    if (!is_iterable($options)) {
        $options = [];
    }

    foreach ($options as $key => $option) {
        if (is_scalar($option)) {
            $normalizedOptions[] = [
                'value' => (string) $key,
                'label' => (string) $option,
                'description' => null,
                'disabled' => false,
            ];

            continue;
        }

        $value = data_get($option, $optionValue);
        $labelText = data_get($option, $optionLabel);
        $description = $optionDescription ? data_get($option, $optionDescription) : null;

        // Compatibilidad con arrays ya normalizados:
        $value ??= data_get($option, 'value', $key);
        $labelText ??= data_get($option, 'label', $value);

        $normalizedOptions[] = [
            'value' => is_scalar($value) ? (string) $value : '',
            'label' => is_scalar($labelText) ? (string) $labelText : '',
            'description' => is_scalar($description) ? (string) $description : null,
            'disabled' => (bool) data_get($option, 'disabled', false),
        ];
    }

    $isSelected = function ($optionValue) use ($boundValue, $multiple) {
        if ($multiple) {
            return in_array((string) $optionValue, array_map('strval', $boundValue), true);
        }

        return (string) $boundValue === (string) $optionValue;
    };
@endphp

<div {{ $attributes->except(['value', 'class'])->class(['w-full']) }}>
    @if ($label)
        <div class="mb-1.5 flex items-center gap-2">
            <label for="{{ $fieldId }}" class="inline-flex items-center gap-1 text-sm font-medium text-slate-700">
                <span>{{ $label }}</span>

                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            @if ($helpPopover)
                <div class="relative inline-flex" x-data="{ open: false }">
                    <button type="button"
                        class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-slate-300 bg-white text-[11px] text-slate-500 shadow-sm transition duration-200 ease-out hover:border-sky-400 hover:text-sky-700 hover:shadow focus:outline-none focus:ring-4 focus:ring-sky-100"
                        @mouseenter="open = true" @mouseleave="open = false" @focus="open = true" @blur="open = false"
                        aria-label="Mostrar ayuda">
                        ?
                    </button>

                    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute left-0 top-full z-20 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-3 text-sm leading-relaxed text-slate-600 shadow-lg">
                        {{ $helpPopover }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="relative">
        <select id="{{ $fieldId }}" name="{{ $fieldName }}" @disabled($disabled) @required($required)
            @if ($multiple) multiple @endif {{ $attributes->merge(['class' => $selectClasses]) }}>
            @if (!$multiple)
                <option value="">{{ $placeholder }}</option>
            @endif

            @forelse($normalizedOptions as $option)
                <option value="{{ $option['value'] }}" @selected($isSelected($option['value'])) @disabled($option['disabled'])>
                    {{ $option['label'] }}@if ($option['description'])
                        — {{ $option['description'] }}
                    @endif
                </option>
                @empty
                    <option value="" disabled>{{ $emptyText }}</option>
                @endforelse
            </select>

            @if (!$multiple)
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-4 w-4 text-slate-400 transition duration-200 ease-out" viewBox="0 0 20 20"
                        fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            @endif
        </div>

        @if ($errorMessage)
            <p class="mt-1.5 text-sm text-red-600">
                {{ $errorMessage }}
            </p>
        @elseif($help)
            <p class="mt-1.5 text-sm text-slate-500">
                {{ $help }}
            </p>
        @endif
    </div>

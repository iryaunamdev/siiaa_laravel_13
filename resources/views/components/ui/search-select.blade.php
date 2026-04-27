{{--
|--------------------------------------------------------------------------
| Componente: ui.search-select
|--------------------------------------------------------------------------
| Descripción:
| Selector avanzado con búsqueda local para SIIAA.
| Pensado para catálogos medianos/largos: investigadores, universidades,
| programas, clases, tipos de solicitud, áreas, etc.
|
| Soporta:
| - Selección simple
| - Selección múltiple
| - Búsqueda local con Alpine
| - Opciones desde array, Collection o Eloquent Collection
| - Catálogos SIIAA con id, clave, nombre
| - help
| - helpPopover
| - error
| - disabled
| - required
| - clearable
|
|--------------------------------------------------------------------------
| Nota sobre búsqueda local:
|--------------------------------------------------------------------------
| Este componente filtra en el navegador las opciones ya cargadas desde PHP
| o Livewire. No realiza consultas AJAX/remotas.
|
| Para catálogos muy grandes, se podrá implementar posteriormente una variante
| remote-search-select o un modo remote con consultas Livewire/AJAX, debounce,
| loading state y paginación.
|
|--------------------------------------------------------------------------
| Selects enlazados:
|--------------------------------------------------------------------------
| Para casos como Estado -> Municipio -> Colonia, la dependencia debe manejarse
| desde Livewire. Este componente solo recibe nuevas opciones mediante :options.
|
| Ejemplo:
|
| <x-ui.search-select
|     name="estado_id"
|     label="Estado"
|     wire:model.live="estadoId"
|     :options="$estados"
| />
|
| <x-ui.search-select
|     name="municipio_id"
|     label="Municipio"
|     wire:model.live="municipioId"
|     :options="$municipios"
|     :disabled="!$estadoId"
| />
|
|--------------------------------------------------------------------------
| Defaults para catálogos SIIAA:
|--------------------------------------------------------------------------
| Estructura habitual:
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
| 1. Select buscable simple:
|
| <x-ui.search-select
|     name="investigador_id"
|     label="Investigador"
|     :options="$investigadores"
| />
|
| 2. Select buscable múltiple:
|
| <x-ui.search-select
|     name="roles"
|     label="Roles"
|     :options="$roles"
|     option-value="id"
|     option-label="name"
|     multiple
| />
|
| 3. Usar clave como value:
|
| <x-ui.search-select
|     name="tipo_usuario"
|     label="Tipo de usuario"
|     :options="$tiposUsuario"
|     option-value="clave"
|     option-label="nombre"
| />
|
| 4. Con descripción:
|
| <x-ui.search-select
|     name="programa_id"
|     label="Programa académico"
|     :options="$programas"
|     option-value="id"
|     option-label="nombre"
|     option-description="clave"
| />
|--------------------------------------------------------------------------
--}}

@props([
    'id' => null,
    'name' => null,
    'label' => null,

    'placeholder' => 'Seleccione una opción',
    'searchPlaceholder' => 'Buscar...',
    'help' => null,
    'helpPopover' => null,
    'error' => null,

    'disabled' => false,
    'required' => false,
    'multiple' => false,
    'clearable' => true,

    'options' => [],

    // Defaults para catálogos SIIAA
    'optionValue' => 'id',
    'optionLabel' => 'nombre',
    'optionDescription' => null,

    'emptyText' => 'No hay opciones disponibles',
    'noResultsText' => 'Sin resultados',
    'selectedTextMode' => 'chips', // chips | summary
    'size' => 'md',
])

@php
    use Illuminate\Support\Collection;
    use Illuminate\Support\Str;

    $fieldId = $id ?: $name ?: 'search-select-' . Str::uuid();
    $fieldName = $name ?: $fieldId;

    $wireModel = $attributes->wire('model')->value();

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

    $hasError = filled($error) || ($name && $errors->has($name));
    $errorMessage = $error ?: ($name ? $errors->first($name) : null);

    $sizeClasses = match ($size) {
        'sm' => 'min-h-[2.25rem] px-3 py-2 text-sm rounded-lg',
        'lg' => 'min-h-[3rem] px-4 py-3 text-base rounded-xl',
        default => 'min-h-[2.625rem] px-3.5 py-2.5 text-sm rounded-xl',
    };

    $triggerStateClasses = $hasError
        ? 'border-red-300 focus-within:border-red-500 focus-within:ring-4 focus-within:ring-red-100'
        : 'border-slate-300 hover:border-slate-400 focus-within:border-sky-600 focus-within:ring-4 focus-within:ring-sky-100';

    $disabledClasses = $disabled
        ? 'cursor-not-allowed bg-slate-100 text-slate-400 border-slate-200 opacity-80'
        : 'bg-white hover:bg-slate-50/60 cursor-pointer';

    /*
    |--------------------------------------------------------------------------
    | Normalización de opciones
    |--------------------------------------------------------------------------
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

        $value ??= data_get($option, 'value', $key);
        $labelText ??= data_get($option, 'label', $value);

        $normalizedOptions[] = [
            'value' => is_scalar($value) ? (string) $value : '',
            'label' => is_scalar($labelText) ? (string) $labelText : '',
            'description' => is_scalar($description) ? (string) $description : null,
            'disabled' => (bool) data_get($option, 'disabled', false),
        ];
    }

    $initialValue = $multiple
        ? array_values(array_map('strval', $boundValue))
        : (is_null($boundValue)
            ? ''
            : (string) $boundValue);
@endphp

<div {{ $attributes->except(['value', 'class', 'wire:model', 'wire:model.live', 'wire:model.defer', 'wire:model.blur'])->class(['w-full']) }}
    x-data="{
        open: false,
        search: '',
        multiple: @js($multiple),
        disabled: @js($disabled),
        clearable: @js($clearable),
        selectedTextMode: @js($selectedTextMode),
        options: @js($normalizedOptions),
        selected: @js($initialValue),

        init() {
            if (this.multiple && !Array.isArray(this.selected)) {
                this.selected = [];
            }

            if (!this.multiple && Array.isArray(this.selected)) {
                this.selected = this.selected[0] ?? '';
            }
        },

        get filteredOptions() {
            const term = this.search.toLowerCase().trim();

            if (!term) {
                return this.options;
            }

            return this.options.filter(option => {
                const label = String(option.label ?? '').toLowerCase();
                const description = String(option.description ?? '').toLowerCase();
                const value = String(option.value ?? '').toLowerCase();

                return label.includes(term) ||
                    description.includes(term) ||
                    value.includes(term);
            });
        },

        get selectedOptions() {
            if (this.multiple) {
                return this.options.filter(option => this.selected.includes(String(option.value)));
            }

            return this.options.filter(option => String(option.value) === String(this.selected));
        },

        get selectedLabel() {
            const option = this.selectedOptions[0];

            return option ? option.label : '';
        },

        get hasSelection() {
            return this.multiple ?
                this.selected.length > 0 :
                this.selected !== null && this.selected !== '';
        },

        isSelected(value) {
            value = String(value);

            if (this.multiple) {
                return this.selected.includes(value);
            }

            return String(this.selected) === value;
        },

        selectOption(option) {
            if (this.disabled || option.disabled) {
                return;
            }

            const value = String(option.value);

            if (this.multiple) {
                if (this.selected.includes(value)) {
                    this.selected = this.selected.filter(item => item !== value);
                } else {
                    this.selected.push(value);
                }

                this.syncLivewire();
                return;
            }

            this.selected = value;
            this.open = false;
            this.search = '';
            this.syncLivewire();
        },

        removeOption(value) {
            if (this.disabled) {
                return;
            }

            value = String(value);

            if (this.multiple) {
                this.selected = this.selected.filter(item => item !== value);
            } else {
                this.selected = '';
            }

            this.syncLivewire();
        },

        clearSelection() {
            if (this.disabled || !this.clearable) {
                return;
            }

            this.selected = this.multiple ? [] : '';
            this.search = '';
            this.syncLivewire();
        },

        toggle() {
            if (this.disabled) {
                return;
            }

            this.open = !this.open;

            if (this.open) {
                this.$nextTick(() => this.$refs.searchInput?.focus());
            }
        },

        close() {
            this.open = false;
            this.search = '';
        },

        syncLivewire() {
            @if($wireModel)
            this.$wire.set(@js($wireModel), this.selected);
            @endif
        }
    }" x-on:keydown.escape.window="close()">
    @if ($label)
        <div class="mb-1.5 flex items-center gap-2">
            <label for="{{ $fieldId }}" class="inline-flex items-center gap-1 text-sm font-medium text-slate-700">
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

    <div class="relative" x-on:click.outside="close()">
        {{-- Valores reales enviados al formulario --}}
        <template x-if="multiple">
            <template x-for="value in selected" :key="value">
                <input type="hidden" name="{{ $fieldName }}" :value="value">
            </template>
        </template>

        <template x-if="!multiple">
            <input type="hidden" name="{{ $fieldName }}" :value="selected">
        </template>

        {{-- Trigger --}}
        <button id="{{ $fieldId }}" type="button"
            class="flex w-full items-center justify-between gap-2 border shadow-sm outline-none transition duration-200 ease-out {{ $sizeClasses }} {{ $triggerStateClasses }} {{ $disabledClasses }}"
            x-on:click="toggle()" @disabled($disabled)>
            <div class="flex min-w-0 flex-1 flex-wrap items-center gap-1.5 text-left">
                <template x-if="!hasSelection">
                    <span class="truncate text-slate-400">
                        {{ $placeholder }}
                    </span>
                </template>

                <template x-if="hasSelection && !multiple">
                    <span class="truncate text-slate-800" x-text="selectedLabel"></span>
                </template>

                <template x-if="hasSelection && multiple && selectedTextMode === 'summary'">
                    <span class="truncate text-slate-700">
                        <span x-text="selectedOptions.length"></span> seleccionados
                    </span>
                </template>

                <template x-if="hasSelection && multiple && selectedTextMode === 'chips'">
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="option in selectedOptions" :key="option.value">
                            <span
                                class="inline-flex items-center gap-1 rounded-full border border-sky-100 bg-sky-50 px-2 py-0.5 text-xs font-medium text-sky-800">
                                <span x-text="option.label"></span>

                                <span role="button" tabindex="0"
                                    class="rounded-full px-1 text-sky-600 transition hover:bg-sky-100 hover:text-sky-900"
                                    x-on:click.stop="removeOption(option.value)">
                                    ×
                                </span>
                            </span>
                        </template>
                    </div>
                </template>
            </div>

            <div class="flex shrink-0 items-center gap-1">
                <template x-if="clearable && hasSelection && !disabled">
                    <span role="button" tabindex="0"
                        class="rounded-full p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        x-on:click.stop="clearSelection()">
                        ×
                    </span>
                </template>

                <svg class="h-4 w-4 text-slate-400 transition duration-200 ease-out" :class="{ 'rotate-180': open }"
                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        </button>

        {{-- Dropdown --}}
        <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]"
            class="absolute z-40 mt-2 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
            <div class="border-b border-slate-100 p-2">
                <input x-ref="searchInput" type="text" x-model="search"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none transition duration-200 ease-out placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-100"
                    placeholder="{{ $searchPlaceholder }}">
            </div>

            <div class="max-h-64 overflow-y-auto p-1">
                <template x-if="options.length === 0">
                    <div class="px-3 py-2 text-sm text-slate-500">
                        {{ $emptyText }}
                    </div>
                </template>

                <template x-if="options.length > 0 && filteredOptions.length === 0">
                    <div class="px-3 py-2 text-sm text-slate-500">
                        {{ $noResultsText }}
                    </div>
                </template>

                <template x-for="option in filteredOptions" :key="option.value">
                    <button type="button"
                        class="flex w-full items-start justify-between gap-3 rounded-lg px-3 py-2 text-left text-sm transition duration-150 ease-out"
                        :class="{
                            'cursor-not-allowed opacity-50': option.disabled,
                            'bg-sky-50 text-sky-900': isSelected(option.value),
                            'text-slate-700 hover:bg-slate-50 hover:text-slate-950': !isSelected(option.value) && !
                                option.disabled
                        }"
                        x-on:click="selectOption(option)">
                        <span class="min-w-0">
                            <span class="block truncate font-medium" x-text="option.label"></span>

                            <template x-if="option.description">
                                <span class="block truncate text-xs text-slate-500" x-text="option.description"></span>
                            </template>
                        </span>

                        <template x-if="isSelected(option.value)">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-sky-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.704 5.29a1 1 0 010 1.42l-7.5 7.5a1 1 0 01-1.42 0l-3.5-3.5a1 1 0 011.42-1.42l2.79 2.79 6.79-6.79a1 1 0 011.42 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </template>
                    </button>
                </template>
            </div>
        </div>
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

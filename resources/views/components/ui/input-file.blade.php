@props([
    'label' => null,
    'name' => null,
    'id' => null,

    'help' => null,
    'helpText' => null,
    'error' => null,

    'required' => false,
    'disabled' => false,
    'compact' => false,

    'accept' => null,
    'multiple' => false,

    // Modo drag & drop
    'dragDrop' => false,
    'dragText' => 'Arrastra archivos aquí o haz clic para seleccionar',
    'dragHint' => null,
])

@php
    /*
    |--------------------------------------------------------------------------
    | x-ui.input-file
    |--------------------------------------------------------------------------
    | Input de archivos con dos modos:
    |
    | 1. Estándar:
    | <x-ui.input-file name="archivo" label="Archivo" />
    |
    | 2. Drag & Drop:
    | <x-ui.input-file
    |     name="archivo"
    |     label="Archivo"
    |     drag-drop
    | />
    |
    | Compatible con Livewire:
    | wire:model="archivo"
    |
    | Soporta:
    | - accept (formatos)
    | - multiple
    | - estado de carga Livewire cuando existe wire:model
    |
    | Nota:
    | No usa librerías externas, solo Alpine.
    */

    $id = $id ?? $name;
    $fieldError = $error ?? ($name ? $errors->first($name) : null);
    $wireModel = $attributes->wire('model')->value();

    $baseClasses =
        'block w-full rounded-xl border bg-white text-zinc-800 shadow-sm transition focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-zinc-100';

    $stateClasses = $fieldError
        ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20'
        : 'border-zinc-300 focus:border-sky-500 focus:ring-sky-500/20';
@endphp

<div x-data="{
    isDragging: false,
    fileNames: [],

    handleDrop(e) {
        this.isDragging = false;

        if (e.dataTransfer.files.length) {
            this.$refs.input.files = e.dataTransfer.files;
            this.fileNames = Array.from(e.dataTransfer.files).map(file => file.name);

            this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    },

    handleChange(e) {
        if (e.target.files.length) {
            this.fileNames = Array.from(e.target.files).map(file => file.name);
        }
    }
}" class="space-y-1.5">
    @if ($label)
        <div class="flex items-center gap-2">
            <label class="block text-sm font-medium text-zinc-700">
                {{ $label }}

                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            @if ($helpText)
                <x-ui.help :text="$helpText" position="bottom" />
            @endif
        </div>
    @endif

    {{-- MODO DRAG & DROP --}}
    @if ($dragDrop)
        <div x-on:dragover.prevent="isDragging = true" x-on:dragleave="isDragging = false"
            x-on:drop.prevent="handleDrop($event)" x-on:click="$refs.input.click()"
            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed p-6 text-center transition"
            :class="isDragging ? 'border-sky-500 bg-sky-50' : 'border-zinc-300 bg-white'">
            {{-- Icono --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-zinc-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16V8m0 0l-3 3m3-3l3 3m7 5a4 4 0 00-4-4H7m10 4a4 4 0 01-4 4H7" />
            </svg>

            <div class="text-sm text-zinc-600">
                {{ $dragText }}
            </div>

            @if ($dragHint)
                <div class="text-xs text-zinc-400">
                    {{ $dragHint }}
                </div>
            @endif

            <template x-if="fileNames.length">
                <div class="mt-2 text-left text-xs text-zinc-500">
                    <p class="mb-1 font-medium text-zinc-600">
                        Archivo(s) seleccionado(s):
                    </p>

                    <ul class="space-y-1">
                        <template x-for="fileName in fileNames" :key="fileName">
                            <li class="flex items-center gap-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                <span x-text="fileName"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </template>
        </div>

        <input x-ref="input" id="{{ $id }}" name="{{ $name }}" type="file" class="hidden"
            @if ($accept) accept="{{ $accept }}" @endif
            @if ($multiple) multiple @endif @required($required) @disabled($disabled)
            x-on:change="handleChange($event)" {{ $attributes }}>
    @else
        {{-- MODO NORMAL --}}
        <input id="{{ $id }}" name="{{ $name }}" type="file"
            @if ($accept) accept="{{ $accept }}" @endif
            @if ($multiple) multiple @endif @required($required) @disabled($disabled)
            class="{{ $baseClasses }} {{ $stateClasses }}" {{ $attributes }}>
    @endif

    {{-- Estado de carga Livewire --}}
    @if ($wireModel)
        <div wire:loading wire:target="{{ $wireModel }}" class="text-xs text-sky-600">
            Subiendo archivo...
        </div>
    @endif

    @if ($help)
        <p class="text-xs text-zinc-500">{{ $help }}</p>
    @endif

    @if ($fieldError)
        <p class="text-xs text-red-600">{{ $fieldError }}</p>
    @endif
</div>

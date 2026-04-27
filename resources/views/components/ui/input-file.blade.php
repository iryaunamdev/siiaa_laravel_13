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
    | - estado de carga Livewire
    |
    | Nota:
    | No usa librerías externas, solo Alpine.
    */

    $id = $id ?? $name;
    $fieldError = $error ?? ($name ? $errors->first($name) : null);

    $baseClasses =
        'block w-full rounded-xl border bg-white text-slate-800 shadow-sm transition focus:outline-none focus:ring-2 disabled:cursor-not-allowed disabled:bg-slate-100';

    $stateClasses = $fieldError
        ? 'border-red-300 focus:border-red-500 focus:ring-red-500/20'
        : 'border-slate-300 focus:border-sky-500 focus:ring-sky-500/20';
@endphp

<div x-data="{
    isDragging: false,
    fileName: null,

    handleDrop(e) {
        this.isDragging = false;

        if (e.dataTransfer.files.length) {
            this.$refs.input.files = e.dataTransfer.files;
            this.fileName = e.dataTransfer.files[0].name;

            this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    },

    handleChange(e) {
        if (e.target.files.length) {
            this.fileName = e.target.files[0].name;
        }
    }
}" class="space-y-1.5">
    @if ($label)
        <div class="flex items-center gap-2">
            <label class="block text-sm font-medium text-slate-700">
                {{ $label }}

                @if ($required)
                    <span class="text-red-500">*</span>
                @endif
            </label>

            @if ($helpText)
                <div class="relative inline-flex" x-data="{ open: false }">
                    <button type="button" x-on:mouseenter="open = true" x-on:mouseleave="open = false"
                        class="inline-flex h-5 w-5 items-center justify-center rounded-full border text-xs text-slate-500">?</button>

                    <div x-show="open"
                        class="absolute left-0 top-6 z-30 w-64 rounded-xl border bg-white p-3 text-xs shadow-lg">
                        {{ $helpText }}
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- MODO DRAG & DROP --}}
    @if ($dragDrop)
        <div x-on:dragover.prevent="isDragging = true" x-on:dragleave="isDragging = false"
            x-on:drop.prevent="handleDrop($event)" x-on:click="$refs.input.click()"
            class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed p-6 text-center transition"
            :class="isDragging ? 'border-sky-500 bg-sky-50' : 'border-slate-300 bg-white'">
            {{-- Icono --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16V8m0 0l-3 3m3-3l3 3m7 5a4 4 0 00-4-4H7m10 4a4 4 0 01-4 4H7" />
            </svg>

            <div class="text-sm text-slate-600">
                {{ $dragText }}
            </div>

            @if ($dragHint)
                <div class="text-xs text-slate-400">
                    {{ $dragHint }}
                </div>
            @endif

            <template x-if="fileName">
                <div class="text-xs text-slate-500 mt-2" x-text="fileName"></div>
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
    <div wire:loading wire:target="{{ $attributes->wire('model')->value() ?? '' }}" class="text-xs text-sky-600">
        Subiendo archivo...
    </div>

    @if ($help)
        <p class="text-xs text-slate-500">{{ $help }}</p>
    @endif

    @if ($fieldError)
        <p class="text-xs text-red-600">{{ $fieldError }}</p>
    @endif
</div>

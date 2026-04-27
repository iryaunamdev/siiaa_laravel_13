{{--
|--------------------------------------------------------------------------
| UI Component: Confirm Dialog
|--------------------------------------------------------------------------
|
| Propósito:
| Mostrar una confirmación antes de ejecutar acciones críticas.
|
| Uso principal:
| - Eliminar registros
| - Desactivar usuarios
| - Restablecer contraseñas
| - Revocar accesos
|
| Props:
| - model: string
|   Propiedad Livewire/Alpine que controla la apertura del modal.
|
| - title: string
| - description: string|null
| - confirmText: string
| - cancelText: string
| - confirmAction: string|null
| - variant: danger | warning | primary
|
|--------------------------------------------------------------------------
--}}

@props([
    'model' => 'confirmingAction',
    'title' => 'Confirmar acción',
    'description' => null,
    'confirmText' => 'Confirmar',
    'cancelText' => 'Cancelar',
    'confirmAction' => null,
    'variant' => 'danger',
])

@php
    $variantClasses = [
        'danger' => [
            'icon' => 'bg-red-50 text-red-600',
            'button' => 'danger',
        ],
        'warning' => [
            'icon' => 'bg-amber-50 text-amber-600',
            'button' => 'warning',
        ],
        'primary' => [
            'icon' => 'bg-blue-50 text-blue-600',
            'button' => 'primary',
        ],
    ];

    $variantClass = $variantClasses[$variant] ?? $variantClasses['danger'];
@endphp

<x-ui.modal wire:model="{{ $model }}" size="md">
    <div class="space-y-5">
        <div class="flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $variantClass['icon'] }}">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                </svg>
            </div>

            <div class="min-w-0">
                <h3 class="text-base font-semibold text-slate-900">
                    {{ $title }}
                </h3>

                @if ($description)
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $description }}
                    </p>
                @endif

                {{ $slot }}
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-4">
            <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close-modal')"
                wire:click="$set('{{ $model }}', false)">
                {{ $cancelText }}
            </x-ui.button>

            <x-ui.button type="button" variant="{{ $variantClass['button'] }}"
                @if ($confirmAction) wire:click="{{ $confirmAction }}" @endif>
                {{ $confirmText }}
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>

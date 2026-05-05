@props([
    'model' => 'confirmDeleteModal',
    'title' => 'Confirmación de eliminación',
    'name' => null,
    'message' => null,
    'warning' => null,
    'cancelAction' => 'closeDeleteModal',
    'confirmAction' => 'deleteConfirmed',
    'confirmText' => 'Eliminar',
    'cancelText' => 'Cancelar',
])

<x-ui.modal wire:model="{{ $model }}" maxWidth="md">
    <div class="space-y-4 px-4 py-5">
        <div class="flex items-start">
            <div
                class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-ui.icon name="exclamation-triangle" class="h-6 w-6 text-red-500" />
            </div>

            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                <h3 class="text-lg font-medium text-zinc-900">
                    {{ $title }}
                </h3>

                <div class="mt-4 text-sm text-zinc-500">
                    @if ($message)
                        {{ $message }}
                    @else
                        El registro
                        <strong>{{ $name }}</strong>
                        se eliminará de manera permanente.
                    @endif

                    @if ($warning)
                        <br>
                        <span class="text-zinc-500">
                            {{ $warning }}
                        </span>
                    @endif

                    <br>
                    <span class="font-medium">
                        ¿Deseas continuar?
                    </span>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <x-ui.button type="button" variant="secondary" wire:click.prevent="{{ $cancelAction }}">
                {{ $cancelText }}
            </x-ui.button>

            <x-ui.button type="button" variant="danger" wire:click.prevent="{{ $confirmAction }}"
                wire:loading.attr="disabled" wire:target="{{ $confirmAction }}">
                {{ $confirmText }}
            </x-ui.button>
        </x-slot>
    </div>
</x-ui.modal>

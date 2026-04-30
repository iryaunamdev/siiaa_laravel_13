<x-ui.modal wire:model="confirmDeleteModal" maxWidth="md">
    <div class="space-y-4 px-4 py-5">
        <div class="flex items-start">
            <div
                class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <x-ui.icon name="exclamation-triangle" class="h-6 w-6 text-red-500" />
            </div>

            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg font-medium text-zinc-900">
                    Confirmación de eliminación
                </h3>

                <div class="mt-4 text-sm text-zinc-500">
                    El registro <strong>{{ $deleteName }}</strong> se eliminará de manera permanente.
                    @if ($deleteType === 'permission')
                        También se retirará este permiso de todos los roles donde esté asignado.
                    @elseif ($deleteType === 'role')
                        Solo se puede eliminar un rol sin usuarios ni permisos asignados.
                    @endif
                    <br><span class="font-medium">¿Deseas continuar?</span>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <x-ui.button variant="secondary" wire:click="closeDeleteModal">
                Cancelar
            </x-ui.button>

            <x-ui.button variant="danger" wire:click="deleteConfirmed">
                Eliminar
            </x-ui.button>
        </x-slot>
    </div>

</x-ui.modal>

<x-ui.modal wire:model="catalogoModal" maxWidth="sm">

    <x-slot:title>
        {{ $catalogoId ? 'Editar catálogo' : 'Nuevo catálogo' }}
    </x-slot:title>

    <div class="space-y-4 p-4 grid grid-cols-2">

        <div class="col-span-1">
            <x-ui.input label="Clave" wire:model.defer="catalogoClave" placeholder="ej: estados" />
        </div>

        <div class="col-span-2">
            <x-ui.input label="Nombre" wire:model.defer="catalogoNombre" placeholder="Nombre del catálogo" />
        </div>

        <div class="col-span-2">
            <x-ui.textarea label="Descripción" wire:model.defer="catalogoDescripcion" rows="3" />
        </div>

        <div class="col-span-2">
            <x-ui.checkbox label="Activo" wire:model.defer="catalogoActivo" />
        </div>
    </div>

    <x-slot:footer>
        <div class="flex justify-end gap-2">

            <x-ui.button variant="secondary" wire:click="$set('catalogoModal', false)">
                Cancelar
            </x-ui.button>

            <x-ui.button wire:click="saveCatalogo">
                Guardar
            </x-ui.button>

        </div>
    </x-slot:footer>

</x-ui.modal>

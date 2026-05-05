<x-ui.modal wire:model="itemModal" maxWidth="sm">

    <x-slot:title>
        {{ $itemId ? 'Editar item' : 'Nuevo item' }}
    </x-slot:title>

    <div class="space-y-4 p-4 grid grid-cols-2">
        @if (!$itemId)
            <div class="col-span-full p-2 border border-zinc-200 rounded">
                <x-ui.checkbox label="Añadir múltiples items" wire:model.live="itemBulkMode" />
                <span class="text-xs text-zinc-400 italic">Activa esta opción para agregar varios items a la vez. De lo
                    contrario llena los campos siguientes para añadir items de manera individual.</span>
            </div>
        @endif

        @if ($itemBulkMode && !$itemId)
            <div class="col-span-full">
                <x-ui.textarea label="Items" wire:model.defer="itemBulkText" rows="4"
                    placeholder="CLAVE1, Nombre del item 1"
                    help="Formato: una línea por item. Cada línea debe usar CLAVE, NOMBRE." />
            </div>
        @else
            <div class="col-span-1">
                <x-ui.input label="Clave" wire:model.defer="itemClave" placeholder="ej: ACTIVO" />
            </div>
            <div class="col-span-2">
                <x-ui.input label="Nombre" wire:model.defer="itemNombre" placeholder="Nombre del item" />
            </div>
            <div class="col-span-2">
                <x-ui.textarea label="Descripción" wire:model.defer="itemDescripcion" rows="3" />
            </div>
            <div>
                <x-ui.checkbox label="Activo" wire:model.defer="itemActivo" />
            </div>
        @endif
    </div>

    <x-slot:footer>
        <div class="flex justify-end gap-2">
            <x-ui.button variant="secondary" wire:click="$set('itemModal', false)">
                Cancelar
            </x-ui.button>

            <x-ui.button wire:click="saveItem">
                Guardar
            </x-ui.button>
        </div>
    </x-slot:footer>

</x-ui.modal>

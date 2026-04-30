<x-ui.modal wire:model="permissionModal">
    <x-ui.panel :title="$editingPermissionId ? 'Editar permiso' : 'Nuevo permiso'" size="md" padding="false">
        <div class="space-y-4 px-4 py-5">
            @if ($editingPermissionId)
                <x-ui.input label="Nombre del permiso" wire:model.defer="permissionName" placeholder="users.view"
                    help="Use la nomenclatura modulo.accion." />
            @else
                <x-ui.textarea label="Crear permisos" wire:model.defer="newPermissions" rows="4"
                    placeholder="users.view, users.create, roles.update"
                    helpPopover="Ingrese uno o varios permisos siguiendo la nomenclatura <em>modulo.accion</em>. Puede capturar varios permisos separados por coma o salto de línea." />
            @endif

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <x-ui.button variant="secondary" wire:click="closePermissionModal">
                    Cancelar
                </x-ui.button>

                <x-ui.button wire:click="savePermission">
                    Guardar
                </x-ui.button>
            </div>
        </div>
    </x-ui.panel>
</x-ui.modal>

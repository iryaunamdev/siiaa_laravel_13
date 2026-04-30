<x-ui.modal wire:model="roleModal">
    <x-ui.panel :title="$editingRoleId ? 'Editar rol' : 'Nuevo rol'" size="md" padding="false">
        <div class="space-y-4 px-4 py-5">
            <x-ui.input label="Nombre del rol" wire:model.defer="roleName" placeholder="admin"
                help="Use minúsculas, números, guion medio o guion bajo." />

            <div class="space-y-2">
                <p class="text-sm font-medium text-slate-700">
                    Permisos
                </p>

                <div class="max-h-72 space-y-3 overflow-y-auto rounded-lg border border-slate-100 px-3 py-3">
                    @forelse ($permissions->sortBy('name')->groupBy(fn($permission) => str($permission->name)->before('.')->toString()) as $module => $modulePermissions)
                        <div>
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                {{ $module }}
                            </p>

                            <div class="grid grid-cols-1 gap-2">
                                @foreach ($modulePermissions as $permission)
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox"
                                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                            value="{{ $permission->name }}" wire:model.defer="selectedPermissions">

                                        <span>{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <x-ui.alert type="info">
                            No hay permisos registrados.
                        </x-ui.alert>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <x-ui.button variant="secondary" wire:click="closeRoleModal">
                    Cancelar
                </x-ui.button>

                <x-ui.button wire:click="saveRole">
                    Guardar
                </x-ui.button>
            </div>
        </div>
    </x-ui.panel>
</x-ui.modal>

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    {{-- Panel roles --}}
    <x-ui.panel title="Roles" size="full">
        <x-slot name="actions">
            <x-ui.button size="sm" wire:click="openCreateRoleModal" class="align-center">
                <x-slot:icon>
                    <x-ui.icon name="plus" class="h-4 w-4 text-white" />
                </x-slot:icon>
                Rol
            </x-ui.button>
        </x-slot>

        <div class="space-y-2 px-4 py-4">
            @forelse ($roles as $role)
                <x-ui.panel collapsible :defaultOpen="false">
                    <x-slot name="header">
                        <p class="text-sm font-semibold text-blue-800">
                            {{ $role->name }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $role->users_count }} usuario(s) ·
                            {{ $role->permissions->count() }} permiso(s)
                        </p>
                    </x-slot>
                    <x-slot name="actions">
                        <x-ui.button variant="link" size="sm" class="h-8 w-8 p-0"
                            wire:click="openEditRoleModal({{ $role->id }})" title="Editar rol">
                            <x-ui.icon name="edit" class="h-5 w-5 text-blue-600" />
                        </x-ui.button>

                        <x-ui.button variant="link" size="sm" class="h-8 w-8 p-0"
                            wire:click="confirmDelete('role', {{ $role->id }})" title="Eliminar rol">
                            <x-ui.icon name="trash" class="h-5 w-5 text-red-600" />
                        </x-ui.button>
                    </x-slot>

                    @php
                        $groupedRolePermissions = $role->permissions
                            ->sortBy('name')
                            ->groupBy(fn($permission) => str($permission->name)->before('.')->toString());
                    @endphp

                    <div class="grid grid-cols-1 gap-0">
                        @forelse ($groupedRolePermissions as $module => $modulePermissions)
                            <div class="p-3">
                                <div class="mb-2">
                                    <span class="text-sm font-semibold">
                                        {{ $module }}
                                    </span>

                                    <span class="text-xs text-slate-500 italic">
                                        ({{ $modulePermissions->count() }})
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @foreach ($modulePermissions as $permission)
                                        <x-ui.badge size="xs" variant="neutral">
                                            {{ $permission->name }}
                                        </x-ui.badge>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <x-ui.alert type="info">
                                No hay permisos registrados.
                            </x-ui.alert>
                        @endforelse
                    </div>

                </x-ui.panel>
            @empty
                <x-ui.alert type="info">
                    No hay roles registrados.
                </x-ui.alert>
            @endforelse
        </div>
    </x-ui.panel>

    {{-- Panel permisos --}}
    <x-ui.panel title="Permisos" size="full">
        <x-slot name="actions">
            <x-ui.button size="sm" wire:click="openCreatePermissionModal" class="align-center">
                <x-slot:icon>
                    <x-ui.icon name="plus" class="h-4 w-4 text-white" />
                </x-slot:icon>
                Permiso(s)
            </x-ui.button>
        </x-slot>
        <div class="space-y-1 px-4 py-4">
            @php
                $groupedPermissions = $permissions
                    ->sortBy('name')
                    ->groupBy(fn($permission) => str($permission->name)->before('.')->toString());
            @endphp

            <div class="grid grid-cols-1 gap-2 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($groupedPermissions as $module => $modulePermissions)
                    <div class="rounded-xl border border-slate-100 bg-white p-3 shadow-sm">
                        <div class=" flex justify-between mb-3 border-b border-slate-100 pb-2">
                            <p class="text-sm font-semibold text-slate-800">
                                {{ $module }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $modulePermissions->count() }} permiso(s)
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($modulePermissions as $permission)
                                <div
                                    class="inline-flex justify-between w-full items-center gap-2 rounded-lg border border-slate-100 bg-slate-50 px-2 py-1.5 text-xs">
                                    <span class="truncate text-slate-700">
                                        {{ $permission->name }}
                                    </span>

                                    <div class="flex shrink-0 items-center gap-1">
                                        <x-ui.button variant="link" size="sm" class="h-5 w-5 p-0"
                                            wire:click="openEditPermissionModal({{ $permission->id }})"
                                            title="Editar permiso">
                                            <x-ui.icon name="edit" class="h-4 w-4 text-blue-600" />
                                        </x-ui.button>

                                        <x-ui.button variant="link" size="sm" class="h-5 w-5 p-0"
                                            wire:click="confirmDelete('permission', {{ $permission->id }})"
                                            title="Eliminar permiso">
                                            <x-ui.icon name="trash" class="h-4 w-4 text-red-600" />
                                        </x-ui.button>
                                    </div>
                                </div>
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
    </x-ui.panel>

    @include('livewire.sys.roles-permissions.modal_rol-editar')
    @include('livewire.sys.roles-permissions.modal_permisos-editar')
    <x-ui.confirm-delete-modal
        model="confirmDeleteModal"
        :title="$deleteType === 'role' ? 'Eliminar rol' : 'Eliminar permiso'"
        :name="$deleteName"
        :message="$deleteType === 'role'
            ? 'El rol ' . $deleteName . ' se eliminará de manera permanente.'
            : 'El permiso ' . $deleteName . ' se eliminará de manera permanente y será retirado de los roles que lo tengan asignado.'"
        :warning="$deleteType === 'role'
            ? 'Solo se podrá eliminar si no tiene usuarios ni permisos asociados.'
            : 'Esta acción no eliminará roles; solo desvinculará y eliminará el permiso.'"
        confirm-action="deleteConfirmed"
        cancel-action="resetDeleteForm"
    />
</div>

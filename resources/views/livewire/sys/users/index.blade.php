<x-ui.panel title="Administración de usuarios" description="Listado de usuarios registrados en el sistema." size="full">
    <x-slot:actions>
        @can('users.create')
            <x-ui.button type="button" wire:click="openCreateModal" wire:loading.attr="disabled" wire:target="openCreateModal"
                variant="primary" size="sm">
                Crear usuario
            </x-ui.button>
        @endcan
    </x-slot:actions>

    <x-ui.panel class="mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div class="w-full md:w-72">
                <x-ui.input wire:model.live.debounce.400ms="search" label="Buscar"
                    placeholder="Nombre, usuario o correo" />
            </div>

            <div class="w-40">
                <x-ui.select wire:model.live="status" label="Estado" :options="[
                    'all' => 'Todos',
                    'active' => 'Activos',
                    'inactive' => 'Inactivos',
                ]" />
            </div>

            <div class="w-40">
                <x-ui.select wire:model.live="authType" label="Tipo de autenticación" :options="[
                    'all' => 'Todos',
                    'local' => 'Local',
                    'ldap' => 'LDAP',
                ]" />
            </div>
            <div class="w-40">
                <x-ui.select wire:model.live="roleFilter" label="Rol" :options="$roles->pluck('name', 'name')->prepend('Todos', 'all')->toArray()" />
            </div>
            <div class="w-20">
                <x-ui.button type="button" wire:click="resetFilters" variant="link" size="sm" class="mt-2"
                    title="Restablecer filtros">
                    <x-ui.icon name="cancel" class="h-4 w-4 text-red-500 hover:text-red-600" />
                </x-ui.button>
            </div>
        </div>

    </x-ui.panel>

    <x-ui.table>
        <x-ui.table.head>
            <x-ui.table.row>
                <x-ui.table.header>ID</x-ui.table.header>
                <x-ui.table.header>Usuario</x-ui.table.header>
                <x-ui.table.header>Nombre</x-ui.table.header>
                <x-ui.table.header>Correo</x-ui.table.header>
                <x-ui.table.header>Autenticación</x-ui.table.header>
                <x-ui.table.header>Estado</x-ui.table.header>
                <x-ui.table.header>Último acceso</x-ui.table.header>
                <x-ui.table.header align="right">Acciones</x-ui.table.header>
            </x-ui.table.row>
        </x-ui.table.head>

        <x-ui.table.body>
            @forelse ($users as $user)
                <x-ui.table.row>
                    <x-ui.table.cell>
                        {{ $user->id }}
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        <button class="flex justify-start gap-3 hover:bg-gray-100 rounded px-1.5 py-1 w-full"
                            wire:click="openEditModal({{ $user->id }})">
                            <x-ui.avatar :model="$user" size="sm" variant="neutral" />

                            <div class="text-left">
                                <div class="font-medium text-blue-600">
                                    {{ $user->username }}
                                </div>

                                <div class="text-xs text-zinc-500">
                                    {{ $user->name }}
                                </div>
                            </div>
                        </button>
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        {{ $user->name }}
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        {{ $user->email ?? '—' }}
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        @if ($user->auth_type === 'ldap')
                            <x-ui.badge variant="info" text-size="text-[0.65rem]">
                                {{ strtoupper($user->auth_type) }}
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="secondary" text-size="text-[0.65rem]">
                                {{ strtoupper($user->auth_type) }}
                            </x-ui.badge>
                        @endif
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        @if ($user->is_active)
                            <x-ui.badge variant="success" text-size="text-xs">
                                Activo
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="danger" text-size="text-xs">
                                Inactivo
                            </x-ui.badge>
                        @endif
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        {{ $user->last_login_at?->format('Y-m-d H:i') ?? '—' }}
                    </x-ui.table.cell>

                    <x-ui.table.cell align="right">
                        @can('users.delete')
                            @if ($user->id !== auth()->id())
                                <x-ui.button type="button" wire:click="confirmDelete({{ $user->id }})" variant="link"
                                    size="sm" title="Eliminar usuario" wire:loading.attr="disabled"
                                    wire:target="confirmDelete({{ $user->id }})">
                                    <x-ui.icon name="trash" class="h-4 w-4 text-red-500 hover:text-red-600" />
                                </x-ui.button>
                            @endif
                        @endcan
                    </x-ui.table.cell>
                </x-ui.table.row>
            @empty
                <x-ui.table.empty colspan="8">
                    No existen usuarios registrados.
                </x-ui.table.empty>
            @endforelse
        </x-ui.table.body>
    </x-ui.table>
    <div class="mt-4">
        {{ $users->links() }}
    </div>

    @include('livewire.sys.users._form-modal')
    <x-ui.confirm-delete-modal model="confirmDeleteModal" title="Eliminar usuario" :name="$deleteName"
        message="El usuario será eliminado de manera permanente del sistema."
        warning="Esta acción eliminará el usuario local o LDAP registrado en SIIAA. No se permite eliminar tu propio usuario."
        confirm-action="deleteConfirmed" cancel-action="resetDeleteForm" />
    <x-ui.confirm-delete-modal model="confirmResetTwoFactorModal" title="Restablecer 2FA" :name="$resetTwoFactorUserName"
        message="Se eliminará la configuración de autenticación en dos factores de este usuario."
        warning="El usuario deberá configurar nuevamente Google Authenticator o Microsoft Authenticator en su próximo acceso."
        confirm-action="resetTwoFactorConfirmed" cancel-action="resetTwoFactorForm" />
</x-ui.panel>

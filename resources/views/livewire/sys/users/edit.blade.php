<x-ui.panel title="Editar usuario" description="Actualiza la información básica y los roles asignados al usuario."
    size="xl">
    <form wire:submit.prevent="save" class="space-y-5">
        <x-ui.input label="Nombre" wire:model="name" placeholder="Nombre completo del usuario" :error="$errors->first('name')"
            required />

        <x-ui.input label="Usuario" wire:model="username" placeholder="Nombre de usuario para iniciar sesión"
            :error="$errors->first('username')" required />

        <x-ui.input type="email" label="Correo electrónico" wire:model="email" placeholder="correo@irya.unam.mx"
            :error="$errors->first('email')" />

        <x-ui.checkbox wire:model="is_active" label="Usuario activo"
            description="Permite que el usuario pueda iniciar sesión en el sistema." />

        @can('sys.users.assign-roles')
            <x-ui.panel title="Roles asignados" description="El sistema administra privilegios únicamente por roles."
                size="full" padding="sm">
                <div class="space-y-3">
                    @forelse ($availableRoles as $role)
                        <x-ui.checkbox wire:model="selectedRoles" value="{{ $role }}" label="{{ $role }}" />
                    @empty
                        <x-ui.alert variant="info">
                            No existen roles disponibles para asignar.
                        </x-ui.alert>
                    @endforelse

                    @error('selectedRoles')
                        <p class="text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('selectedRoles.*')
                        <p class="text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </x-ui.panel>
        @endcan

        <div class="flex items-center justify-end gap-3 pt-4">
            <x-ui.button href="{{ route('sys.users.index') }}" variant="secondary">
                Cancelar
            </x-ui.button>

            <x-ui.button type="submit" variant="primary">
                Guardar cambios
            </x-ui.button>
        </div>
    </form>
</x-ui.panel>

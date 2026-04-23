<div class="flex flex-col gap-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Editar usuario
        </h1>

        <p class="text-sm text-gray-600 dark:text-gray-400">
            Actualiza la información básica del usuario seleccionado.
        </p>
    </div>

    <form wire:submit.prevent="save" class="flex flex-col gap-4">
        <div>
            <label class="text-sm">Nombre</label>
            <input type="text" wire:model="name" class="w-full border rounded p-2">
            @error('name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="text-sm">Usuario</label>
            <input type="text" wire:model="username" class="w-full border rounded p-2">
            @error('username')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="text-sm">Correo</label>
            <input type="email" wire:model="email" class="w-full border rounded p-2">
            @error('email')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model="is_active">
            <label class="text-sm">Activo</label>
        </div>

        @can('sys.users.assign-roles')
            <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                <div class="mb-3">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Roles asignados
                    </h2>

                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        El sistema administra privilegios únicamente por roles.
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    @forelse ($availableRoles as $role)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}">
                            <span>{{ $role }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            No existen roles disponibles.
                        </p>
                    @endforelse
                </div>

                @error('selectedRoles')
                    <span class="mt-2 block text-xs text-red-500">{{ $message }}</span>
                @enderror

                @error('selectedRoles.*')
                    <span class="mt-2 block text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>
        @endcan

        <div class="flex gap-2">
            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded">
                Guardar cambios
            </button>

            <a href="{{ route('sys.users.index') }}" class="px-4 py-2 border rounded">
                Cancelar
            </a>
        </div>
    </form>
</div>

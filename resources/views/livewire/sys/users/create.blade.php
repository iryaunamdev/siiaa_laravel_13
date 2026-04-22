<div class="flex flex-col gap-6 max-w-2xl">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
            Crear usuario
        </h1>
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

        <div>
            <label class="text-sm">Contraseña</label>
            <input type="password" wire:model="password" class="w-full border rounded p-2">
            @error('password')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" wire:model="is_active">
            <label class="text-sm">Activo</label>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded">
                Guardar
            </button>

            <a href="{{ route('sys.users.index') }}" class="px-4 py-2 border rounded">
                Cancelar
            </a>
        </div>
    </form>
</div>

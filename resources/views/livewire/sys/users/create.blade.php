<x-ui.panel title="Crear usuario" description="Registra un nuevo usuario para el acceso al sistema." size="xl">
    <form wire:submit.prevent="save" class="space-y-5">
        <x-ui.input label="Nombre" wire:model="name" placeholder="Nombre completo del usuario" :error="$errors->first('name')"
            required />

        <x-ui.input label="Usuario" wire:model="username" placeholder="Nombre de usuario para iniciar sesión"
            :error="$errors->first('username')" required />

        <x-ui.input type="email" label="Correo electrónico" wire:model="email" placeholder="correo@irya.unam.mx"
            :error="$errors->first('email')" />

        <x-ui.input type="password" label="Contraseña" wire:model="password" placeholder="Contraseña inicial"
            :error="$errors->first('password')" required />

        <x-ui.checkbox wire:model="is_active" label="Usuario activo"
            description="Permite que el usuario pueda iniciar sesión en el sistema." />

        <div class="flex items-center justify-end gap-3 pt-4">
            <x-ui.button href="{{ route('sys.users.index') }}" variant="secondary">
                Cancelar
            </x-ui.button>

            <x-ui.button type="submit" variant="primary">
                Guardar usuario
            </x-ui.button>
        </div>
    </form>
</x-ui.panel>

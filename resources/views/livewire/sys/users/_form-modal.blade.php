<x-ui.modal wire:model="userModal" max-width="md">
    <x-slot name="title">
        {{ $editingUserId ? 'Editar usuario' : 'Nuevo usuario' }}
    </x-slot>

    <div class="space-y-5">

        {{-- DATOS --}}
        <div class="grid grid-cols-1 gap-4">
            <x-ui.input label="Usuario" wire:model.defer="username" />
            <x-ui.input label="Nombre" wire:model.defer="name" />
            <x-ui.input label="Correo" type="email" wire:model.defer="email" />
        </div>

        {{-- CONTRASEÑA --}}
        @if ($isLocalUser)
            <div class="space-y-3">

                <div class="flex items-center justify-between">
                    <label class="text-sm font-medium text-slate-700">
                        Cambiar contraseña
                    </label>

                    <input type="checkbox" wire:model.live="changePassword">
                </div>

                @if ($changePassword)
                    <div x-data="{ show: @entangle('changePassword') }" x-show="show"
                        x-transition:enter="transition ease-[cubic-bezier(0.4,0,0.2,1)] duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="grid grid-cols-1 gap-3 overflow-hidden">
                        <x-ui.input label="Contraseña" type="password" wire:model.defer="password" />

                        <x-ui.input label="Confirmar contraseña" type="password"
                            wire:model.defer="password_confirmation" />

                        @can('users.change_password')
                            <x-ui.button type="button" variant="secondary" size="sm" wire:click="generatePassword">
                                Generar contraseña
                            </x-ui.button>

                            {{-- copiar contraseña text/png --}}
                            @if ($password)
                                <div x-data="{
                                    password: @js($password),
                                    username: @js($username),
                                    name: @js($name),

                                    get message() {
                                        return `Hola ${this.name || this.username}, tu contraseña temporal de acceso al SIIAA es: ${this.password}`;
                                    },

                                    async copyPassword() {
                                        await navigator.clipboard.writeText(this.password);
                                    },

                                    async copyMessage() {
                                        await navigator.clipboard.writeText(this.message);
                                    },

                                    downloadPng() {
                                        const canvas = document.createElement('canvas');
                                        const ctx = canvas.getContext('2d');

                                        canvas.width = 900;
                                        canvas.height = 420;

                                        ctx.fillStyle = '#f8fafc';
                                        ctx.fillRect(0, 0, canvas.width, canvas.height);

                                        ctx.fillStyle = '#0f172a';
                                        ctx.font = 'bold 24px Arial';
                                        ctx.fillText('Contraseña SIIAA', 50, 80);

                                        /*ctx.fillStyle = '#334155';
                                        ctx.font = '24px Arial';
                                        #ctx.fillText(`Usuario: ${this.username || 'N/A'}`, 50, 140);*/

                                        ctx.fillStyle = '#1e293b';
                                        ctx.font = 'bold 40px Arial';
                                        ctx.fillText(this.password, 50, 230);

                                        /*ctx.fillStyle = '#64748b';
                                        ctx.font = '20px Arial';
                                        ctx.fillText('Esta contraseña .', 50, 310);*/

                                        const link = document.createElement('a');
                                        link.download = `password-siiaa-${this.username || 'usuario'}.png`;
                                        link.href = canvas.toDataURL('image/png');
                                        link.click();
                                    }
                                }"
                                    class="rounded-xl border border-slate-200 bg-slate-50 p-3 space-y-3">
                                    <div>
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                            Contraseña temporal generada
                                        </p>

                                        <p
                                            class="mt-1 rounded-lg bg-white px-3 py-2 font-mono text-sm text-slate-800 ring-1 ring-slate-200">
                                            {{ $password }}
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                        <x-ui.button type="button" variant="secondary" size="sm"
                                            x-on:click="copyPassword">
                                            Copiar password
                                        </x-ui.button>

                                        <x-ui.button type="button" variant="secondary" size="sm"
                                            x-on:click="copyMessage">
                                            Copiar mensaje
                                        </x-ui.button>

                                        <x-ui.button type="button" variant="secondary" size="sm"
                                            x-on:click="downloadPng">
                                            Descargar PNG
                                        </x-ui.button>
                                    </div>
                                </div>
                            @endif
                        @endcan
                    </div>
                @endif

            </div>
        @endif

        {{-- ROLES --}}
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700">
                Roles
            </label>

            <div class="grid grid-cols-2 gap-2">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" value="{{ $role->name }}" wire:model="selectedRoles">
                        <span>{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>


    </div>

    <x-slot name="footer">
        <x-ui.button variant="secondary" wire:click="$set('userModal', false)">
            Cancelar
        </x-ui.button>

        <x-ui.button wire:click="saveUser">
            Guardar
        </x-ui.button>
    </x-slot>
</x-ui.modal>

<x-ui.modal wire:model="userModal" max-width="md" footer-position="between">
    <x-slot name="title">
        {{ $editingUserId ? 'Editar usuario' : 'Nuevo usuario' }}
    </x-slot>

    <div class="space-y-5 p-4">

        {{-- DATOS --}}
        <div class="grid grid-cols-1 gap-4">
            @if ($isLdapUser)
                <x-ui.alert type="info">
                    Este usuario está vinculado a LDAP. Sus datos de identidad son visibles, pero no editables.
                    Únicamente se permite modificar la asignación de roles.
                </x-ui.alert>
            @endif

            <x-ui.input label="Usuario" wire:model.defer="username" :disabled="$isLdapUser" />

            <x-ui.input label="Nombre" wire:model.defer="name" :disabled="$isLdapUser" />

            <x-ui.input label="Correo" type="email" wire:model.defer="email" :disabled="$isLdapUser" />
        </div>

        {{-- CONTRASEÑA --}}
        @if ($isLocalUser)
            <div class="space-y-3">

                <div class="flex items-center justify-between">
                    <x-ui.checkbox wire:model.live="changePassword" label="Cambiar contraseña" />
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
                            <x-ui.button type="button" variant="secondary" size="sm" wire:click="generatePassword"
                                wire:loading.attr="disabled" wire:target="generatePassword">
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
        <div class="p-2 border rounded-lg max-h-[100px] overflow-y-auto">
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

        {{-- A2F --}}
        @if ($editingUserId && $editingUserHasTwoFactor)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-amber-800">
                            Autenticación en dos factores activa
                        </h3>

                        <p class="mt-1 text-sm leading-5 text-amber-700">
                            Este usuario tiene 2FA configurado. Puedes restablecerlo si perdió acceso a su aplicación
                            autenticadora
                            o necesita configurarlo nuevamente.
                        </p>
                    </div>

                    @can('users.reset_2fa')
                        <x-ui.button type="button" variant="warning" size="sm"
                            wire:click="confirmResetTwoFactor({{ $editingUserId }})" wire:loading.attr="disabled"
                            wire:target="confirmResetTwoFactor({{ $editingUserId }})">
                            Restablecer 2FA
                        </x-ui.button>
                    @endcan
                </div>
            </div>
        @endif


    </div>

    <x-slot name="footer">
        <div>
            @if ($user->two_factor_secret || $user->two_factor_confirmed_at)
                <x-ui.button type="button" wire:click="confirmResetTwoFactor({{ $user->id }})" variant="danger"
                    size="sm" title="Restablecer 2FA" wire:loading.attr="disabled"
                    wire:target="confirmResetTwoFactor({{ $user->id }})">
                    Restablecer 2FA
                </x-ui.button>
            @endif
        </div>
        <div>
            <x-ui.button variant="secondary" wire:click="$set('userModal', false)">
                Cancelar
            </x-ui.button>

            <x-ui.button wire:click="saveUser" wire:loading.attr="disabled" wire:target="saveUser">
                <span wire:loading.remove wire:target="saveUser">Guardar</span>
                <span wire:loading wire:target="saveUser">Guardando...</span>
            </x-ui.button>
        </div>

    </x-slot>
</x-ui.modal>

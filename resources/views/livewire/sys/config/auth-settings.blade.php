<div class="space-y-6">

    <x-ui.panel>
        <div class="space-y-6 p-4">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">
                        Autenticación institucional
                    </h2>

                    <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-500">
                        Esta sección permite controlar los mecanismos de autenticación (local/LDAP).
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    @if ($ldapEnabled)
                        <x-ui.badge variant="success">
                            LDAP activo
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="secondary">
                            LDAP inactivo
                        </x-ui.badge>
                    @endif
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">

                {{-- LDAP ENABLED --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-800">
                                Autenticación LDAP
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Permite que los usuarios LDAP inicien sesión usando sus credenciales institucionales.
                            </p>
                        </div>

                        <x-ui.checkbox wire:model.live="ldapEnabled" />
                    </div>

                    <div class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-xs leading-5 text-slate-500">
                        Las credenciales sensibles del servidor LDAP permanecen en el archivo
                        <code>.env</code>. Desde esta pantalla solo se controla su activación operativa.
                    </div>
                </div>

                {{-- LDAP LOGGING --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-800">
                                Logging LDAP
                            </h3>

                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Registra eventos relacionados con autenticación LDAP para auditoría y diagnóstico.
                            </p>
                        </div>

                        <x-ui.checkbox wire:model.live="ldapLogging" />
                    </div>

                    <div class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-xs leading-5 text-slate-500">
                        Útil durante pruebas, migraciones o diagnóstico de errores de acceso institucional.
                    </div>
                </div>
            </div>

            {{-- TEST CONNECTION --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-slate-800">
                            Prueba de conexión LDAP
                        </h3>

                        <p class="mt-1 text-sm text-slate-500">
                            Verifica que el servidor LDAP responda y que el usuario de servicio pueda conectarse.
                        </p>
                    </div>

                    <x-ui.button type="button" variant="secondary" wire:click="testLdapConnection"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="testLdapConnection">
                            Probar conexión
                        </span>

                        <span wire:loading wire:target="testLdapConnection">
                            Probando...
                        </span>
                    </x-ui.button>
                </div>

                @if (!is_null($ldapConnectionOk))
                    <div class="mt-4">
                        <x-ui.alert :type="$ldapConnectionOk ? 'success' : 'error'">
                            {{ $ldapConnectionMessage }}
                        </x-ui.alert>
                    </div>
                @endif
            </div>

            {{-- FUTURE OPTIONS --}}
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-5">
                <h3 class="text-sm font-semibold text-slate-700">
                    Configuraciones futuras
                </h3>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Esta pantalla queda preparada para incorporar autenticación en dos pasos, políticas de acceso,
                    restricciones por tipo de usuario y reglas de autoregistro institucional.
                </p>
            </div>
        </div>
    </x-ui.panel>

    <x-ui.panel>
        <div class="flex items-start justify-between gap-4 p-4">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">
                    Autenticación en dos factores
                </h3>

                <p class="mt-1 text-sm leading-6 text-slate-500">
                    Obliga a los usuarios a confirmar su acceso mediante una aplicación TOTP,
                    como Google Authenticator o Microsoft Authenticator.
                </p>
            </div>

            <x-ui.checkbox wire:model.live="twoFactorEnabled" />
        </div>

        <div class="mt-4 rounded-xl bg-slate-50 px-4 py-3 text-xs leading-5 text-slate-500">
            Esta configuración aplica de forma global para usuarios locales y usuarios LDAP.
        </div>
    </x-ui.panel>
</div>

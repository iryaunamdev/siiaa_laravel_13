<div class="space-y-6">
    <x-ui.panel>
        <div class="p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">
                        Autenticación en dos factores
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Usa Google Authenticator, Microsoft Authenticator u otra aplicación compatible con códigos
                        TOTP.
                    </p>
                </div>

                @if (!$user->two_factor_secret)
                    <x-ui.badge variant="secondary">
                        No configurada
                    </x-ui.badge>
                @elseif (!$user->two_factor_confirmed_at)
                    <x-ui.badge variant="warning">
                        Pendiente de confirmar
                    </x-ui.badge>
                @else
                    <x-ui.badge variant="success">
                        Confirmada
                    </x-ui.badge>
                @endif
            </div>

            @if ($twoFactorEnabledGlobally && !$user->two_factor_confirmed_at)
                <x-ui.alert type="warning">
                    La autenticación en dos factores está activa de manera global. Para continuar usando el sistema,
                    debes configurarla y confirmar el código desde tu aplicación autenticadora.
                </x-ui.alert>
            @elseif ($twoFactorEnabledGlobally && $user->two_factor_confirmed_at)
                <x-ui.alert type="success">
                    Tu cuenta ya cumple con la política global de autenticación en dos factores.
                </x-ui.alert>
            @else
                <x-ui.alert type="info">
                    La autenticación en dos factores no está obligatoria actualmente.
                </x-ui.alert>
            @endif

            <div class="mt-6 space-y-5">
                @if (!$user->two_factor_secret)
                    <x-ui.button type="button" wire:click="enableTwoFactor" wire:loading.attr="disabled">
                        Activar 2FA
                    </x-ui.button>
                @else
                    @if (!$user->two_factor_confirmed_at)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="mb-3 text-sm font-medium text-slate-700">
                                Escanea este código QR con tu aplicación autenticadora:
                            </p>

                            <div class="inline-block rounded-xl bg-white p-4 shadow-sm">
                                {!! $user->twoFactorQrCodeSvg() !!}
                            </div>
                        </div>
                    @endif

                    @if (!$user->two_factor_confirmed_at)
                        <div class="max-w-sm space-y-3">
                            <x-ui.input label="Código de verificación" wire:model.defer="code" />

                            <x-ui.button type="button" wire:click="confirmTwoFactor" wire:loading.attr="disabled">
                                Confirmar código
                            </x-ui.button>
                        </div>
                    @endif

                    @if ($user->two_factor_confirmed_at)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="mb-3 text-sm font-medium text-slate-700">
                                Códigos de recuperación
                            </p>

                            <div class="grid gap-2 sm:grid-cols-2">
                                @foreach (json_decode(decrypt($user->two_factor_recovery_codes), true) as $recoveryCode)
                                    <code class="rounded-lg bg-white px-3 py-2 text-xs text-slate-700 shadow-sm">
                                        {{ $recoveryCode }}
                                    </code>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!$twoFactorEnabledGlobally)
                        <x-ui.button type="button" variant="danger" wire:click="disableTwoFactor"
                            wire:loading.attr="disabled">
                            Desactivar 2FA
                        </x-ui.button>
                    @endif
                @endif
            </div>
        </div>


    </x-ui.panel>
</div>

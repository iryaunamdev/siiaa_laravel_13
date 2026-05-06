<?php

namespace App\Livewire\Sys\Config;

use App\Services\Auth\LdapService;
use App\Services\System\SettingService;
use Livewire\Component;

class AuthSettings extends Component
{
    public bool $ldapEnabled = false;

    public bool $ldapLogging = false;

    public ?bool $ldapConnectionOk = null;

    public ?string $ldapConnectionMessage = null;

    public bool $twoFactorEnabled = false;

    public function mount(SettingService $settings): void
    {
        $this->ldapEnabled = (bool) $settings->get(
            'auth.ldap.enabled',
            config('ldap_auth.enabled', false)
        );

        $this->ldapLogging = (bool) $settings->get(
            'auth.ldap.logging',
            config('ldap_auth.logging', false)
        );

        $this->twoFactorEnabled = (bool) $settings->get(
            'auth.2fa.enabled',
            false
        );
    }

    public function updatedLdapEnabled(SettingService $settings): void
    {
        $settings->set(
            key: 'auth.ldap.enabled',
            value: $this->ldapEnabled,
            group: 'auth',
            type: 'boolean',
            label: 'Autenticación LDAP',
            description: 'Permite activar o desactivar la autenticación mediante LDAP.'
        );

        $this->resetConnectionState();

        $this->dispatch(
            'toast',
            type: 'success',
            message: $this->ldapEnabled
                ? 'Autenticación LDAP activada.'
                : 'Autenticación LDAP desactivada.'
        );
    }

    public function updatedLdapLogging(SettingService $settings): void
    {
        $settings->set(
            key: 'auth.ldap.logging',
            value: $this->ldapLogging,
            group: 'auth',
            type: 'boolean',
            label: 'Logging LDAP',
            description: 'Permite registrar eventos relacionados con autenticación LDAP.'
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: $this->ldapLogging
                ? 'Logging LDAP activado.'
                : 'Logging LDAP desactivado.'
        );
    }

    public function testLdapConnection(LdapService $ldap): void
    {
        $this->ldapConnectionOk = $ldap->testConnection();

        $this->ldapConnectionMessage = $this->ldapConnectionOk
            ? 'Conexión LDAP correcta.'
            : 'No fue posible conectar con el servidor LDAP.';

        $this->dispatch(
            'toast',
            type: $this->ldapConnectionOk ? 'success' : 'error',
            message: $this->ldapConnectionMessage
        );
    }

    protected function resetConnectionState(): void
    {
        $this->ldapConnectionOk = null;
        $this->ldapConnectionMessage = null;
    }

    public function updatedTwoFactorEnabled(SettingService $settings): void
    {
        $settings->set(
            key: 'auth.2fa.enabled',
            value: $this->twoFactorEnabled,
            group: 'auth',
            type: 'boolean',
            label: 'Autenticación en dos factores',
            description: 'Permite activar o desactivar la autenticación en dos factores de manera global.'
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: $this->twoFactorEnabled
                ? 'Autenticación en dos factores activada.'
                : 'Autenticación en dos factores desactivada.'
        );
    }

    public function render()
    {
        return view('livewire.sys.config.auth-settings');
    }
}

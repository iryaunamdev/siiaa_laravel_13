<?php

namespace App\Livewire\Sys\Users;

use App\Services\System\SettingService;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Livewire\Component;

class Security extends Component
{
    public string $code = '';

    public bool $twoFactorEnabledGlobally = false;

    public function mount(SettingService $settings): void
    {
        $this->twoFactorEnabledGlobally = (bool) $settings->get('auth.2fa.enabled', false);
    }

    public function enableTwoFactor(EnableTwoFactorAuthentication $enable): void
    {
        $enable(Auth::user());

        Auth::user()->refresh();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Autenticación en dos factores activada. Escanea el código QR y confirma el código.'
        );
    }

    public function confirmTwoFactor(ConfirmTwoFactorAuthentication $confirm): void
    {
        $this->validate([
            'code' => ['required', 'string'],
        ]);

        $confirm(Auth::user(), $this->code);

        Auth::user()->refresh();

        $this->reset('code');

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Autenticación en dos factores confirmada correctamente.'
        );

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function disableTwoFactor(DisableTwoFactorAuthentication $disable): void
    {
        $disable(Auth::user());

        Auth::user()->refresh();

        $this->reset('code');

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Autenticación en dos factores desactivada.'
        );
    }

    public function render()
    {
        return view('livewire.sys.users.security', [
            'user' => Auth::user(),
        ]);
    }
}

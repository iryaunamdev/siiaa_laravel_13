<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Auth;

trait AuthorizesSIIAA
{
    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::check() && Auth::user()->can($permission),
            403,
            'Acción no permitida.'
        );
    }

    protected function canPermission(string $permission): bool
    {
        return Auth::check() && Auth::user()->can($permission);
    }
}

<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class ClearCurrentIdentityOnLogout
{
    public function handle(Logout $event): void
    {
        session()->forget([
            'current_identity_id',
            'current_identity_type',
            'impersonating_identity',
            'impersonation_reason',
            'impersonation_started_by',
            'identity_warning',
            'identity_warning_type',
            'identity_warning_scope',
            'identity_warning_message',
        ]);
    }
}
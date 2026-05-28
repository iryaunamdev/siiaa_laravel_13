<?php

namespace App\Listeners;

use App\Services\Identity\IdentityResolverService;
use Illuminate\Auth\Events\Login;

class ResolveUserIdentityOnLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        app(IdentityResolverService::class)
            ->resolveForUser($event->user);
    }
}
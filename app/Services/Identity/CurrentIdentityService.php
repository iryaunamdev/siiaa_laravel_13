<?php

namespace App\Services\Identity;

use App\Models\IdentityLink;

class CurrentIdentityService
{
    public function id(): ?int
    {
        return session('current_identity_id');
    }

    public function type(): ?string
    {
        return session('current_identity_type');
    }

    public function get(): ?IdentityLink
    {
        $identityId = $this->id();

        if (! $identityId) {
            return null;
        }

        return IdentityLink::query()
            ->where('id', $identityId)
            ->where('active', true)
            ->first();
    }

    public function has(): bool
    {
        return filled($this->id());
    }

    public function isSiiaa(): bool
    {
        return $this->type() === IdentityLink::TYPE_SIIAA;
    }

    public function isSiiapStudent(): bool
    {
        return $this->type() === IdentityLink::TYPE_SIIAP_STUDENT;
    }

    public function isImpersonating(): bool
    {
        return session('impersonating_identity') === true
            && filled(session('current_identity_id'))
            && filled(session('impersonation_started_by'));
    }

    public function impersonatedBy(): ?int
    {
        return session('impersonation_started_by');
    }

    public function reason(): ?string
    {
        return session('impersonation_reason');
    }
}
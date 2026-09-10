<?php

namespace App\Policies\ConsejoInterno;

use App\Models\ConsejoInterno\CiReunion;
use App\Models\User;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;

class CiReunionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ci.reuniones_view')
            || $user->can('ci.reuniones_manage')
            || $user->can('ci.reuniones_evaluate');
    }

    public function view(User $user, CiReunion $reunion): bool
    {
        if ($user->can('ci.reuniones_manage') || $user->can('ci.reuniones_view')) {
            return true;
        }

        return $user->can('ci.reuniones_evaluate')
            && $this->participaEnReunion($reunion);
    }

    public function create(User $user): bool
    {
        return $user->can('ci.reuniones_manage');
    }

    public function update(User $user, CiReunion $reunion): bool
    {
        return $user->can('ci.reuniones_manage');
    }

    public function delete(User $user, CiReunion $reunion): bool
    {
        return $user->can('ci.reuniones_manage');
    }

    public function conclude(User $user, CiReunion $reunion): bool
    {
        return $user->can('ci.reuniones_manage');
    }

    public function evaluate(User $user, CiReunion $reunion): bool
    {
        return $user->can('ci.reuniones_evaluate')
            && $reunion->estatus === ConsejoInternoCatalogos::REUNION_EN_PROCESO
            && $this->participaEnReunion($reunion);
    }

    private function participaEnReunion(CiReunion $reunion): bool
    {
        $identityId = currentIdentityId();

        if (blank($identityId)) {
            return false;
        }

        return $reunion->participantes()
            ->where('identity_link_id', $identityId)
            ->exists();
    }
}
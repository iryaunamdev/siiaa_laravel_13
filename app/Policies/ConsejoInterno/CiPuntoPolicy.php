<?php

namespace App\Policies\ConsejoInterno;

use App\Models\ConsejoInterno\CiPunto;
use App\Models\User;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;

class CiPuntoPolicy
{
    public function view(User $user, CiPunto $punto): bool
    {
        if ($user->can('ci.reuniones_manage') || $user->can('ci.reuniones_view')) {
            return true;
        }

        return $user->can('ci.reuniones_evaluate')
            && $this->participaEnReunion($punto);
    }

    public function update(User $user, CiPunto $punto): bool
    {
        return $user->can('ci.reuniones_manage');
    }

    public function delete(User $user, CiPunto $punto): bool
    {
        return $user->can('ci.reuniones_manage');
    }

    public function evaluate(User $user, CiPunto $punto): bool
    {
        if ($punto->reunion?->estatus !== ConsejoInternoCatalogos::REUNION_EN_PROCESO) {
            return false;
        }

        if ($user->hasAnyRole(['admin-sistema', 'super-admin'])) {
            return true;
        }

        return $user->can('ci.reuniones_evaluate')
            && $this->participaEnReunion($punto);
    }
    public function resolve(User $user, CiPunto $punto): bool
    {
        return $user->can('ci.reuniones_manage');
    }

    private function participaEnReunion(CiPunto $punto): bool
    {
        $identityId = currentIdentityId();

        if (blank($identityId)) {
            return false;
        }

        return $punto->reunion
            ?->participantes()
            ->where('identity_link_id', $identityId)
            ->exists() ?? false;
    }
}

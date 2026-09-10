<?php

namespace App\Policies\ConsejoInterno;

use App\Models\ConsejoInterno\CiActa;
use App\Models\User;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;

class CiActaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ci.actas_manage') ||
            $user->can('ci.actas_evaluate') ||
            $user->can('ci.actas_view');
    }

    public function view(User $user, CiActa $acta): bool
    {
        if ($user->can('ci.actas_manage')) {
            return true;
        }

        if (
            $user->can('ci.actas_view') &&
            $acta->estatus === ConsejoInternoCatalogos::ACTA_PUBLICADA
        ) {
            return true;
        }

        return $this->evaluate($user, $acta);
    }

    public function create(User $user): bool
    {
        return $user->can('ci.actas_manage');
    }

    public function update(User $user, CiActa $acta): bool
    {
        return $user->can('ci.actas_manage');
    }

    public function delete(User $user, CiActa $acta): bool
    {
        return $user->can('ci.actas_manage');
    }

    public function publish(User $user, CiActa $acta): bool
    {
        return $user->can('ci.actas_manage');
    }

    public function evaluate(User $user, CiActa $acta): bool
    {
        if ($acta->estatus !== ConsejoInternoCatalogos::ACTA_BORRADOR) {
            return false;
        }

        /*
         * Los administradores técnicos pueden evaluar sin identidad
         * institucional activa.
         */
        if ($user->hasAnyRole(['admin-sistema', 'super-admin'])) {
            return true;
        }

        if (!$user->can('ci.actas_evaluate')) {
            return false;
        }

        /*
         * Un acta independiente puede ser evaluada sin pertenecer
         * a una reunión.
         */
        if (blank($acta->reunion_id)) {
            return true;
        }

        return $this->participaEnReunionLigada($acta);
    }

    public function viewInternal(User $user, CiActa $acta): bool
    {
        return $user->can('ci.actas_manage') ||
            $this->evaluate($user, $acta);
    }

    public function viewPublished(User $user, CiActa $acta): bool
    {
        return $user->can('ci.actas_view') &&
            $acta->estatus === ConsejoInternoCatalogos::ACTA_PUBLICADA;
    }

    private function participaEnReunionLigada(CiActa $acta): bool
    {
        $identityId = currentIdentityId();

        if (blank($identityId) || blank($acta->reunion_id)) {
            return false;
        }

        return $acta
            ->reunion
            ?->participantes()
            ->where('identity_link_id', $identityId)
            ->exists() ?? false;
    }
}

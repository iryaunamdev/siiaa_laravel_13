<?php

namespace App\Services\Perfiles;

use App\Models\IdentityLink;
use App\Models\PerfilPublico;
use Illuminate\Support\Collection;

class PerfilPublicoService
{
    /**
     * Crea o recupera un perfil público desde un IdentityLink.
     */
    public function firstOrCreateFromIdentity(
        IdentityLink $identity,
        bool $visible = false
    ): PerfilPublico {
        return PerfilPublico::firstOrCreate(
            [
                'identity_link_id' => $identity->id,
            ],
            [
                'active' => true,
                'visible' => $visible,
                'sort_order' => 0,
            ]
        );
    }

    /**
     * Crea o recupera un perfil público desde identity_links.id.
     */
    public function firstOrCreateFromIdentityId(
        int $identityLinkId,
        bool $visible = false
    ): ?PerfilPublico {
        $identity = IdentityLink::query()
            ->whereKey($identityLinkId)
            ->where('active', true)
            ->first();

        if (! $identity) {
            return null;
        }

        return $this->firstOrCreateFromIdentity($identity, $visible);
    }

    /**
     * Crea o recupera perfiles públicos para una colección de identidades.
     */
    public function firstOrCreateManyFromIdentities(
        Collection $identities,
        bool $visible = false
    ): Collection {
        return $identities
            ->map(fn(IdentityLink $identity) => $this->firstOrCreateFromIdentity($identity, $visible))
            ->values();
    }

    /**
     * Crea o recupera perfiles públicos para todas las identidades activas
     * de un tipo específico.
     */
    public function firstOrCreateManyByIdentityType(
        string $identityType,
        bool $visible = false
    ): Collection {
        $identities = IdentityLink::query()
            ->where('identity_type', $identityType)
            ->where('active', true)
            ->orderBy('id')
            ->get();

        return $this->firstOrCreateManyFromIdentities($identities, $visible);
    }

    /**
     * Actualiza datos públicos mínimos desde la identidad resuelta.
     *
     * No sobrescribe campos manuales si ya fueron capturados.
     */
    public function fillMissingPublicData(PerfilPublico $perfil): PerfilPublico
    {
        $identity = $perfil->identityLink;

        if (! $identity) {
            return $perfil;
        }

        $profile = $identity->profile();

        if (! $profile) {
            return $perfil;
        }

        $perfil->fill([
            'email_publico' => $perfil->email_publico ?: ($profile['email'] ?? null),
            'photo_path' => $perfil->photo_path,
        ]);

        /*
         * Se separa nombre/apellido de forma conservadora.
         * Si ya existe nombre_publico o apellido_publico, no se toca.
         */
        if (! $perfil->nombre_publico && ! $perfil->apellido_publico) {
            $perfil->nombre_publico = $profile['nombre'] ?? null;

            $apellidos = trim(
                ($profile['apellidop'] ?? '') . ' ' .
                    ($profile['apellidom'] ?? '')
            );

            $perfil->apellido_publico = $apellidos !== '' ? $apellidos : null;
        }

        /*
         * Para estudiantes SIIAP, el área pública inicial puede derivarse
         * del grado/programa actual si no hay captura manual.
         */
        if (! $perfil->area_es && ($profile['identity_type'] ?? null) === IdentityLink::TYPE_SIIAP_STUDENT) {
            $grado = $profile['grado_actual_nombre'] ?? null;
            $programa = $profile['inscripcion_actual']['programa']['nombre'] ?? null;

            $area = collect([$grado, $programa])
                ->filter()
                ->implode(' - ');

            $perfil->area_es = $area !== '' ? $area : null;
        }

        $perfil->save();

        return $perfil;
    }

    /**
     * Crea el perfil si no existe y completa campos públicos faltantes
     * sin sobrescribir capturas manuales.
     */
    public function firstOrCreateAndFillFromIdentity(
        IdentityLink $identity,
        bool $visible = false
    ): PerfilPublico {
        $perfil = $this->firstOrCreateFromIdentity($identity, $visible);

        return $this->fillMissingPublicData($perfil);
    }

    /**
     * Crea o completa perfiles públicos para identidades activas de un tipo.
     */
    public function firstOrCreateAndFillManyByIdentityType(
        string $identityType,
        bool $visible = false
    ): Collection {
        $identities = IdentityLink::query()
            ->where('identity_type', $identityType)
            ->where('active', true)
            ->orderBy('id')
            ->get();

        return $identities
            ->map(fn(IdentityLink $identity) => $this->firstOrCreateAndFillFromIdentity($identity, $visible))
            ->values();
    }
}

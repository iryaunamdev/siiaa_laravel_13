<?php

namespace App\Services\Identity;

use App\Models\IdentityLink;
use App\Services\Siiap\EstudianteProfileData;

class ProfileDataResolver
{
    public function __construct(
        protected EstudianteProfileData $estudianteProfileData,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Resolución general de perfiles
    |--------------------------------------------------------------------------
    |
    | Este resolver recibe:
    |
    | - identity_type: tipo de identidad institucional.
    | - identityId: ID determinístico de identity_links.id.
    |
    | Ejemplo para estudiantes SIIAP:
    |
    | identity_type = 'siiap_student'
    | identityId     = 20000000 + estudiantes.id
    |
    | No debe confundirse con identity_links.identity_id, que corresponde
    | al ID original de la fuente externa o interna.
    |
    */

    public function resolve(?string $identityType, ?int $identityId): ?array
    {
        if (! $identityType || ! $identityId) {
            return null;
        }

        return match ($identityType) {
            IdentityLink::TYPE_SIIAP_STUDENT => $this->estudianteProfileData
                ->makeByIdentityLinkId($identityId),

            // Futuro:
            // IdentityLink::TYPE_SIIAA => app(PersonaProfileData::class)
            //     ->makeByIdentityLinkId($identityId),

            default => null,
        };
    }

    public function fullname(?string $identityType, ?int $identityId): ?string
    {
        $profile = $this->resolve($identityType, $identityId);

        return $profile['fullname'] ?? null;
    }

    public function email(?string $identityType, ?int $identityId): ?string
    {
        $profile = $this->resolve($identityType, $identityId);

        return $profile['email'] ?? null;
    }

    public function photoUrl(?string $identityType, ?int $identityId): ?string
    {
        $profile = $this->resolve($identityType, $identityId);

        return $profile['photo_url'] ?? null;
    }

    public function initials(?string $identityType, ?int $identityId): string
    {
        $fullname = $this->fullname($identityType, $identityId);

        if (! $fullname) {
            return 'NA';
        }

        $parts = collect(explode(' ', trim($fullname)))
            ->filter()
            ->values();

        if ($parts->isEmpty()) {
            return 'NA';
        }

        if ($parts->count() === 1) {
            return mb_strtoupper(mb_substr($parts->first(), 0, 2));
        }

        return mb_strtoupper(
            mb_substr($parts->first(), 0, 1) .
                mb_substr($parts->last(), 0, 1)
        );
    }
}

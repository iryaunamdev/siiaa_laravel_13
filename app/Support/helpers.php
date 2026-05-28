<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Services\Identity\CurrentIdentityService;
use App\Services\Identity\ProfileDataResolver;
use App\Services\Siiap\EstudianteProfileData;
use App\Services\Perfiles\PerfilPublicoService;

use App\Models\Siiap\Estudiante;
use App\Models\PerfilPublico;
use App\Models\IdentityLink;


if (! function_exists('normalizeString')) {
    function normalizeString(string $value): string
    {
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return $normalized ?: $value;
    }
}

if (! function_exists('current_identity')) {
    function current_identity(): CurrentIdentityService
    {
        return app(CurrentIdentityService::class);
    }
}

if (! function_exists('currentSemestre')) {
    function currentSemestre(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();

        $year = (int) $date->year;
        $month = (int) $date->month;

        if ($month === 1) {
            return "{$year}-1";
        }

        if ($month > 2 && $month < 8) {
            return "{$year}-2";
        }

        $nextYear = $year + 1;

        return "{$nextYear}-1";
    }
}

if (! function_exists('ultimosTresSemestres')) {
    function ultimosTresSemestres(?string $semestre = null): array
    {
        $semestre = $semestre ?? currentSemestre();

        [$anio, $periodo] = explode('-', $semestre);

        $anio = (int) $anio;
        $periodo = (int) $periodo;

        $semestres = [];

        for ($i = 0; $i < 3; $i++) {
            $semestres[] = "{$anio}-{$periodo}";

            if ($periodo === 2) {
                $periodo = 1;
            } else {
                $periodo = 2;
                $anio--;
            }
        }

        return $semestres;
    }
}

if (! function_exists('semestreOrdenValue')) {
    function semestreOrdenValue(?string $semestre): int
    {
        if (empty($semestre) || ! str_contains($semestre, '-')) {
            return 0;
        }

        [$anio, $periodo] = explode('-', $semestre);

        return ((int) $anio * 10) + (int) $periodo;
    }
}

/*
|--------------------------------------------------------------------------
| Resolucion de identidades SIIAP
|--------------------------------------------------------------------------
*/
if (! function_exists('siiapStudentProfile')) {
    function siiapStudentProfile(?Estudiante $estudiante): ?array
    {
        if (! $estudiante) {
            return null;
        }

        return app(EstudianteProfileData::class)->make($estudiante);
    }
}

if (! function_exists('siiapStudentProfileBySourceId')) {
    function siiapStudentProfileBySourceId(int $sourceId): ?array
    {
        return app(EstudianteProfileData::class)->makeBySourceId($sourceId);
    }
}

if (! function_exists('siiapStudentProfileByIdentityId')) {
    function siiapStudentProfileByIdentityId(int $identityId): ?array
    {
        return app(EstudianteProfileData::class)->makeByIdentityLinkId($identityId);
    }
}

if (! function_exists('siiapStudentIdentityId')) {
    function siiapStudentIdentityId(int|Estudiante $student): int
    {
        $sourceId = $student instanceof Estudiante
            ? (int) $student->id
            : (int) $student;

        return app(EstudianteProfileData::class)
            ->identityLinkIdFromSourceId($sourceId);
    }
}

if (! function_exists('siiapStudentSourceId')) {
    function siiapStudentSourceId(int $identityId): ?int
    {
        return app(EstudianteProfileData::class)
            ->sourceIdFromIdentityLinkId($identityId);
    }
}

if (! function_exists('isSiiapStudentIdentityId')) {
    function isSiiapStudentIdentityId(int $identityId): bool
    {
        return app(EstudianteProfileData::class)
            ->isSiiapStudentIdentityLinkId($identityId);
    }
}

/*
|--------------------------------------------------------------------------
| Profile helpers
|--------------------------------------------------------------------------
*/

if (! function_exists('profileData')) {
    function profileData(?string $identityType, ?int $identityId): ?array
    {
        return app(ProfileDataResolver::class)
            ->resolve($identityType, $identityId);
    }
}

if (! function_exists('profileFullname')) {
    function profileFullname(?string $identityType, ?int $identityId): ?string
    {
        return app(ProfileDataResolver::class)
            ->fullname($identityType, $identityId);
    }
}

if (! function_exists('profileEmail')) {
    function profileEmail(?string $identityType, ?int $identityId): ?string
    {
        return app(ProfileDataResolver::class)
            ->email($identityType, $identityId);
    }
}

if (! function_exists('profilePhotoUrl')) {
    function profilePhotoUrl(?string $identityType, ?int $identityId): ?string
    {
        return app(ProfileDataResolver::class)
            ->photoUrl($identityType, $identityId);
    }
}

if (! function_exists('profileInitials')) {
    function profileInitials(?string $identityType, ?int $identityId): string
    {
        return app(ProfileDataResolver::class)
            ->initials($identityType, $identityId);
    }
}

/*
|--------------------------------------------------------------------------
| Helpers de indentidad actual
|--------------------------------------------------------------------------
*/

if (! function_exists('currentIdentityId')) {
    function currentIdentityId(): ?int
    {
        $identityId = session('current_identity_id');

        return $identityId ? (int) $identityId : null;
    }
}

if (! function_exists('currentIdentityType')) {
    function currentIdentityType(): ?string
    {
        return session('current_identity_type');
    }
}

if (! function_exists('currentIdentity')) {
    function currentIdentity(): ?IdentityLink
    {
        $identityId = currentIdentityId();

        if (! $identityId) {
            return null;
        }

        return IdentityLink::query()
            ->whereKey($identityId)
            ->where('active', true)
            ->first();
    }
}

if (! function_exists('currentProfile')) {
    function currentProfile(): ?array
    {
        $identity = currentIdentity();

        if (! $identity) {
            return null;
        }

        return profileData($identity->identity_type, $identity->id);
    }
}

if (! function_exists('currentProfileFullname')) {
    function currentProfileFullname(): ?string
    {
        $identity = currentIdentity();

        if (! $identity) {
            return null;
        }

        return profileFullname($identity->identity_type, $identity->id);
    }
}

if (! function_exists('currentProfileEmail')) {
    function currentProfileEmail(): ?string
    {
        $identity = currentIdentity();

        if (! $identity) {
            return null;
        }

        return profileEmail($identity->identity_type, $identity->id);
    }
}

if (! function_exists('currentProfilePhotoUrl')) {
    function currentProfilePhotoUrl(): ?string
    {
        $identity = currentIdentity();

        if (! $identity) {
            return null;
        }

        return profilePhotoUrl($identity->identity_type, $identity->id);
    }
}

if (! function_exists('currentProfileInitials')) {
    function currentProfileInitials(): string
    {
        $identity = currentIdentity();

        if (! $identity) {
            return 'NA';
        }

        return profileInitials($identity->identity_type, $identity->id);
    }
}

/*
|--------------------------------------------------------------------------
| Helpers para tipo de indentidad actual
|--------------------------------------------------------------------------
*/

if (! function_exists('currentIdentityIs')) {
    function currentIdentityIs(string $identityType): bool
    {
        return currentIdentityType() === $identityType;
    }
}

if (! function_exists('currentIdentityIsSiiapStudent')) {
    function currentIdentityIsSiiapStudent(): bool
    {
        return currentIdentityIs(IdentityLink::TYPE_SIIAP_STUDENT);
    }
}

if (! function_exists('currentIdentityIsSiiaa')) {
    function currentIdentityIsSiiaa(): bool
    {
        return currentIdentityIs(IdentityLink::TYPE_SIIAA);
    }
}

/*
|--------------------------------------------------------------------------
| Helpers para impersonation
|--------------------------------------------------------------------------
*/

if (! function_exists('isImpersonatingIdentity')) {
    function isImpersonatingIdentity(): bool
    {
        return session('impersonating_identity') === true;
    }
}

if (! function_exists('impersonationStartedBy')) {
    function impersonationStartedBy(): ?int
    {
        $userId = session('impersonation_started_by');

        return $userId ? (int) $userId : null;
    }
}

if (! function_exists('impersonationReason')) {
    function impersonationReason(): ?string
    {
        return session('impersonation_reason');
    }
}

/*
|--------------------------------------------------------------------------
| Helpers para perfil publico
|--------------------------------------------------------------------------
*/

if (! function_exists('publicProfileFromIdentity')) {
    function publicProfileFromIdentity(
        ?IdentityLink $identity,
        bool $visible = false,
        bool $fillMissingData = true
    ): ?PerfilPublico {
        if (! $identity) {
            return null;
        }

        $service = app(PerfilPublicoService::class);

        if ($fillMissingData) {
            return $service->firstOrCreateAndFillFromIdentity($identity, $visible);
        }

        return $service->firstOrCreateFromIdentity($identity, $visible);
    }
}

if (! function_exists('publicProfileFromIdentityId')) {
    function publicProfileFromIdentityId(
        ?int $identityLinkId,
        bool $visible = false,
        bool $fillMissingData = true
    ): ?PerfilPublico {
        if (! $identityLinkId) {
            return null;
        }

        $identity = IdentityLink::query()
            ->whereKey($identityLinkId)
            ->where('active', true)
            ->first();

        return publicProfileFromIdentity($identity, $visible, $fillMissingData);
    }
}

if (! function_exists('publicProfilesFromIdentities')) {
    function publicProfilesFromIdentities(
        Collection $identities,
        bool $visible = false,
        bool $fillMissingData = true
    ): Collection {
        $service = app(PerfilPublicoService::class);

        if ($fillMissingData) {
            return $identities
                ->map(fn(IdentityLink $identity) => $service->firstOrCreateAndFillFromIdentity($identity, $visible))
                ->values();
        }

        return $service->firstOrCreateManyFromIdentities($identities, $visible);
    }
}

if (! function_exists('publicProfilesByIdentityType')) {
    function publicProfilesByIdentityType(
        string $identityType,
        bool $visible = false,
        bool $fillMissingData = true
    ): Collection {
        $service = app(PerfilPublicoService::class);

        if ($fillMissingData) {
            return $service->firstOrCreateAndFillManyByIdentityType($identityType, $visible);
        }

        return $service->firstOrCreateManyByIdentityType($identityType, $visible);
    }
}

if (! function_exists('currentPublicProfile')) {
    function currentPublicProfile(
        bool $visible = false,
        bool $fillMissingData = true
    ): ?PerfilPublico {
        $identity = currentIdentity();

        return publicProfileFromIdentity($identity, $visible, $fillMissingData);
    }
}

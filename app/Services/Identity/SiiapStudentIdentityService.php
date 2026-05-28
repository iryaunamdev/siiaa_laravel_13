<?php

namespace App\Services\Identity;

use App\Models\IdentityLink;
use App\Models\Siiap\Estudiante;
use App\Services\Siiap\EstudianteProfileData;

class SiiapStudentIdentityService
{
    public function __construct(
        protected EstudianteProfileData $profileData,
    ) {}

    /**
     * Crea o actualiza la identidad de un estudiante SIIAP.
     *
     * Este método puede usarse tanto desde el login como desde comandos
     * periódicos de mantenimiento.
     */
    public function sync(
        Estudiante $estudiante,
        string $matchedBy = IdentityLink::MATCHED_BY_SIIAP
    ): IdentityLink {
        $profile = $this->profileData->make($estudiante);

        return IdentityLink::updateOrCreate(
            [
                'id' => $profile['identity_link_id'],
            ],
            [
                'identity_type' => IdentityLink::TYPE_SIIAP_STUDENT,
                'identity_id' => $profile['identity_id'], // ID original SIIAP
                'email' => $profile['email'],
                'is_primary' => false,
                'active' => (bool) $profile['activo'],
                'matched_by' => $matchedBy,
                'matched_at' => now(),
                'verified_at' => now(),
                'observaciones' => $this->buildObservaciones($profile),
            ]
        );
    }

    /**
     * Sincroniza usando el ID original del estudiante en SIIAP.
     */
    public function syncBySourceId(
        int $sourceId,
        string $matchedBy = IdentityLink::MATCHED_BY_SIIAP
    ): ?IdentityLink {
        $estudiante = $this->profileData->findBySourceId($sourceId);

        if (! $estudiante) {
            return null;
        }

        return $this->sync($estudiante, $matchedBy);
    }

    /**
     * Busca la identidad y, si no existe, la crea.
     *
     * Útil para procesos bajo demanda.
     */
    public function findOrSyncBySourceId(int $sourceId): ?IdentityLink
    {
        $identityLinkId = $this->profileData->identityLinkIdFromSourceId($sourceId);

        $identity = IdentityLink::query()
            ->whereKey($identityLinkId)
            ->first();

        if ($identity) {
            return $identity;
        }

        return $this->syncBySourceId($sourceId);
    }

    protected function buildObservaciones(array $profile): string
    {
        return trim(
            'Identidad sincronizada desde SIIAP. ' .
                'Grado actual: ' . ($profile['grado_actual_nombre'] ?? 'N/D') . '. ' .
                'Semestre: ' . ($profile['inscripcion_actual']['semestre']['nombre'] ?? 'N/D') . '. ' .
                'Adscripción: ' . ($profile['inscripcion_actual']['adscripcion']['clave'] ?? 'N/D') . '.'
        );
    }
}
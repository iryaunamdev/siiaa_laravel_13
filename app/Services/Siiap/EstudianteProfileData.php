<?php

namespace App\Services\Siiap;

use App\Models\IdentityLink;
use App\Models\Siiap\Estudiante;
use App\Models\Siiap\EstudianteTutor;
use Illuminate\Support\Collection;

class EstudianteProfileData
{
    public const IDENTITY_TYPE = IdentityLink::TYPE_SIIAP_STUDENT;

    public const IDENTITY_OFFSET = 20000000;

    /**
     * Construye un arreglo normalizado con la información pública/base
     * de un estudiante SIIAP para consumo desde SIIAA.
     */
    public function make(Estudiante $estudiante): array
    {
        $inscripcion = $estudiante->inscripcion_actual;

        $tutorPrincipal = $inscripcion?->tutor_principal?->tutor;

        return [
            /*
            |--------------------------------------------------------------------------
            | Identidad SIIAA
            |--------------------------------------------------------------------------
            |
            | identity_link_id corresponde a identity_links.id.
            | identity_id corresponde al ID original del estudiante en SIIAP.
            |
            */

            'identity_type' => self::IDENTITY_TYPE,
            'identity_link_id' => $this->identityLinkId($estudiante),
            'identity_id' => (int) $estudiante->id,
            'source_id' => (int) $estudiante->id,

            /*
            |--------------------------------------------------------------------------
            | Datos personales base
            |--------------------------------------------------------------------------
            */

            'cuenta' => $estudiante->cuenta,
            'orcid' => $estudiante->orcid,
            'nombre' => $estudiante->nombre,
            'apellidop' => $estudiante->apellidop,
            'apellidom' => $estudiante->apellidom,
            'fullname' => $estudiante->fullname,
            'email' => $estudiante->email,
            'email_alt' => $estudiante->email_alt,
            'photo_url' => $estudiante->photo_url,

            /*
            |--------------------------------------------------------------------------
            | Estado institucional
            |--------------------------------------------------------------------------
            */

            'activo' => $estudiante->activo,
            'grado_actual_clave' => $estudiante->grado_actual_clave,
            'grado_actual_nombre' => $estudiante->grado_actual_nombre,

            /*
            |--------------------------------------------------------------------------
            | Inscripción actual
            |--------------------------------------------------------------------------
            */

            'inscripcion_actual' => [
                'id' => $inscripcion?->id,

                'semestre' => [
                    'id' => $inscripcion?->semestre?->id,
                    'clave' => $inscripcion?->semestre?->clave,
                    'nombre' => $inscripcion?->semestre?->nombre,
                ],

                'grado' => [
                    'id' => $inscripcion?->grado?->id,
                    'clave' => $inscripcion?->grado?->clave,
                    'nombre' => $inscripcion?->grado?->nombre,
                ],

                'programa' => [
                    'id' => $inscripcion?->programa?->id,
                    'clave' => $inscripcion?->programa?->clave,
                    'nombre' => $inscripcion?->programa?->nombre,
                ],

                'adscripcion' => [
                    'id' => $inscripcion?->adscripcion?->id,
                    'clave' => $inscripcion?->adscripcion?->clave,
                    'nombre' => $inscripcion?->adscripcion?->nombre,
                ],
            ],

            /*
            |--------------------------------------------------------------------------
            | Tutoría
            |--------------------------------------------------------------------------
            */

            'tutor_principal' => $this->tutorData($tutorPrincipal),

            'comite_tutor' => $this->comiteTutor($estudiante),
        ];
    }

    /**
     * Construye el perfil a partir del ID original del estudiante en SIIAP.
     */
    public function makeBySourceId(int $sourceId): ?array
    {
        $estudiante = $this->findBySourceId($sourceId);

        if (! $estudiante) {
            return null;
        }

        return $this->make($estudiante);
    }

    /**
     * Construye el perfil a partir de identity_links.id.
     */
    public function makeByIdentityLinkId(int $identityLinkId): ?array
    {
        $sourceId = $this->sourceIdFromIdentityLinkId($identityLinkId);

        if ($sourceId === null) {
            return null;
        }

        return $this->makeBySourceId($sourceId);
    }

    /**
     * Obtiene el modelo Estudiante a partir del ID original de SIIAP.
     */
    public function findBySourceId(int $sourceId): ?Estudiante
    {
        return Estudiante::query()
            ->whereKey($sourceId)
            ->first();
    }

    /**
     * Obtiene el modelo Estudiante a partir de identity_links.id.
     */
    public function findByIdentityLinkId(int $identityLinkId): ?Estudiante
    {
        $sourceId = $this->sourceIdFromIdentityLinkId($identityLinkId);

        if ($sourceId === null) {
            return null;
        }

        return $this->findBySourceId($sourceId);
    }

    /**
     * Genera identity_links.id para un estudiante SIIAP.
     */
    public function identityLinkId(Estudiante $estudiante): int
    {
        return $this->identityLinkIdFromSourceId((int) $estudiante->id);
    }

    /**
     * Genera identity_links.id desde el ID original de SIIAP.
     */
    public function identityLinkIdFromSourceId(int $sourceId): int
    {
        return IdentityLink::makeIdentityId(
            IdentityLink::TYPE_SIIAP_STUDENT,
            $sourceId
        );
    }

    /**
     * Recupera el ID original de SIIAP desde identity_links.id.
     */
    public function sourceIdFromIdentityLinkId(int $identityLinkId): ?int
    {
        if (! $this->isSiiapStudentIdentityLinkId($identityLinkId)) {
            return null;
        }

        return $identityLinkId - self::IDENTITY_OFFSET;
    }

    /**
     * Valida si un ID determinístico pertenece al rango de estudiantes SIIAP.
     */
    public function isSiiapStudentIdentityLinkId(int $identityLinkId): bool
    {
        return $identityLinkId > self::IDENTITY_OFFSET
            && $identityLinkId < 30000000;
    }

    /*
    |--------------------------------------------------------------------------
    | Alias de compatibilidad
    |--------------------------------------------------------------------------
    |
    | Estos métodos conservan compatibilidad con helpers previamente propuestos.
    | Conceptualmente trabajan con identity_links.id, no con identity_links.identity_id.
    |
    */

    public function makeByIdentityId(int $identityId): ?array
    {
        return $this->makeByIdentityLinkId($identityId);
    }

    public function findByIdentityId(int $identityId): ?Estudiante
    {
        return $this->findByIdentityLinkId($identityId);
    }

    public function identityId(Estudiante $estudiante): int
    {
        return $this->identityLinkId($estudiante);
    }

    public function identityIdFromSourceId(int $sourceId): int
    {
        return $this->identityLinkIdFromSourceId($sourceId);
    }

    public function sourceIdFromIdentityId(int $identityId): ?int
    {
        return $this->sourceIdFromIdentityLinkId($identityId);
    }

    public function isSiiapStudentIdentityId(int $identityId): bool
    {
        return $this->isSiiapStudentIdentityLinkId($identityId);
    }

    /**
     * Construye el arreglo normalizado del comité tutor asociado
     * a la inscripción actual del estudiante.
     */
    protected function comiteTutor(Estudiante $estudiante): array
    {
        $comite = $estudiante->inscripcion_actual?->comite_tutor;

        if (! $comite instanceof Collection) {
            return [];
        }

        return $comite
            ->map(function (EstudianteTutor $estudianteTutor) {
                $tutor = $estudianteTutor->tutor;

                if (! $tutor) {
                    return null;
                }

                return [
                    'relacion_id' => $estudianteTutor->id,
                    'principal' => (bool) $estudianteTutor->principal,
                    'tutor' => $this->tutorData($tutor),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Normaliza los datos de un tutor.
     */
    protected function tutorData($tutor): ?array
    {
        if (! $tutor) {
            return null;
        }

        return [
            'id' => $tutor->id,
            'fullname' => $tutor->fullname,
            'nombre' => $tutor->nombre,
            'apellidop' => $tutor->apellidop,
            'apellidom' => $tutor->apellidom,
            'email' => $tutor->email ?? null,

            'grado' => [
                'id' => $tutor->grado?->id,
                'clave' => $tutor->grado?->clave,
                'nombre' => $tutor->grado?->nombre,
            ],

            'adscripcion' => [
                'id' => $tutor->adscripcion?->id,
                'clave' => $tutor->adscripcion?->clave,
                'nombre' => $tutor->adscripcion?->nombre,
            ],

            'sni' => [
                'id' => $tutor->sni?->id,
                'clave' => $tutor->sni?->clave,
                'nombre' => $tutor->sni?->nombre,
            ],

            'pride' => [
                'id' => $tutor->pride?->id,
                'clave' => $tutor->pride?->clave,
                'nombre' => $tutor->pride?->nombre,
            ],

            'contrato' => [
                'id' => $tutor->contrato?->id,
                'clave' => $tutor->contrato?->clave,
                'nombre' => $tutor->contrato?->nombre,
            ],
        ];
    }
}

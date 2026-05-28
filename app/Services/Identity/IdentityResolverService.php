<?php

namespace App\Services\Identity;

use App\Models\IdentityAccessLog;
use App\Models\IdentityLink;
use App\Models\Persona;
use App\Models\Siiap\Estudiante;
use App\Models\User;
use App\Services\Identity\SiiapStudentIdentityService;
use Illuminate\Support\Facades\DB;

class IdentityResolverService
{
    public function __construct(
        protected SiiapStudentIdentityService $siiapStudentIdentityService,
    ) {}
    /**
     * Resuelve la identidad institucional activa para el usuario autenticado.
     */
    public function resolveForUser(User $user): ?IdentityLink
    {
        if ($this->hasActiveImpersonation()) {
            return $this->handleActiveImpersonation($user);
        }

        if (! filled($user->email)) {
            $this->clearCurrentIdentityFromSession();
            $this->storeMissingIdentityWarning();

            return null;
        }

        $email = mb_strtolower(trim($user->email));

        return DB::transaction(function () use ($user, $email) {
            $identityLink = $this->resolveSiiaaIdentity($email);

            if (! $identityLink) {
                $identityLink = $this->resolveSiiapStudentIdentity($email);
            }

            if (! $identityLink) {
                $this->clearCurrentIdentityFromSession();
                $this->storeMissingIdentityWarning();

                return null;
            }

            $this->storeCurrentIdentityInSession($identityLink);
            $this->clearIdentityWarning();
            $this->logNormalAccess($user, $identityLink);

            return $identityLink;
        });
    }

    /**
     * Indica si hay una suplantación activa en sesión.
     */
    protected function hasActiveImpersonation(): bool
    {
        return session('impersonating_identity') === true
            && filled(session('current_identity_id'))
            && filled(session('impersonation_started_by'));
    }

    /**
     * Maneja el caso donde ya existe impersonation activa.
     *
     * No sobrescribe current_identity_id.
     * Solo registra log y mantiene aviso administrativo para el super-admin.
     */
    protected function handleActiveImpersonation(User $user): ?IdentityLink
    {
        $identityLink = IdentityLink::query()
            ->where('id', session('current_identity_id'))
            ->where('active', true)
            ->first();

        if (! $identityLink) {
            $this->clearCurrentIdentityFromSession();
            $this->storeMissingIdentityWarning();

            return null;
        }

        $this->storeImpersonationWarning();
        $this->logImpersonationAccess($user, $identityLink);

        return $identityLink;
    }

    /**
     * Resuelve identidad local SIIAA por correo.
     */
    protected function resolveSiiaaIdentity(string $email): ?IdentityLink
    {
        $persona = Persona::query()
            ->where('email', $email)
            ->first();

        if (! $persona) {
            return null;
        }

        return IdentityLink::updateOrCreate(
            [
                'id' => IdentityLink::makeIdentityId(
                    IdentityLink::TYPE_SIIAA,
                    $persona->id
                ),
            ],
            [
                'identity_type' => IdentityLink::TYPE_SIIAA,
                'identity_id' => $persona->id,
                'email' => $persona->email,
                'is_primary' => true,
                'active' => true,
                'matched_by' => IdentityLink::MATCHED_BY_EMAIL,
                'matched_at' => now(),
                'verified_at' => now(),
            ]
        );
    }

    /**
     * Resuelve identidad de estudiante SIIAP por correo.
     *
     * Reglas:
     * - Busca por email o email_alt.
     * - Solo considera estudiantes activos IRyA.
     * - Crea o actualiza identity_links bajo demanda durante login.
     * - No depende del comando periódico.
     */
    protected function resolveSiiapStudentIdentity(string $email): ?IdentityLink
    {
        $estudiante = Estudiante::query()
            ->activosIrya()
            ->where(function ($query) use ($email) {
                $query->whereRaw('LOWER(email) = ?', [$email])
                    ->orWhereRaw('LOWER(email_alt) = ?', [$email]);
            })
            ->first();

        if (! $estudiante) {
            return null;
        }

        return $this->siiapStudentIdentityService->sync(
            estudiante: $estudiante,
            matchedBy: IdentityLink::MATCHED_BY_SIIAP
        );
    }

    /**
     * Guarda identidad activa normal en sesión.
     */
    protected function storeCurrentIdentityInSession(IdentityLink $identityLink): void
    {
        session([
            'current_identity_id' => $identityLink->id,
            'current_identity_type' => $identityLink->identity_type,
            'impersonating_identity' => false,
        ]);
    }

    /**
     * Limpia identidad activa de sesión.
     */
    protected function clearCurrentIdentityFromSession(): void
    {
        session()->forget([
            'current_identity_id',
            'current_identity_type',
            'impersonating_identity',
            'impersonation_reason',
            'impersonation_started_by',
        ]);
    }

    /**
     * Guarda alerta persistente para usuario sin identidad institucional.
     */
    protected function storeMissingIdentityWarning(): void
    {
        session([
            'identity_warning' => true,
            'identity_warning_type' => 'missing_identity',
            'identity_warning_scope' => 'user',
            'identity_warning_message' => 'No se encontró una identidad institucional asociada a tu correo electrónico. Algunas funciones pueden estar limitadas. Informa al administrador del sistema para revisar tu registro en SIIAA o SIIAP.',
        ]);
    }

    /**
     * Guarda alerta administrativa para el super-admin suplantador.
     */
    protected function storeImpersonationWarning(): void
    {
        session([
            'identity_warning' => true,
            'identity_warning_type' => 'impersonation',
            'identity_warning_scope' => 'admin',
            'identity_warning_message' => 'Modo suplantación activo. Verifica que no interfieras con una sesión o trabajo activo del usuario asociado.',
        ]);
    }

    /**
     * Limpia alertas de identidad.
     */
    protected function clearIdentityWarning(): void
    {
        session()->forget([
            'identity_warning',
            'identity_warning_type',
            'identity_warning_scope',
            'identity_warning_message',
        ]);
    }

    /**
     * Registra acceso normal.
     */
    protected function logNormalAccess(User $user, IdentityLink $identityLink): void
    {
        IdentityAccessLog::create([
            'user_id' => $user->id,
            'identity_link_id' => $identityLink->id,
            'access_type' => IdentityAccessLog::ACCESS_NORMAL,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'started_at' => now(),
        ]);
    }

    /**
     * Registra acceso durante impersonation.
     */
    protected function logImpersonationAccess(User $user, IdentityLink $identityLink): void
    {
        IdentityAccessLog::create([
            'user_id' => $user->id,
            'identity_link_id' => $identityLink->id,
            'access_type' => IdentityAccessLog::ACCESS_IMPERSONATION,
            'impersonated_by' => session('impersonation_started_by', $user->id),
            'reason' => session('impersonation_reason'),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'started_at' => now(),
        ]);
    }
}
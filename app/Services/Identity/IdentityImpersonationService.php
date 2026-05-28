<?php

namespace App\Services\Identity;

use App\Models\IdentityAccessLog;
use App\Models\IdentityLink;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IdentityImpersonationService
{
    /**
     * Inicia una suplantación de identidad institucional.
     */
    public function start(User $user, int $identityLinkId, string $reason): IdentityLink
    {
        $this->ensureCanImpersonate($user);

        $reason = trim($reason);

        if ($reason === '') {
            throw ValidationException::withMessages([
                'reason' => 'El motivo de la suplantación es obligatorio.',
            ]);
        }

        return DB::transaction(function () use ($user, $identityLinkId, $reason) {
            $identityLink = IdentityLink::query()
                ->where('id', $identityLinkId)
                ->where('active', true)
                ->firstOrFail();

            $this->closeActiveImpersonationLog($user);

            session([
                'current_identity_id' => $identityLink->id,
                'current_identity_type' => $identityLink->identity_type,
                'impersonating_identity' => true,
                'impersonation_started_by' => $user->id,
                'impersonation_reason' => $reason,
                'identity_warning' => true,
                'identity_warning_type' => 'impersonation',
                'identity_warning_scope' => 'admin',
                'identity_warning_message' => 'Modo suplantación activo. Verifica que no interfieras con una sesión o trabajo activo del usuario asociado.',
            ]);

            IdentityAccessLog::create([
                'user_id' => $user->id,
                'identity_link_id' => $identityLink->id,
                'access_type' => IdentityAccessLog::ACCESS_IMPERSONATION,
                'impersonated_by' => $user->id,
                'reason' => $reason,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'started_at' => now(),
            ]);

            return $identityLink;
        });
    }

    /**
     * Finaliza la suplantación activa del usuario.
     */
    public function stop(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->closeActiveImpersonationLog($user);

            session()->forget([
                'current_identity_id',
                'current_identity_type',
                'impersonating_identity',
                'impersonation_started_by',
                'impersonation_reason',
                'identity_warning',
                'identity_warning_type',
                'identity_warning_scope',
                'identity_warning_message',
            ]);
        });
    }

    /**
     * Indica si hay suplantación activa en sesión.
     */
    public function isActive(): bool
    {
        return session('impersonating_identity') === true
            && filled(session('current_identity_id'))
            && filled(session('impersonation_started_by'));
    }

    /**
     * Obtiene la identidad suplantada actual.
     */
    public function currentIdentity(): ?IdentityLink
    {
        if (! $this->isActive()) {
            return null;
        }

        return IdentityLink::query()
            ->where('id', session('current_identity_id'))
            ->first();
    }

    /**
     * Valida que el usuario pueda suplantar identidades.
     */
    protected function ensureCanImpersonate(User $user): void
    {
        if (! $user->hasRole('super-admin')) {
            abort(403, 'No tienes permisos para suplantar identidades.');
        }
    }

    /**
     * Cierra el log activo de suplantación del usuario.
     */
    protected function closeActiveImpersonationLog(User $user): void
    {
        IdentityAccessLog::query()
            ->where('user_id', $user->id)
            ->where('access_type', IdentityAccessLog::ACCESS_IMPERSONATION)
            ->whereNull('ended_at')
            ->update([
                'ended_at' => now(),
            ]);
    }
}
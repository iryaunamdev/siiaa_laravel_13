<?php

namespace App\Policies;

use App\Models\Solicitudes\Solicitud;
use App\Models\User;

class SolicitudPolicy
{
    /**
     * Obtiene la identidad institucional activa del usuario.
     */
    protected function activeIdentityId(User $user): ?int
    {
        return \currentIdentityId();
    }

    /**
     * Puede entrar al listado de solicitudes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('solicitudes.access')
            || $user->can('solicitudes.review')
            || $user->can('solicitudes.manage');
    }

    /**
     * Puede ver una solicitud específica.
     */
    public function view(User $user, Solicitud $solicitud): bool
    {
        if ($this->manage($user)) {
            return true;
        }

        if ($user->can('solicitudes.review')) {
            return true;
        }

        if (! $user->can('solicitudes.access')) {
            return false;
        }

        return $solicitud->perteneceA($this->activeIdentityId($user));
    }

    /**
     * Puede crear solicitudes propias.
     */
    public function create(User $user): bool
    {
        return $user->can('solicitudes.access')
            && $this->activeIdentityId($user) !== null;
    }

    /**
     * Puede actualizar una solicitud.
     */
    public function update(User $user, Solicitud $solicitud): bool
    {
        if ($solicitud->estaArchivada()) {
            return $this->manage($user);
        }

        if ($this->manage($user)) {
            return true;
        }

        if (! $user->can('solicitudes.access')) {
            return false;
        }

        return $solicitud->perteneceA($this->activeIdentityId($user))
            && $solicitud->puedeEditarPropietario();
    }

    /**
     * Puede enviar una solicitud.
     */
    public function send(User $user, Solicitud $solicitud): bool
    {
        if ($solicitud->estaArchivada()) {
            return false;
        }

        if (! $user->can('solicitudes.access')) {
            return false;
        }

        return $solicitud->perteneceA($this->activeIdentityId($user))
            && $solicitud->puedeEnviar();
    }

    /**
     * Puede cancelar una solicitud.
     */
    public function cancel(User $user, Solicitud $solicitud): bool
    {
        if ($solicitud->estaArchivada()) {
            return false;
        }

        if ($this->manage($user)) {
            return ! $solicitud->estaFinalizada();
        }

        if (! $user->can('solicitudes.access')) {
            return false;
        }

        return $solicitud->perteneceA($this->activeIdentityId($user))
            && $solicitud->puedeCancelarPropietario();
    }

    /**
     * Puede revisar una solicitud enviada.
     */
    public function review(User $user, Solicitud $solicitud): bool
    {
        if ($solicitud->estaArchivada()) {
            return false;
        }

        return ($user->can('solicitudes.review') || $this->manage($user))
            && $solicitud->puedeRevisarse();
    }

    /**
     * Puede aprobar una solicitud.
     */
    public function approve(User $user, Solicitud $solicitud): bool
    {
        return $this->review($user, $solicitud);
    }

    /**
     * Puede rechazar una solicitud.
     */
    public function reject(User $user, Solicitud $solicitud): bool
    {
        return $this->review($user, $solicitud);
    }

    /**
     * Puede pasar una solicitud a trámite de pago.
     */
    public function markPaymentProcess(User $user, Solicitud $solicitud): bool
    {
        if ($solicitud->estaArchivada()) {
            return false;
        }

        return $this->manage($user)
            && $solicitud->puedePasarATramitePago();
    }

    /**
     * Puede marcar una solicitud como pagada.
     */
    public function markPaid(User $user, Solicitud $solicitud): bool
    {
        if ($solicitud->estaArchivada()) {
            return false;
        }

        return $this->manage($user)
            && $solicitud->puedeMarcarsePagada();
    }

    /**
     * Puede cerrar una solicitud.
     */
    public function close(User $user, Solicitud $solicitud): bool
    {
        if ($solicitud->estaArchivada()) {
            return false;
        }

        return $this->manage($user)
            && $solicitud->puedeCerrarse();
    }

    /**
     * Puede archivar lógicamente una solicitud.
     */
    public function archive(User $user, Solicitud $solicitud): bool
    {
        return $this->manage($user)
            && ! $solicitud->estaArchivada();
    }

    /**
     * Puede borrar físicamente una solicitud.
     *
     * Regla aprobada:
     * - El administrador/manager puede borrar físicamente con advertencia
     *   explícita de borrado en cascada.
     * - El propietario institucional solo puede borrar físicamente mientras
     *   la solicitud está en borrador.
     */
    public function delete(User $user, Solicitud $solicitud): bool
    {
        if ($this->manage($user)) {
            return true;
        }

        if (! $user->can('solicitudes.access')) {
            return false;
        }

        return $solicitud->perteneceA($this->activeIdentityId($user))
            && $solicitud->esBorrador();
    }

    /**
     * Administración amplia del módulo.
     */
    public function manage(User $user): bool
    {
        return $user->can('solicitudes.manage');
    }
}

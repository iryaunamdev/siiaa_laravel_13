<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserAccessLog;
use Illuminate\Http\Request;

class AccessLogger
{
    /**
     * Registra un evento de acceso/autenticación.
     *
     * Este servicio centraliza la bitácora de accesos para evitar
     * lógica duplicada en Fortify, listeners u otros flujos futuros
     * como LDAP o 2FA.
     */
    public function log(
        Request $request,
        string $event,
        ?User $user = null,
        ?string $authType = null,
        ?array $context = null
    ): UserAccessLog {
        return UserAccessLog::create([
            'user_id' => $user?->id,
            'event' => $event,
            'auth_type' => $authType ?? $user?->auth_type,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
            'context' => $context,
        ]);
    }
}
@if (session('identity_warning'))
    @php
        $warningType = session('identity_warning_type');
        $warningScope = session('identity_warning_scope');
        $warningMessage = session('identity_warning_message');

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Roles que no requieren identidad institucional
        |--------------------------------------------------------------------------
        | Los usuarios administrativos pueden operar el sistema sin estar vinculados
        | a una identidad institucional de persona o estudiante.
        */
        $identityBypassRoles = ['admin', 'super-admin'];

        $bypassesIdentityWarning =
            $user && method_exists($user, 'hasAnyRole') ? $user->hasAnyRole($identityBypassRoles) : false;

        $shouldShow = false;

        /*
        |--------------------------------------------------------------------------
        | Advertencia por identidad faltante
        |--------------------------------------------------------------------------
        | Solo se muestra para usuarios normales sin identidad institucional.
        | Se omite para admin y super-admin.
        */
        if ($warningType === 'missing_identity' && $warningScope === 'user' && !$bypassesIdentityWarning) {
            $shouldShow = true;
        }

        /*
        |--------------------------------------------------------------------------
        | Advertencia por suplantación
        |--------------------------------------------------------------------------
        | Esta advertencia sí debe mostrarse al administrador que inició
        | la suplantación, aunque sea admin o super-admin.
        */
        if (
            $warningType === 'impersonation' &&
            $warningScope === 'admin' &&
            session('impersonation_started_by') === auth()->id()
        ) {
            $shouldShow = true;
        }
    @endphp

    @if ($shouldShow)
        <div class="mb-4">
            <x-ui.alert type="warning">
                {{ $warningMessage }}
            </x-ui.alert>
        </div>
    @endif
@endif

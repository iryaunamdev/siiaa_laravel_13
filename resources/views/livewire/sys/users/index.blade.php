<x-ui.panel title="Administración de usuarios" description="Listado de usuarios registrados en el sistema." size="full">
    <x-slot:actions>
        @can('sys.users.create')
            <x-ui.button href="{{ route('sys.users.create') }}" variant="primary" size="sm">
                Crear usuario
            </x-ui.button>
        @endcan
    </x-slot:actions>

    <x-ui.table>
        <x-ui.table.head>
            <x-ui.table.row>
                <x-ui.table.header>ID</x-ui.table.header>
                <x-ui.table.header>Usuario</x-ui.table.header>
                <x-ui.table.header>Nombre</x-ui.table.header>
                <x-ui.table.header>Correo</x-ui.table.header>
                <x-ui.table.header>Autenticación</x-ui.table.header>
                <x-ui.table.header>Estado</x-ui.table.header>
                <x-ui.table.header>Último acceso</x-ui.table.header>
                <x-ui.table.header align="right">Acciones</x-ui.table.header>
            </x-ui.table.row>
        </x-ui.table.head>

        <x-ui.table.body>
            @forelse ($users as $user)
                <x-ui.table.row>
                    <x-ui.table.cell>
                        {{ $user->id }}
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        <div class="flex items-center gap-3">
                            <x-ui.avatar :model="$user" size="sm" />

                            <div>
                                <p class="font-medium text-slate-900">
                                    {{ $user->username }}
                                </p>

                                <p class="text-xs text-slate-500">
                                    {{ $user->name }}
                                </p>
                            </div>
                        </div>
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        {{ $user->name }}
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        {{ $user->email ?? '—' }}
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        <x-ui.badge variant="info" text-size="text-[0.65rem]">
                            {{ strtoupper($user->auth_type) }}
                        </x-ui.badge>
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        @if ($user->is_active)
                            <x-ui.badge variant="success" text-size="text-xs">
                                Activo
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="danger" text-size="text-xs">
                                Inactivo
                            </x-ui.badge>
                        @endif
                    </x-ui.table.cell>

                    <x-ui.table.cell>
                        {{ $user->last_login_at?->format('Y-m-d H:i') ?? '—' }}
                    </x-ui.table.cell>

                    <x-ui.table.cell align="right">
                        @can('sys.users.update')
                            <x-ui.button href="{{ route('sys.users.edit', $user) }}" variant="link" size="sm">
                                Editar
                            </x-ui.button>
                        @endcan
                    </x-ui.table.cell>
                </x-ui.table.row>
            @empty
                <x-ui.table.empty colspan="8">
                    No existen usuarios registrados.
                </x-ui.table.empty>
            @endforelse
        </x-ui.table.body>
    </x-ui.table>
</x-ui.panel>

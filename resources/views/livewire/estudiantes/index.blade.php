<div class="space-y-4">
    <x-ui.panel title="Estudiantes IRyA"
        description="Listado de estudiantes obtenidos desde SIIAP con adscripción al IRyA vigente">

        {{-- Filtros --}}
        <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                <div class="lg:col-span-3">
                    <x-ui.input label="Búsqueda" placeholder="Nombre, correo o ID"
                        wire:model.live.debounce.400ms="search" />
                </div>
                <div class="col-span-2">
                    <x-ui.select name="grado_id" label="Grado" wire:model.live="grado" :options="$c_grados"
                        option-value="clave" option-label="nombre" />
                </div>
            </div>
        </div>

        <div class="px-4">
            <x-ui.table>
                <x-ui.table.head>
                    <x-ui.table.row>
                        <x-ui.table.header>Estudiante</x-ui.table.header>
                        <x-ui.table.header>Programa</x-ui.table.header>
                        <x-ui.table.header>Última inscripción</x-ui.table.header>
                        <x-ui.table.header>Comité tutor</x-ui.table.header>
                        <x-ui.table.header>Estatus</x-ui.table.header>
                        <x-ui.table.header class="text-right"></x-ui.table.header>
                    </x-ui.table.row>
                </x-ui.table.head>

                <x-ui.table.body>
                    @forelse ($estudiantes as $estudiante)
                        @php
                            $inscripcionesOrdenadas = $estudiante->inscripciones
                                ->sortByDesc(fn($inscripcion) => semestreOrdenValue($inscripcion->semestre?->nombre))
                                ->values();

                            $ultima = $inscripcionesOrdenadas->first();

                            $comiteActual = $ultima?->comite ?? collect();

                            $tutorPrincipal = $comiteActual->firstWhere('principal', true);
                        @endphp

                        <x-ui.table.row wire:key="estudiante-{{ $estudiante->id }}">
                            <x-ui.table.cell>
                                <div class="flex items-center gap-3">
                                    <x-ui.avatar :name="$estudiante->fullname"
                                        :initials="$estudiante->initials">{{ $estudiante->initials }}</x-ui.avatar>

                                    <div>
                                        <div class="text-sm font-semibold text-zinc-800">
                                            {{ $estudiante->fullname }}
                                        </div>

                                        <div class="text-xs text-blue-500">
                                            @if ($estudiante->email)
                                                {{ $estudiante->email }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </x-ui.table.cell>

                            <x-ui.table.cell>
                                <div class="text-sm text-zinc-800">
                                    {{ $ultima?->programa?->nombre ?? 'Sin programa registrado' }}
                                </div>

                                <div class="text-xs text-zinc-500">
                                    {{ $ultima?->grado?->nombre ?? 'Sin grado' }}
                                </div>
                            </x-ui.table.cell>

                            <x-ui.table.cell>
                                <div class="text-sm text-zinc-800">
                                    {{ $ultima?->semestre?->nombre ?? 'Sin inscripción' }}
                                </div>

                                <div class="text-xs text-zinc-500">
                                    {{ $ultima?->adscripcion?->nombre ?? 'Sin adscripción' }}
                                </div>
                            </x-ui.table.cell>

                            <x-ui.table.cell>
                                @if ($comiteActual->isNotEmpty())
                                    <div class="text-sm text-zinc-800">
                                        {{ $comiteActual->count() }} integrante(s)
                                    </div>

                                    <div class="text-xs text-zinc-500">
                                        Principal:
                                        {{ $tutorPrincipal?->tutor?->fullname ?? 'No definido' }}
                                    </div>
                                @else
                                    <x-ui.badge variant="warning">
                                        Sin comité
                                    </x-ui.badge>
                                @endif
                            </x-ui.table.cell>

                            <x-ui.table.cell>
                                @if ($estudiante->activo)
                                    <x-ui.badge variant="success">
                                        Activo
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary">
                                        Inactivo
                                    </x-ui.badge>
                                @endif

                                <x-ui.badge variant="neutral" class="ml-2 text-[0.650rem]">
                                    ID SIIAP: {{ $estudiante->id }}
                                </x-ui.badge>
                            </x-ui.table.cell>

                            <x-ui.table.cell class="text-right">
                                <x-ui.button type="button" variant="link"
                                    wire:click="openResumen({{ $estudiante->id }})">
                                    <x-ui.icon name="eye-open" class="w-5 h-5 text-blue-500 hover:text-blue-600" />
                                </x-ui.button>
                            </x-ui.table.cell>
                        </x-ui.table.row>
                    @empty
                        <x-ui.table.empty colspan="6">
                            No se encontraron estudiantes con los filtros seleccionados.
                        </x-ui.table.empty>
                    @endforelse
                </x-ui.table.body>
            </x-ui.table>

            <div class="mt-4">
                {{ $estudiantes->links() }}
            </div>
        </div>
    </x-ui.panel>
    @include('livewire.estudiantes._show_modal')
</div>

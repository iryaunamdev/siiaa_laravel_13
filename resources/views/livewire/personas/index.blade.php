<div class="space-y-4">
    <x-ui.panel title="Personal IRyA">
        <x-slot name="actions">
            @can('personas.create')
                <x-ui.button type="button" variant="primary" href="{{ route('personas.create') }}">
                    Nueva persona
                </x-ui.button>
            @endcan
        </x-slot>

        {{-- Filtros --}}
        <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between px-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="w-full sm:w-80">
                    <x-ui.input wire:model.live.debounce.400ms="search" name="search" label="Buscar"
                        placeholder="Nombre, correo, CURP o RFC" />
                </div>

                <div class="w-full sm:w-44">
                    <x-ui.select wire:model.live="status" name="status" label="Estado" :options="[
                        'active' => 'Activas',
                        'inactive' => 'Inactivas',
                        'all' => 'Todas',
                    ]" />
                </div>

                <div class="w-full sm:w-44">
                    <x-ui.select name="tipo_personal_id" label="Tipo de personal" wire:model.live="tipoPersonalId"
                        :options="$c_tiposPersonal" option-value="id" option-label="nombre" placeholder="Todos los tipos" />
                </div>

                <div class="w-full sm:w-28">
                    <x-ui.select wire:model.live="perPage" name="perPage" label="Mostrar" :options="[
                        10 => '10',
                        15 => '15',
                        25 => '25',
                        50 => '50',
                    ]" />
                </div>

                <div>
                    <x-ui.button type="button" variant="secondary" wire:click="resetFilters">
                        Limpiar
                    </x-ui.button>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="px-4">
            <x-ui.table>
                <x-ui.table.head>
                    <x-ui.table.row>
                        <x-ui.table.header>Personal IRyA</x-ui.table.header>
                        <x-ui.table.header>No. trabajador</x-ui.table.header>
                        <x-ui.table.header>Tipo de personal</x-ui.table.header>
                        <x-ui.table.header></x-ui.table.header>
                        <x-ui.table.header></x-ui.table.header>
                        <x-ui.table.header></x-ui.table.header>
                        <x-ui.table.header></x-ui.table.header>
                        <x-ui.table.header>Antiguedad</x-ui.table.header>
                        <x-ui.table.header></x-ui.table.header>
                        <x-ui.table.header></x-ui.table.header>
                    </x-ui.table.row>
                </x-ui.table.head>
                <x-ui.table.row>
                    @forelse ($personas as $persona)
                        <x-ui.table.row>
                            <x-ui.table.cell>
                                <div class="flex align-center justify-start space-x-4">
                                    <x-ui.avatar>{{ $persona->initials }}</x-ui.avatar>
                                    <div>
                                        <div class="text-sm font-semibold">
                                            {{ trim(($persona->nombre ?? '') . ' ' . ($persona->apellidop ?? '') . ' ' . ($persona->apellidom ?? '')) }}
                                        </div>

                                        <div class="text-xs text-blue-500">
                                            {{ $persona->email }}
                                        </div>
                                    </div>
                                </div>
                            </x-ui.table.cell>
                            <x-ui.table.cell class="text-xs">
                                {{ $persona->numero_trabajador ?? '---' }}
                            </x-ui.table.cell>
                            <x-ui.table.cell class="text-xs">
                                {{ $persona->ingresoPrincipal?->tipoPersonal?->nombre ?? '' }}
                            </x-ui.table.cell>
                            <x-ui.table.cell class="text-xs">
                                {{ $persona->ingresoPrincipal?->nombramiento?->nombre ?? '' }}
                            </x-ui.table.cell>
                            <x-ui.table.cell class="text-xs">
                                {{ $persona->ingresoPrincipal?->contrato?->nombre ?? '' }}
                            </x-ui.table.cell>
                            <x-ui.table.cell class="text-xs">
                                {{ $persona->perfilAcademico?->pride?->nombre ?? '---' }}
                            </x-ui.table.cell>
                            <x-ui.table.cell class="text-xs">
                                {{ $persona->perfilAcademico?->sni?->nombre ?? '---' }}
                            </x-ui.table.cell>
                            <x-ui.table.cell class="text-xs">
                                @if ($persona->ingresoPrincipal?->fecha_ingreso)
                                    @php
                                        $antiguedad = $persona->ingresoPrincipal?->fecha_ingreso->diff(now());
                                    @endphp

                                    {{-- @if ($antiguedad->y > 0)
                                    {{ $antiguedad->y }} {{ $antiguedad->y === 1 ? 'año' : 'años' }}
                                @endif

                                @if ($antiguedad->m > 0)
                                    {{ $antiguedad->m }} {{ $antiguedad->m === 1 ? 'mes' : 'meses' }}
                                @endif

                                @if ($antiguedad->y === 0 && $antiguedad->m === 0)
                                    Menos de un mes
                                @endif --}}
                                    {{ $antiguedad->y > 0 ? $antiguedad->y . ' años' : 0 }}
                                @else
                                    --
                                @endif
                            </x-ui.table.cell>
                            <x-ui.table.cell>
                                <div class="flex items-center justify-end space-x-2">
                                    @if ($persona->activo)
                                        <x-ui.badge variant="success" textSize="text-[0.650rem]">Activo</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" textSize="text-[0.650rem]">Inactivo</x-ui.badge>
                                    @endif

                                    <x-ui.badge variant="neutral" textSize="text-[0.650rem]">ID:
                                        {{ $persona->id }}</x-ui.badge>
                                </div>
                            </x-ui.table.cell>
                            <x-ui.table.cell>
                                <div class="flex items-center justify-end space-x-2">
                                    @can('personas.view')
                                        <a href="{{ route('personas.show', $persona) }}" title="Visualizar datos"
                                            class="text-xs font-medium text-zinc-500 transition hover:text-zinc-800">
                                            <x-ui.icon name="eye-open" class="w-5 h-5 text-blue-500 hover:text-blue-600" />
                                        </a>
                                    @endcan

                                    @can('personas.update')
                                        <a href="{{ route('personas.edit', $persona) }}" title="Editar datos"
                                            class="text-xs font-medium text-zinc-500 transition hover:text-zinc-800">
                                            <x-ui.icon name="edit" class="w-4 h-4 text-blue-500 hover:text-blue-600" />
                                        </a>

                                        <x-ui.switch name="persona_active_{{ $persona->id }}" :checked="$persona->activo"
                                            size="xs" :disabled="!auth()->user()->can('personas.update')" wire:click="toggleActive({{ $persona->id }})"
                                            title="{{ $persona->activo ? 'Desactvar registro' : 'Activar registro' }}" />
                                    @endcan

                                    @can('personas.delete')
                                        <button wire:click.stop="confirmDelete({{ $persona->id }})"
                                            wire:loading.attr="disabled" class="shrink-0  text-sm" title="Eliminar persona">
                                            <x-ui.icon name="trash" class="w-4 h-4 text-red-500 hover:text-red-600" />
                                        </button>
                                    @endcan
                                </div>
                            </x-ui.table.cell>
                        </x-ui.table.row>
                    @empty
                        <x-ui.table.empty colspan="8">
                            No existen usuarios registrados.
                        </x-ui.table.empty>
                    @endforelse
                    </x-ui.table.body>

            </x-ui.table>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $personas->links() }}
            </div>
        </div>
    </x-ui.panel>

    {{-- Modal global de eliminación --}}
    <x-ui.confirm-delete-modal model="deleteModal" title="Confirmar eliminación"
        message="Esta acción eliminará el registro seleccionado. Si el modelo utiliza eliminación lógica, el historial podrá conservarse internamente."
        confirmText="Eliminar persona" cancelText="Cancelar" confirmAction="deleteConfirmed"
        cancelAction="resetDeleteForm" />

</div>

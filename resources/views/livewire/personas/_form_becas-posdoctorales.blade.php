<div class="px-4 py-3">
    @if ($persona->esPosdoctorado())

        @if ($posdocBecaFormVisible)
            @include('livewire.personas._form_becas-posdoctorales-data')
        @endif

        <x-ui.table>
            <x-ui.table.head>
                <x-ui.table.row>
                    <x-ui.table.header>Beca</x-ui.table.header>
                    <x-ui.table.header>Asesor</x-ui.table.header>
                    <x-ui.table.header>Periodo</x-ui.table.header>
                    <x-ui.table.header>Estatus</x-ui.table.header>
                    <x-ui.table.header></x-ui.table.header>
                </x-ui.table.row>
            </x-ui.table.head>

            <x-ui.table.body>
                @forelse ($posdocBecas as $posdocBeca)
                    <x-ui.table.row>
                        <x-ui.table.cell class="text-xs">{{ $posdocBeca->beca?->nombre ?? '—' }}</x-ui.table.cell>
                        <x-ui.table.cell class="text-xs">{{ $posdocBeca->asesor?->fullname ?? '—' }}</x-ui.table.cell>
                        <x-ui.table.cell class="text-xs">
                            <span>{{ $posdocBeca->fecha_inicio ? $posdocBeca->fecha_inicio->format('d/m/Y') : '—' }}</span>

                            <span class="text-zinc-400"> a
                                {{ $posdocBeca->fecha_fin ? $posdocBeca->fecha_fin->format('d/m/Y') : 'Actual / sin fecha' }}</span>
                        </x-ui.table.cell>
                        <x-ui.table.cell>
                            @if ($posdocBeca->principal)
                                <x-ui.badge variant="success">Principal</x-ui.badge>
                            @endif

                            @if ($posdocBeca->activo)
                                <x-ui.badge variant="success">Activa</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">Inactiva</x-ui.badge>
                            @endif
                        </x-ui.table.cell>
                        <x-ui.table.cell>
                            <div class="flex justify-end items-center space-x-2">
                                @can('personas.manage_posdoc_becas')
                                    <button type="button" wire:click="editPosdocBeca({{ $posdocBeca->id }})"
                                        wire:loading.attr="disabled">
                                        <x-ui.icon name="edit" class="w-4 h-4 text-blue-500 hover:text-blue-600" />
                                    </button>

                                    <button type="button" wire:click="confirmDeletePosdocBeca({{ $posdocBeca->id }})"
                                        wire:loading.attr="disabled">
                                        <x-ui.icon name="trash" class="w-4 h-4 text-red-500 hover:text-red-600" />
                                    </button>
                                </div>
                            @endcan
                        </x-ui.table.cell>
                    </x-ui.table.row>
                @empty
                    <x-ui.empty-state>
                        <x-slot name="description">
                            No hay becas posdoctorales registradas para esta persona.
                        </x-slot>
                    </x-ui.empty-state>
                @endforelse
            </x-ui.table.body>
        </x-ui.table>
    @else
        <section class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50/60 p-6">
            <p class="mt-2 text-sm text-zinc-500">
                Esta sección solo está disponible cuando la persona tiene ingreso principal con tipo de personal
                posdoctoral.
            </p>
        </section>
    @endif
</div>

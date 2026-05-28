<div class="px-4 py-3">
    @if ($ingresoFormVisible)
        <div>
            <form wire:submit.prevent="saveIngreso" class="rounded-2xl border border-zinc-200 bg-white px-4 py-3">
                @include('livewire.personas._form_ingresos-data')
            </form>
        </div>
    @endif

    <div class="mt-4">
        @if ($ingresos->count() > 1)
            <x-ui.checkbox name="showAllIngresos" size="xs" :checked="$showAllIngresos"
                label="Mostrar todos los ingresos institucionales" wire:model.live="showAllIngresos" />
        @endif

        @if ($showAllIngresos)

            @forelse ($ingresos as $ingreso)
                <div class="flex justify-between px-4 py-2 rounded-xl border mt-2">
                    <div class="">
                        <div class="font-semibold text-sm">{{ $ingreso->tipoPersonal?->nombre ?? '' }}</div>
                        <div class="text-xs text-zinc-400">{{ $ingreso->nombramiento?->nombre ?? '' }}</div>
                        <div class="text-xs text-zinc-400">{{ $ingreso->contrato?->nombre ?? '' }}</div>
                    </div>
                    <div class="flex justify-between gap-4">
                        <div class="text-xs text-zinc-400" title="Fecha de ingreso">
                            {{ $ingreso->fecha_ingreso?->format('d-M-Y') ?? '-- -- ----' }}
                        </div>
                        <div class="text-xs text-zinc-400" title="Fecha de baja">
                            {{ $ingreso->fecha_baja?->format('d-M-Y') ?? '-- -- ----' }}
                        </div>
                    </div>

                    <div>
                        @if ($ingreso->principal)
                            <x-ui.badge variant="success" textSize="text-xs">
                                Principal
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral" textSize="text-xs">
                                Histórico
                            </x-ui.badge>
                        @endif
                        @if ($ingreso->activo)
                            <x-ui.badge variant="success" textSize="text-xs" class="ml-2">
                                Activo
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral" textSize="text-xs" class="ml-2">
                                Inactivo
                            </x-ui.badge>
                        @endif
                    </div>

                    <div class="flex justify-end space-x-2 align-middle">

                        <div>
                            <button type="button" wire:click="editIngreso({{ $ingreso->id }})"
                                wire:loading.attr="disabled" title="Editar datos"
                                class="text-xs font-medium text-zinc-500 transition hover:text-zinc-800">
                                <x-ui.icon name="edit" class="w-4 h-4 text-blue-500 hover:text-blue-600" />
                            </button>
                        </div>

                        <x-ui.switch name="ingreso_active_{{ $ingreso->id }}" :checked="$ingreso->activo" size="xs"
                            wire:click="setIngresoPrincipal({{ $ingreso->id }})"
                            title="{{ $ingreso->activo ? 'Quitar como principal' : 'Hacer principal' }}" />

                        <div>
                            <button wire:click.stop="confirmDeleteIngreso({{ $ingreso->id }})"
                                wire:loading.attr="disabled" class="shrink-0  text-sm" title="Eliminar ingreso">
                                <x-ui.icon name="trash" class="w-4 h-4 text-red-500 hover:text-red-600" />
                            </button>
                        </div>

                    </div>
                </div>
            @empty
                <x-ui.empty-state title="No hay ingresos registrados"
                    description=" No se han registrado ingresos institucionales para esta persona. Haz clic en el botón
            <<Agregar ingreso>> para registrar su historial de ingresos, tipo de personal, vigencia y estatus
            institucional.">
                </x-ui.empty-state>
            @endforelse
        @endif
    </div>
</div>

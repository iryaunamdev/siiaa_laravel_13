<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-sm text-gray-500">Folio</p>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $solicitud->folioDisplay() }}
                </p>
            </div>

            <span
                class="inline-flex w-fit rounded-full border px-2.5 py-1 text-xs font-medium {{ $solicitud->estatusBadgeClass() }}">
                {{ $solicitud->estatusNombre() }}
            </span>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <p class="text-sm text-gray-500">Tipo de solicitud</p>
                <p class="font-medium text-gray-900">
                    {{ $solicitud->tipoSolicitud?->nombre ?? 'Sin tipo' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Solicitante</p>
                <p class="font-medium text-gray-900">
                    {{ $solicitud->owner?->nombre_completo ?? ($solicitud->owner?->nombre ?? 'Sin propietario') }}
                </p>
            </div>
        </div>

        @if ($solicitud->informacion_adicional)
            <div class="mt-5 border-t border-gray-100 pt-4">
                <p class="text-sm text-gray-500">Información adicional</p>
                <p class="mt-1 whitespace-pre-line text-sm text-gray-800">
                    {{ $solicitud->informacion_adicional }}
                </p>
            </div>
        @endif
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <x-ui.textarea
            name="observaciones_sacad"
            label="Observaciones SACAD / CI"
            wire:model="observaciones_sacad"
            rows="6"
            placeholder="Capture observaciones de revisión. Si rechaza la solicitud, este campo es obligatorio."
            help="Estas observaciones se guardan en el expediente y podrán reutilizarse en el flujo de Consejo Interno." />
    </section>

    <div class="flex items-center justify-end gap-3">
        <x-ui.button variant="secondary" href="{{ route('solicitudes.show', $solicitud) }}">
            Cancelar
        </x-ui.button>

        @can('reject', $solicitud)
            <x-ui.button
                variant="danger"
                type="button"
                wire:click="confirmarRechazar"
                wire:loading.attr="disabled">
                Rechazar
            </x-ui.button>
        @endcan

        @can('approve', $solicitud)
            <x-ui.button
                type="button"
                wire:click="confirmarAprobar"
                wire:loading.attr="disabled">
                Aprobar
            </x-ui.button>
        @endcan
    </div>

    <x-ui.modal wire:model="confirmRejectModal" max-width="md" close-action="cancelarRechazar">
        <x-slot:title>
            Confirmar rechazo
        </x-slot:title>

        <div class="space-y-3 p-4">
            <p class="text-sm text-gray-700">
                La solicitud será rechazada y se conservarán las observaciones capturadas en el expediente.
            </p>
            <p class="text-sm font-medium text-red-700">
                Esta acción cambiará el estatus de la solicitud.
            </p>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" type="button" wire:click="cancelarRechazar">
                Cancelar
            </x-ui.button>

            <x-ui.button variant="danger" type="button" wire:click="rechazar" wire:loading.attr="disabled">
                Rechazar solicitud
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    <x-ui.modal wire:model="confirmApproveModal" max-width="md" close-action="cancelarAprobar">
        <x-slot:title>
            Confirmar aprobación
        </x-slot:title>

        <div class="space-y-3 p-4">
            <p class="text-sm text-gray-700">
                La solicitud será aprobada por CI/SACAD y avanzará al siguiente estatus institucional.
            </p>
            <p class="text-sm font-medium text-emerald-700">
                Confirme únicamente si la revisión ya fue validada.
            </p>
        </div>

        <x-slot:footer>
            <x-ui.button variant="secondary" type="button" wire:click="cancelarAprobar">
                Cancelar
            </x-ui.button>

            <x-ui.button type="button" wire:click="aprobar" wire:loading.attr="disabled">
                Aprobar solicitud
            </x-ui.button>
        </x-slot:footer>
    </x-ui.modal>
</div>

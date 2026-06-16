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
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Observaciones SACAD
        </label>
        <textarea wire:model="observaciones_sacad" rows="5"
            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Observaciones de revisión."></textarea>
        @error('observaciones_sacad')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Motivo de rechazo
        </label>
        <textarea wire:model="observaciones_sacad" rows="4"
            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Requerido únicamente si se rechaza la solicitud."></textarea>
        @error('observaciones_sacad')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </section>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('solicitudes.show', $solicitud) }}"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Cancelar
        </a>

        @can('reject', $solicitud)
            <button type="button" wire:click="rechazar" wire:confirm="La solicitud será rechazada. ¿Desea continuar?"
                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700"
                wire:loading.attr="disabled">
                Rechazar
            </button>
        @endcan

        @can('approve', $solicitud)
            <button type="button" wire:click="aprobar" wire:confirm="La solicitud será aprobada. ¿Desea continuar?"
                class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700"
                wire:loading.attr="disabled">
                Aprobar
            </button>
        @endcan
    </div>
</div>

<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div class="grid flex-1 grid-cols-1 gap-3 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Buscar
                    </label>
                    <input type="text" wire:model.live.debounce.400ms="search"
                        placeholder="Folio o información adicional"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Estatus
                    </label>
                    <select wire:model.live="estatus_id"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos</option>

                        @foreach ($c_estatus as $estatus)
                            <option value="{{ $estatus->id }}">
                                {{ $estatus->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Tipo
                    </label>
                    <select wire:model.live="tipo_solicitud_id"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos</option>

                        @foreach ($c_tipos_solicitud as $tipo)
                            <option value="{{ $tipo->id }}">
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @can('create', App\Models\Solicitudes\Solicitud::class)
                <a href="{{ route('solicitudes.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    Nueva solicitud
                </a>
            @endcan
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Folio</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Tipo</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Solicitante</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Estatus</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Enviada</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($solicitudes as $solicitud)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $solicitud->folioDisplay() }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $solicitud->tipoSolicitud?->nombre ?? 'Sin tipo' }}
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $solicitud->owner?->nombre_completo ?? ($solicitud->owner?->nombre ?? 'Sin propietario') }}
                            </td>

                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium {{ $solicitud->estatusBadgeClass() }}">
                                    {{ $solicitud->estatusNombre() }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ $solicitud->submitted_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    @can('view', $solicitud)
                                        <a href="{{ route('solicitudes.show', $solicitud) }}"
                                            class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                            Ver
                                        </a>
                                    @endcan

                                    @can('update', $solicitud)
                                        <a href="{{ route('solicitudes.edit', $solicitud) }}"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                            Editar
                                        </a>
                                    @endcan

                                    @can('review', $solicitud)
                                        <a href="{{ route('solicitudes.review', $solicitud) }}"
                                            class="text-sm font-medium text-emerald-600 hover:text-emerald-800">
                                            Revisar
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">
                                No hay solicitudes registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-4 py-3">
            {{ $solicitudes->links() }}
        </div>
    </section>
</div>

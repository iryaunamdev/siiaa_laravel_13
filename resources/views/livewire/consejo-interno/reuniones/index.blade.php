<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                Reuniones del Consejo Interno
            </h1>

            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Administra reuniones, participantes, solicitudes, otros puntos y documentos del Consejo Interno.
            </p>
        </div>

        @can('ci.reuniones_manage')
            <a href="{{ route('consejo-interno.reuniones.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                Nueva reunión
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div
            class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="grid gap-4 md:grid-cols-[1fr_220px_auto]">
            <div>
                <label for="search" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Buscar
                </label>

                <input id="search" type="search" wire:model.live.debounce.400ms="search"
                    placeholder="Título, tipo de reunión o modalidad"
                    class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
            </div>

            <div>
                <label for="estatus" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                    Estatus
                </label>

                <select id="estatus" wire:model.live="estatus"
                    class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                    <option value="">Todos</option>

                    @foreach ($estatusOptions as $option)
                        <option value="{{ $option }}">
                            {{ str_replace('_', ' ', $option) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="button" wire:click="limpiarFiltros"
                    class="inline-flex w-full items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    Limpiar
                </button>
            </div>
        </div>
    </div>

    <div
        class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800/70">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Fecha
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Reunión
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Tipo
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Modalidad
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Estatus
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Resumen
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($reuniones as $reunion)
                        <tr wire:key="reunion-{{ $reunion->id }}">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $reunion->fecha?->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $reunion->titulo }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $reunion->tipo_reunion }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $reunion->modalidad }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3">
                                <span
                                    class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ str_replace('_', ' ', $reunion->estatus) }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2 text-xs text-zinc-600 dark:text-zinc-400">
                                    <span class="rounded-full bg-zinc-100 px-2 py-1 dark:bg-zinc-800">
                                        {{ $reunion->participantes_count }} participantes
                                    </span>

                                    <span class="rounded-full bg-zinc-100 px-2 py-1 dark:bg-zinc-800">
                                        {{ $reunion->puntos_solicitud_count }} solicitudes
                                    </span>

                                    <span class="rounded-full bg-zinc-100 px-2 py-1 dark:bg-zinc-800">
                                        {{ $reunion->puntos_otros_count }} otros puntos
                                    </span>

                                    <span class="rounded-full bg-zinc-100 px-2 py-1 dark:bg-zinc-800">
                                        {{ $reunion->documentos_count }} documentos
                                    </span>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('consejo-interno.reuniones.show', $reunion) }}"
                                        class="font-medium text-zinc-700 hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white">
                                        Ver
                                    </a>

                                    @can('ci.reuniones_manage')
                                        <a href="{{ route('consejo-interno.reuniones.edit', $reunion) }}"
                                            class="font-medium text-zinc-700 hover:text-zinc-950 dark:text-zinc-300 dark:hover:text-white">
                                            Editar
                                        </a>

                                        <button type="button" wire:click="eliminar({{ $reunion->id }})"
                                            wire:confirm="Esta acción eliminará la reunión y sus datos relacionados según las reglas de base de datos. ¿Deseas continuar?"
                                            class="font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            Eliminar
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                No se encontraron reuniones con los filtros actuales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
            {{ $reuniones->links() }}
        </div>
    </div>
</div>

<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                Actas del Consejo Interno
            </h1>

            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Consulta y administración de las actas del Consejo Interno.
            </p>
        </div>

        @can('create', \App\Models\ConsejoInterno\CiActa::class)
            {{--
                El botón "Nueva acta" se agrega en 6C,
                cuando exista la ruta actas.create.
            --}}
        @endcan
    </div>

    @if (session('status'))
        <div
            class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300"
        >
            {{ session('status') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div
        class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
    >
        <div class="grid gap-4 md:grid-cols-4">

            <div class="md:col-span-2">
                <label
                    for="search"
                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300"
                >
                    Buscar
                </label>

                <input
                    id="search"
                    type="search"
                    wire:model.live.debounce.400ms="search"
                    placeholder="Número, título o contenido"
                    class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                >
            </div>

            <div>
                <label
                    for="estatus"
                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300"
                >
                    Estatus
                </label>

                <select
                    id="estatus"
                    wire:model.live="estatus"
                    class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                >
                    <option value="">Todos</option>

                    @foreach ($estatusOptions as $option)
                        <option value="{{ $option }}">
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label
                    for="year"
                    class="block text-sm font-medium text-zinc-700 dark:text-zinc-300"
                >
                    Año
                </label>

                <select
                    id="year"
                    wire:model.live="year"
                    class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                >
                    <option value="">Todos</option>

                    @foreach ($years as $yearOption)
                        <option value="{{ $yearOption }}">
                            {{ $yearOption }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        @if ($search !== '' || $estatus !== '' || $year !== '')
            <div class="mt-4 flex justify-end">
                <button
                    type="button"
                    wire:click="limpiarFiltros"
                    class="text-sm font-medium text-zinc-600 hover:text-zinc-950 dark:text-zinc-400 dark:hover:text-white"
                >
                    Limpiar filtros
                </button>
            </div>
        @endif
    </div>

    {{-- Listado --}}
    <div
        class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
    >
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">

                <thead class="bg-zinc-50 dark:bg-zinc-800/70">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Acta
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Fecha
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Reunión
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Estatus
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            Acciones
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($actas as $acta)
                        <tr wire:key="acta-{{ $acta->id }}">

                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $acta->numero_acta }}
                                </div>

                                <div class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $acta->titulo }}
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $acta->fecha?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                @if ($acta->reunion)
                                    {{ $acta->reunion->titulo }}
                                @else
                                    <span class="text-zinc-400">
                                        Independiente
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex rounded-full bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300"
                                >
                                    {{ $acta->estatus }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">

                                    {{--
                                        Ver / Editar se agregan en 6C
                                        cuando existan esas rutas.
                                    --}}

                                    @can('delete', $acta)
                                        <button
                                            type="button"
                                            wire:click="eliminar({{ $acta->id }})"
                                            wire:confirm="¿Deseas eliminar permanentemente esta acta?"
                                            class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                        >
                                            Eliminar
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td
                                colspan="5"
                                class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400"
                            >
                                No se encontraron actas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($actas->hasPages())
            <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                {{ $actas->links() }}
            </div>
        @endif
    </div>
</div>

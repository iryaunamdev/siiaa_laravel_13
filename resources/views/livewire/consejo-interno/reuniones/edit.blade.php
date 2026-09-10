<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $reunion ? 'Editar reunión' : 'Nueva reunión' }}
            </h1>

            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                {{ $reunion
                    ? 'Administra los datos base y participantes de la reunión.'
                    : 'Captura los datos base para crear una nueva reunión del Consejo Interno.' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($reunion)
                <a href="{{ route('consejo-interno.reuniones.show', $reunion) }}"
                    class="inline-flex items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    Ver expediente
                </a>
            @endif

            <a href="{{ route('consejo-interno.reuniones.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                Volver al listado
            </a>
        </div>
    </div>

    @if (session('status'))
        <div
            class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="guardar" class="space-y-6">
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                Datos generales
            </h2>

            <div class="mt-5 grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="titulo" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Título
                    </label>

                    <input id="titulo" type="text" wire:model.blur="titulo"
                        class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">

                    @error('titulo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Fecha
                    </label>

                    <input id="fecha" type="date" wire:model.blur="fecha"
                        class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">

                    @error('fecha')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="estatus" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Estatus
                    </label>

                    <select id="estatus" wire:model.blur="estatus"
                        class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                        @foreach ($estatusOptions as $option)
                            <option value="{{ $option }}">
                                {{ str_replace('_', ' ', $option) }}
                            </option>
                        @endforeach
                    </select>

                    @error('estatus')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tipo_reunion" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Tipo de reunión
                    </label>

                    <input id="tipo_reunion" type="text" wire:model.blur="tipo_reunion"
                        class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">

                    @error('tipo_reunion')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="modalidad" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Modalidad
                    </label>

                    <input id="modalidad" type="text" wire:model.blur="modalidad"
                        class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">

                    @error('modalidad')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('consejo-interno.reuniones.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    Cancelar
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                    Guardar reunión
                </button>
            </div>
        </div>
    </form>

    @if ($reunion)
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                    Participantes
                </h2>

                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    Administra las identidades institucionales que participan en esta reunión.
                </p>
            </div>

            <form wire:submit="agregarParticipante"
                class="mt-5 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950/60">
                <div class="grid gap-4 md:grid-cols-[1fr_1fr_auto]">
                    <div>
                        <label for="participanteSearch"
                            class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Buscar identidad
                        </label>

                        <input id="participanteSearch" type="search"
                            wire:model.live.debounce.400ms="participanteSearch" placeholder="Correo o ID de identidad"
                            class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                    </div>

                    <div>
                        <label for="nuevoParticipanteId"
                            class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Participante
                        </label>

                        <select id="nuevoParticipanteId" wire:model="nuevoParticipanteId"
                            class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                            <option value="">Selecciona una identidad</option>

                            @foreach ($opcionesParticipantes as $identity)
                                <option value="{{ $identity->id }}">
                                    {{ $identity->fullname() ?? 'Sin nombre registrado' }}
                                    — {{ $identity->emailResolved() ?? ($identity->email ?? 'Sin correo') }}
                                    — ID {{ $identity->id }}
                                </option>
                            @endforeach
                        </select>

                        @error('nuevoParticipanteId')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                            Agregar
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-5 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/70">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Participante
                            </th>

                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Correo
                            </th>

                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Tipo
                            </th>

                            <th
                                class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($reunion->participantes as $participante)
                            <tr wire:key="participante-{{ $participante->id }}">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $participante->identidad?->fullname() ?? 'Sin nombre registrado' }}
                                    </div>

                                    <div class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        Identity ID: {{ $participante->identity_link_id }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $participante->identidad?->emailResolved() ?? 'Sin correo' }}
                                </td>

                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $participante->identidad?->identity_type ?? 'Sin tipo' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                    <button type="button"
                                        wire:click="quitarParticipante({{ $participante->identity_link_id }})"
                                        wire:confirm="¿Deseas quitar a esta identidad como participante de la reunión?"
                                        class="font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        Quitar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    Esta reunión todavía no tiene participantes registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Solicitudes -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                    Solicitudes
                </h2>

                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    Agrega solicitudes enviadas al Consejo Interno como puntos de esta reunión.
                </p>
            </div>

            <form wire:submit="agregarSolicitud"
                class="mt-5 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950/60">
                <div class="grid gap-4 md:grid-cols-[1fr_1fr_auto]">
                    <div>
                        <label for="solicitudSearch"
                            class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Buscar solicitud
                        </label>

                        <input id="solicitudSearch" type="search" wire:model.live.debounce.400ms="solicitudSearch"
                            placeholder="Folio, evento, institución o ID"
                            class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                    </div>

                    <div>
                        <label for="nuevaSolicitudId"
                            class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Solicitud
                        </label>

                        <select id="nuevaSolicitudId" wire:model="nuevaSolicitudId"
                            class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                            <option value="">Selecciona una solicitud</option>

                            @foreach ($opcionesSolicitudes as $solicitud)
                                <option value="{{ $solicitud->id }}">
                                    {{ $solicitud->folio ?? 'Sin folio' }}
                                    — {{ $solicitud->tipoSolicitud?->nombre ?? 'Sin tipo' }}
                                    — {{ $solicitud->owner?->fullname() ?? 'Sin solicitante' }}
                                    — ID {{ $solicitud->id }}
                                </option>
                            @endforeach
                        </select>

                        @error('nuevaSolicitudId')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                            Agregar
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-5 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/70">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Solicitud
                            </th>

                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Solicitante
                            </th>

                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Estatus
                            </th>

                            <th
                                class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($reunion->puntosSolicitud as $punto)
                            <tr wire:key="solicitud-punto-{{ $punto->id }}">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $punto->solicitud?->folio ?? 'Sin folio' }}
                                    </div>

                                    <div class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $punto->solicitud?->tipoSolicitud?->nombre ?? 'Sin tipo' }}
                                        @if ($punto->solicitud?->nombre_evento)
                                            · {{ $punto->solicitud->nombre_evento }}
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $punto->solicitud?->owner?->fullname() ?? 'Sin solicitante' }}
                                </td>

                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $punto->solicitud?->estatus?->nombre ?? ($punto->solicitud?->estatusClave() ?? 'Sin estatus') }}
                                </td>

                                <td class="px-4 py-3 text-right text-sm">
                                    <div class="flex flex-col items-end gap-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <select wire:model="resoluciones.{{ $punto->id }}"
                                                class="rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                                                <option value="">Resolución</option>

                                                @foreach (\App\Support\ConsejoInterno\ConsejoInternoCatalogos::resolucionesFinales() as $resolucion)
                                                    <option value="{{ $resolucion }}">
                                                        {{ $resolucion }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button type="button" wire:click="resolverPunto({{ $punto->id }})"
                                                class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                                                Resolver
                                            </button>
                                        </div>

                                        @error("resoluciones.{$punto->id}")
                                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror

                                        @if ($punto->resolucion)
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                Resuelto como <span
                                                    class="font-medium">{{ $punto->resolucion }}</span>
                                                @if ($punto->resolved_at)
                                                    el {{ $punto->resolved_at->format('d/m/Y H:i') }}
                                                @endif

                                                @if ($punto->resolved_by)
                                                    por
                                                    {{ $punto->resolvedBy?->fullname() ?? 'identidad no disponible' }}
                                                @else
                                                    por ADMIN
                                                @endif
                                            </p>
                                        @endif

                                        <button type="button"
                                            wire:click="quitarSolicitud({{ $punto->solicitud_id }})"
                                            wire:confirm="¿Deseas retirar esta solicitud de la reunión?"
                                            class="font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            Quitar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    Esta reunión todavía no tiene solicitudes agregadas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Otros Puntos -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                    Otros puntos
                </h2>

                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    Agrega puntos libres del orden del día que no correspondan a una solicitud.
                </p>
            </div>

            <form wire:submit="agregarOtroPunto"
                class="mt-5 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950/60">
                <div class="space-y-4">
                    <div>
                        <label for="otroPuntoTitulo"
                            class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Título del punto
                        </label>

                        <input id="otroPuntoTitulo" type="text" wire:model.blur="otroPuntoTitulo"
                            class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">

                        @error('otroPuntoTitulo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="otroPuntoDescripcion"
                            class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Descripción
                        </label>

                        <textarea id="otroPuntoDescripcion" wire:model.blur="otroPuntoDescripcion" rows="4"
                            class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"></textarea>

                        @error('otroPuntoDescripcion')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                            Agregar punto
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-5 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/70">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Orden
                            </th>

                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Punto
                            </th>

                            <th
                                class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($reunion->puntosOtros as $punto)
                            <tr wire:key="otro-punto-{{ $punto->id }}">
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $punto->orden }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $punto->titulo }}
                                    </div>

                                    <div class="mt-1 whitespace-pre-line text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $punto->descripcion }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-right text-sm">
                                    <div class="flex flex-col items-end gap-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <select wire:model="resoluciones.{{ $punto->id }}"
                                                class="rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                                                <option value="">Resolución</option>

                                                @foreach (\App\Support\ConsejoInterno\ConsejoInternoCatalogos::resolucionesFinales() as $resolucion)
                                                    <option value="{{ $resolucion }}">
                                                        {{ $resolucion }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            <button type="button" wire:click="resolverPunto({{ $punto->id }})"
                                                class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                                                Resolver
                                            </button>
                                        </div>

                                        @error("resoluciones.{$punto->id}")
                                            <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror

                                        @if ($punto->resolucion)
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                Resuelto como <span
                                                    class="font-medium">{{ $punto->resolucion }}</span>
                                                @if ($punto->resolved_at)
                                                    el {{ $punto->resolved_at->format('d/m/Y H:i') }}
                                                @endif

                                                @if ($punto->resolved_by)
                                                    por
                                                    {{ $punto->resolvedBy?->fullname() ?? 'identidad no disponible' }}
                                                @else
                                                    por ADMIN
                                                @endif
                                            </p>
                                        @endif

                                        <button type="button" wire:click="quitarOtroPunto({{ $punto->id }})"
                                            wire:confirm="¿Deseas retirar este punto de la reunión?"
                                            class="font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            Quitar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3"
                                    class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    Esta reunión todavía no tiene otros puntos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Documentos -->
        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div>
                <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                    Documentos adicionales
                </h2>

                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                    Adjunta documentos de trabajo asociados a esta reunión.
                </p>
            </div>

            <form wire:submit="subirDocumento"
                class="mt-5 rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950/60">
                <div class="grid gap-4 md:grid-cols-[1fr_auto]">
                    <div>
                        <label for="nuevoDocumento"
                            class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">
                            Archivo
                        </label>

                        <input id="nuevoDocumento" type="file" wire:model="nuevoDocumento"
                            class="mt-1 block w-full rounded-lg border border-zinc-300 bg-white text-sm text-zinc-700 shadow-sm file:mr-4 file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-zinc-700 hover:file:bg-zinc-200 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-200">

                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                            Tamaño máximo: 10 MB.
                        </p>

                        @error('nuevoDocumento')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <div wire:loading wire:target="nuevoDocumento"
                            class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                            Cargando archivo...
                        </div>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" wire:loading.attr="disabled"
                            wire:target="nuevoDocumento,subirDocumento"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                            Subir documento
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-5 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/70">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Documento
                            </th>

                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Cargado por
                            </th>

                            <th
                                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Tamaño
                            </th>

                            <th
                                class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($reunion->documentos as $documento)
                            <tr wire:key="documento-{{ $documento->id }}">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $documento->original_name }}
                                    </div>

                                    <div class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ $documento->mime_type ?? 'Tipo no identificado' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $documento->uploadedBy?->fullname() ?? 'Sin identidad registrada' }}
                                </td>

                                <td class="px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $documento->size ? number_format($documento->size / 1024, 1) . ' KB' : 'Sin dato' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                    <button type="button" wire:click="eliminarDocumento({{ $documento->id }})"
                                        wire:confirm="¿Deseas eliminar este documento de la reunión?"
                                        class="font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4"
                                    class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                    Esta reunión todavía no tiene documentos adicionales.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div
            class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-900/60">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                Participantes
            </h2>

            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                Primero guarda la reunión. Después podrás agregar participantes, solicitudes,
                otros puntos y documentos.
            </p>
        </div>
    @endif
</div>

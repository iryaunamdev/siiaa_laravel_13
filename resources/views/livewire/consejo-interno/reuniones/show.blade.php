<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                    {{ $reunion->titulo }}
                </h1>

                @if ($reunion->estaConcluida())
                    <div
                        class="rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-600 dark:border-zinc-700 dark:bg-zinc-950/60 dark:text-zinc-400">
                        Reunión concluida

                        @if ($reunion->concluida_at)
                            el {{ $reunion->concluida_at->format('d/m/Y H:i') }}
                        @endif

                        @if ($reunion->concluida_by)
                            por {{ $reunion->concluidaPor?->fullname() ?? 'Identidad no disponible' }}
                        @else
                            por ADMIN
                        @endif
                    </div>
                @else
                    <span
                        class="inline-flex rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                        {{ str_replace('_', ' ', $reunion->estatus) }}
                    </span>
                @endif
            </div>


            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Expediente operativo de la reunión del Consejo Interno.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('consejo-interno.reuniones.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                Volver
            </a>

            @can('update', $reunion)
                <a href="{{ route('consejo-interno.reuniones.edit', $reunion) }}"
                    class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                    Editar reunión
                </a>
            @endcan
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                Participantes
            </p>

            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $reunion->participantes_count }}
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                Solicitudes
            </p>

            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $reunion->puntos_solicitud_count }}
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                Otros puntos
            </p>

            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $reunion->puntos_otros_count }}
            </p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                Documentos
            </p>

            <p class="mt-2 text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $reunion->documentos_count }}
            </p>
        </div>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
            Datos generales
        </h2>

        <dl class="mt-5 grid gap-5 md:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    Fecha
                </dt>

                <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                    {{ $reunion->fecha?->format('d/m/Y') ?? 'Sin fecha' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    Estatus
                </dt>

                <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                    {{ str_replace('_', ' ', $reunion->estatus) }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    Tipo de reunión
                </dt>

                <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                    {{ $reunion->tipo_reunion }}
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                    Modalidad
                </dt>

                <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                    {{ $reunion->modalidad }}
                </dd>
            </div>
        </dl>
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
            Participantes
        </h2>

        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
            Identidades institucionales registradas como participantes de esta reunión.
        </p>

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
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($reunion->participantes as $participante)
                        <tr>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                Esta reunión todavía no tiene participantes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!--Solicitudes -->
    <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
            Solicitudes
        </h2>

        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
            Solicitudes integradas como puntos de esta reunión.
        </p>

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

                        <th></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($reunion->puntosSolicitud as $punto)
                        <tr>
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

                                @if ($punto->resolucion)
                                    <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                                        Resolución CI:
                                        <span class="font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ $punto->resolucion }}
                                        </span>

                                        @if ($punto->resolved_at)
                                            · {{ $punto->resolved_at->format('d/m/Y H:i') }}
                                        @endif

                                        @if ($punto->resolved_by)
                                            · {{ $punto->resolvedBy?->fullname() ?? 'identidad no disponible' }}
                                        @else
                                            · ADMIN
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($punto->evaluaciones->isNotEmpty())
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach ($punto->evaluaciones as $evaluacion)
                                            <span
                                                class="inline-flex rounded-full bg-zinc-100 px-2 py-1 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                                {{ $evaluacion->identity_link_id ? $evaluacion->identidad?->fullname() ?? 'Sin identidad' : 'ADMIN' }}:
                                                {{ $evaluacion->evaluacion?->clave ?? 'Sin evaluación' }}
                                            </span>
                                            @if ($evaluacion->comentarios)
                                                <span
                                                    class="inline-flex rounded-full bg-zinc-100 px-2 py-1 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                                    {{ $evaluacion->identity_link_id ? $evaluacion->identidad?->fullname() ?? 'Sin identidad' : 'ADMIN' }}:
                                                    {{ $evaluacion->evaluacion?->clave ?? 'Sin evaluación' }}
                                                    · {{ $evaluacion->comentarios }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                @can('evaluate', $punto)
                                    <form wire:submit="evaluar({{ $punto->id }})"
                                        class="mt-4 rounded-lg border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-950/60">
                                        <div class="grid gap-3 md:grid-cols-[180px_1fr_auto]">
                                            <div>
                                                <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400">
                                                    Evaluación
                                                </label>

                                                <select wire:model="evaluaciones.{{ $punto->id }}"
                                                    class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                                                    <option value="">Selecciona</option>

                                                    @foreach ($evaluacionOptions as $option)
                                                        <option value="{{ $option }}">
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                @error("evaluaciones.{$punto->id}")
                                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                    </p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium text-zinc-600 dark:text-zinc-400">
                                                    Comentario
                                                </label>

                                                <input type="text" wire:model.blur="comentarios.{{ $punto->id }}"
                                                    placeholder="Opcional"
                                                    class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">

                                                @error("comentarios.{$punto->id}")
                                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}
                                                    </p>
                                                @enderror
                                            </div>

                                            <div class="flex items-end">
                                                <button type="submit"
                                                    class="inline-flex w-full items-center justify-center rounded-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white">
                                                    Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
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
        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
            Otros puntos
        </h2>

        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
            Puntos libres integrados al orden del día de esta reunión.
        </p>

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
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($reunion->puntosOtros as $punto)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $punto->orden }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $punto->titulo }}
                                </div>

                                <div class="mt-1 whitespace-pre-line text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $punto->descripcion }}

                                    @if ($punto->resolucion)
                                        <div
                                            class="mt-3 rounded-lg border border-zinc-200 bg-zinc-50 px-3 py-2 text-xs text-zinc-600 dark:border-zinc-700 dark:bg-zinc-950/60 dark:text-zinc-400">
                                            Resolución CI:
                                            <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                                {{ $punto->resolucion }}
                                            </span>

                                            @if ($punto->resolved_at)
                                                · {{ $punto->resolved_at->format('d/m/Y H:i') }}
                                            @endif

                                            @if ($punto->resolved_by)
                                                · {{ $punto->resolvedBy?->fullname() ?? 'identidad no disponible' }}
                                            @else
                                                · ADMIN
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
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
        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
            Documentos adicionales
        </h2>

        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
            Documentos de trabajo asociados a esta reunión.
        </p>

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
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($reunion->documentos as $documento)
                        <tr>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                Esta reunión todavía no tiene documentos adicionales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div
        class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-6 dark:border-zinc-700 dark:bg-zinc-900/60">
        <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
            Secciones pendientes
        </h2>

        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            En los siguientes subbloques se agregarán solicitudes, otros puntos,
            documentos, evaluaciones y resoluciones.
        </p>
    </div>
</div>

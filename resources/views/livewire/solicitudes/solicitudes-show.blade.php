<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="space-y-1">
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
                <p class="text-sm text-gray-500">Motivo</p>
                <p class="font-medium text-gray-900">
                    {{ $solicitud->motivo?->nombre ?? 'Sin motivo' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Solicitante</p>
                <p class="font-medium text-gray-900">
                    {{ $solicitud->owner?->nombre_completo ?? ($solicitud->owner?->nombre ?? 'Sin propietario') }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Fecha de envío</p>
                <p class="font-medium text-gray-900">
                    {{ $solicitud->submitted_at?->format('d/m/Y H:i') ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Fecha de inicio</p>
                <p class="font-medium text-gray-900">
                    {{ $solicitud->fecha_inicio?->format('d/m/Y') ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Fecha de fin</p>
                <p class="font-medium text-gray-900">
                    {{ $solicitud->fecha_fin?->format('d/m/Y') ?? '—' }}
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
        <h3 class="text-sm font-semibold text-gray-900">Visitante principal</h3>

        @if ($solicitud->visitante)
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <p class="text-sm text-gray-500">Nombre</p>
                    <p class="font-medium text-gray-900">
                        {{ $solicitud->visitante->nombre ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Institución</p>
                    <p class="font-medium text-gray-900">
                        {{ $solicitud->visitante->institucion ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Correo</p>
                    <p class="font-medium text-gray-900">
                        {{ $solicitud->visitante->correo ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Teléfono</p>
                    <p class="font-medium text-gray-900">
                        {{ $solicitud->visitante->telefono ?? '—' }}
                    </p>
                </div>
            </div>
        @else
            <p class="mt-3 text-sm text-gray-500">
                Esta solicitud no tiene visitante registrado.
            </p>
        @endif
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-900">Requerimientos</h3>

        @if ($solicitud->requerimientos->isNotEmpty())
            <ul class="mt-3 space-y-2">
                @foreach ($solicitud->requerimientos as $requerimiento)
                    <li class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                        {{ $requerimiento->nombre() }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-3 text-sm text-gray-500">
                Sin requerimientos registrados.
            </p>
        @endif
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-900">Documentos</h3>

        @if ($solicitud->documentos->isNotEmpty())
            <ul class="mt-3 divide-y divide-gray-100">
                @foreach ($solicitud->documentos as $documento)
                    <li class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $documento->nombreDisplay() }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $documento->sizeDisplay() }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-3 text-sm text-gray-500">
                Sin documentos adjuntos.
            </p>
        @endif
    </section>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('solicitudes.index') }}"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Volver
        </a>

        @can('update', $solicitud)
            <a href="{{ route('solicitudes.edit', $solicitud) }}"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                Editar
            </a>
        @endcan

        @can('review', $solicitud)
            <a href="{{ route('solicitudes.review', $solicitud) }}"
                class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700">
                Revisar
            </a>
        @endcan
    </div>
</div>

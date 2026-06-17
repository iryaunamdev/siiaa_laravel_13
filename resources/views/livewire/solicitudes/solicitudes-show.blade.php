<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
                <p class="text-sm text-gray-500">Expediente de solicitud</p>
                <div class="mt-1 flex flex-wrap items-center gap-3">
                    <h2 class="text-xl font-semibold text-gray-900">
                        {{ $solicitud->folioDisplay() }}
                    </h2>

                    <span class="inline-flex w-fit rounded-full border px-2.5 py-1 text-xs font-medium {{ $solicitud->estatusBadgeClass() }}">
                        {{ $solicitud->estatusNombre() }}
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-ui.button variant="secondary" href="{{ route('solicitudes.index') }}">
                    Volver
                </x-ui.button>

                @can('update', $solicitud)
                    <x-ui.button href="{{ route('solicitudes.edit', $solicitud) }}">
                        Editar
                    </x-ui.button>
                @endcan
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-900">Datos generales</h3>

        <dl class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-sm text-gray-500">Tipo de solicitud</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->tipoSolicitud?->nombre ?? 'Sin tipo' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Motivo</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->motivo?->nombre ?? 'Sin motivo' }}</dd>
            </div>

            @if ($solicitud->motivo_otro)
                <div>
                    <dt class="text-sm text-gray-500">Motivo otro</dt>
                    <dd class="font-medium text-gray-900">{{ $solicitud->motivo_otro }}</dd>
                </div>
            @endif

            <div>
                <dt class="text-sm text-gray-500">Solicitante</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->ownerNombre() }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">País</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->pais?->nombre ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Periodo</dt>
                <dd class="font-medium text-gray-900">
                    {{ $solicitud->fecha_inicio?->format('d/m/Y') ?? '—' }}
                    <span class="text-gray-400">a</span>
                    {{ $solicitud->fecha_fin?->format('d/m/Y') ?? '—' }}
                </dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Fecha de envío</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->submitted_at?->format('d/m/Y H:i') ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Enviado por</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->enviadoPor?->fullname() ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Tutor / responsable</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->tutorNombre() ?? '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Nombre del evento</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->nombre_evento ?: '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Tipo de presentación</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->tipo_presentacion ?: '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Institución</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->institucion ?: '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Anfitrión</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->anfitrion ?: '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Lugar</dt>
                <dd class="font-medium text-gray-900">{{ $solicitud->lugar ?: '—' }}</dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Recursos</dt>
                <dd>
                    <x-ui.badge :variant="$solicitud->requiere_recursos ? 'info' : 'neutral'">
                        {{ $solicitud->requiere_recursos ? 'Requiere recursos' : 'No requiere recursos' }}
                    </x-ui.badge>
                </dd>
            </div>

            <div>
                <dt class="text-sm text-gray-500">Seguro UNAM</dt>
                <dd>
                    <x-ui.badge :variant="$solicitud->requiere_seguro_unam ? 'warning' : 'neutral'">
                        {{ $solicitud->requiere_seguro_unam ? 'Requiere seguro' : 'No requiere seguro' }}
                    </x-ui.badge>
                </dd>
            </div>

            @if ($solicitud->seguro_unam_beneficiario)
                <div>
                    <dt class="text-sm text-gray-500">Beneficiario seguro UNAM</dt>
                    <dd class="font-medium text-gray-900">{{ $solicitud->seguro_unam_beneficiario }}</dd>
                </div>
            @endif
        </dl>

        @if ($solicitud->informacion_adicional)
            <div class="mt-5 border-t border-gray-100 pt-4">
                <p class="text-sm text-gray-500">Información adicional</p>
                <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $solicitud->informacion_adicional }}</p>
            </div>
        @endif
    </section>

    @if ($solicitud->esVisitante())
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900">Visitante principal</h3>

            @if ($solicitud->visitante)
                <dl class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-sm text-gray-500">Nombre</dt>
                        <dd class="font-medium text-gray-900">
                            {{ trim(($solicitud->visitante->nombre ?? '') . ' ' . ($solicitud->visitante->apellidos ?? '')) ?: '—' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Correo</dt>
                        <dd class="font-medium text-gray-900">{{ $solicitud->visitante->email ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Institución</dt>
                        <dd class="font-medium text-gray-900">{{ $solicitud->visitante->institucion ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Lugar</dt>
                        <dd class="font-medium text-gray-900">{{ $solicitud->visitante->lugar ?? '—' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Periodo visita</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $solicitud->visitante->fecha_inicio?->format('d/m/Y') ?? '—' }}
                            <span class="text-gray-400">a</span>
                            {{ $solicitud->visitante->fecha_fin?->format('d/m/Y') ?? '—' }}
                        </dd>
                    </div>
                </dl>
            @else
                <x-ui.alert type="warning" class="mt-4" :dismissible="false">
                    Esta solicitud es de visitante, pero aún no tiene visitante registrado.
                </x-ui.alert>
            @endif
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900">Requerimientos de visita</h3>

            @if ($solicitud->requerimientos->isNotEmpty())
                <ul class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-2">
                    @foreach ($solicitud->requerimientos as $requerimiento)
                        <li class="rounded-lg border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-700">
                            {{ $requerimiento->nombre() }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mt-3 text-sm text-gray-500">Sin requerimientos registrados.</p>
            @endif
        </section>
    @endif

    @if ($solicitud->requiere_recursos)
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900">Recursos solicitados</h3>

            @if ($solicitud->recursos->isNotEmpty())
                <div class="mt-4 space-y-3">
                    @foreach ($solicitud->recursos as $recurso)
                        <article class="rounded-xl border border-gray-200 p-4">
                            <div class="flex flex-col gap-1 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $recurso->proyectoDisplay() }}</p>
                                    <p class="text-xs text-gray-500">{{ $recurso->origenDisplay() }}</p>
                                </div>

                                <p class="text-xs text-gray-500">{{ $recurso->diasDisplay() }}</p>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-3 text-sm md:grid-cols-3">
                                <div>
                                    <span class="text-gray-500">Cuota:</span>
                                    <span class="font-medium text-gray-900">{{ $recurso->cuotaDisplay() ?? '—' }}</span>
                                </div>

                                <div>
                                    <span class="text-gray-500">Avión:</span>
                                    <span class="font-medium text-gray-900">{{ $recurso->avionDisplay() ?? '—' }}</span>
                                </div>

                                <div>
                                    <span class="text-gray-500">Otro:</span>
                                    <span class="font-medium text-gray-900">{{ $recurso->otroDisplay() ?? '—' }}</span>
                                </div>
                            </div>

                            @if ($recurso->informacion_adicional)
                                <p class="mt-3 whitespace-pre-line text-sm text-gray-700">{{ $recurso->informacion_adicional }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <x-ui.alert type="warning" class="mt-4" :dismissible="false">
                    Esta solicitud está marcada como solicitud con recursos, pero aún no tiene recursos capturados.
                </x-ui.alert>
            @endif
        </section>
    @endif

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-gray-900">Documentos</h3>

        @if ($solicitud->documentos->isNotEmpty())
            <ul class="mt-3 divide-y divide-gray-100">
                @foreach ($solicitud->documentos as $documento)
                    <li class="flex flex-col gap-3 py-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $documento->nombreDisplay() }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $documento->sizeDisplay() }}
                                @if ($documento->uploadedBy)
                                    · Subido por {{ $documento->uploadedBy->fullname() ?? 'identidad institucional' }}
                                @endif
                            </p>
                        </div>

                        <x-ui.button variant="secondary" size="sm" href="{{ route('solicitudes.documentos.download', $documento) }}">
                            Descargar
                        </x-ui.button>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mt-3 text-sm text-gray-500">Sin documentos adjuntos.</p>
        @endif
    </section>

    @if ($solicitud->observaciones_sacad || $solicitud->observaciones_administracion)
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900">Observaciones</h3>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                @if ($solicitud->observaciones_sacad)
                    <div>
                        <p class="text-sm text-gray-500">SACAD / CI</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $solicitud->observaciones_sacad }}</p>
                    </div>
                @endif

                @if ($solicitud->observaciones_administracion)
                    <div>
                        <p class="text-sm text-gray-500">Administración</p>
                        <p class="mt-1 whitespace-pre-line text-sm text-gray-800">{{ $solicitud->observaciones_administracion }}</p>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>

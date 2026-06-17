<div class="space-y-6">
    <section class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="irAPaso(1)"
                class="rounded-lg border px-3 py-2 text-sm font-medium {{ $paso === 1 ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                1. Datos generales
            </button>

            @if ($solicitud->requiere_recursos)
                <button type="button" wire:click="irAPaso(2)"
                    class="rounded-lg border px-3 py-2 text-sm font-medium {{ $paso === 2 ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                    2. Recursos
                </button>
            @endif

            <button type="button" wire:click="irAPaso(3)"
                class="rounded-lg border px-3 py-2 text-sm font-medium {{ $paso === 3 ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                {{ $solicitud->requiere_recursos ? '3' : '2' }}. Documentos
            </button>

            <button type="button" wire:click="irAPaso(4)"
                class="rounded-lg border px-3 py-2 text-sm font-medium {{ $paso === 4 ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                {{ $solicitud->requiere_recursos ? '4' : '3' }}. Revisión y envío
            </button>
        </div>
    </section>

    @if ($paso === 1)
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <form wire:submit.prevent="guardarPaso1" class="space-y-5">
                @if ($can_manage_owner)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <x-ui.select
                            label="Solicitante propietario"
                            name="owner_id"
                            wire:model="form.owner_id"
                            :options="$c_owner_identities"
                            option-value="id"
                            option-label="email"
                            placeholder="Seleccione la identidad propietaria de la solicitud"
                            :disabled="! $can_modify_owner"
                            help="El propietario institucional se guarda con identity_links.id." />

                        @if (! $can_modify_owner)
                            <p class="mt-2 text-xs text-amber-800">
                                Esta solicitud fue creada directamente por el propietario; el owner_id se muestra como referencia administrativa.
                            </p>
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-ui.select
                        label="Tipo de solicitud"
                        name="tipo_solicitud_id"
                        wire:model="form.tipo_solicitud_id"
                        :options="$c_tipos_solicitud"
                        placeholder="Seleccione una opción"
                        required />

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 md:col-span-2">
                        <x-ui.checkbox
                            name="requiere_recursos"
                            label="Requiere recursos"
                            wire:model="form.requiere_recursos"
                            help="Para ausencia con recursos, solo recursos y recursos IRyA para estudiantes se activará automáticamente. En visitantes puede marcarse si aplica." />
                    </div>

                    <x-ui.select
                        label="Motivo"
                        name="motivo_id"
                        wire:model="form.motivo_id"
                        :options="$c_motivos"
                        placeholder="Seleccione una opción" />

                    <x-ui.input
                        label="Motivo otro"
                        name="motivo_otro"
                        wire:model="form.motivo_otro"
                        placeholder="Especifique el motivo si seleccionó Otro" />

                    <x-ui.input
                        label="Fecha de inicio"
                        name="fecha_inicio"
                        type="date"
                        wire:model="form.fecha_inicio" />

                    <x-ui.input
                        label="Fecha de fin"
                        name="fecha_fin"
                        type="date"
                        wire:model="form.fecha_fin" />

                    <x-ui.select
                        label="País"
                        name="pais_id"
                        wire:model="form.pais_id"
                        :options="$c_paises"
                        placeholder="Seleccione una opción" />

                    <x-ui.input
                        label="Nombre del evento"
                        name="nombre_evento"
                        wire:model="form.nombre_evento" />

                    <x-ui.input
                        label="Tipo de presentación"
                        name="tipo_presentacion"
                        wire:model="form.tipo_presentacion"
                        placeholder="Ponencia, cartel, charla, participación, etc." />

                    <x-ui.input
                        label="Institución"
                        name="institucion"
                        wire:model="form.institucion" />

                    <x-ui.input
                        label="Anfitrión"
                        name="anfitrion"
                        wire:model="form.anfitrion" />

                    <x-ui.input
                        label="Lugar"
                        name="lugar"
                        wire:model="form.lugar" />

                    <x-ui.select
                        label="Tutor / responsable institucional"
                        name="tutor_id"
                        wire:model="form.tutor_id"
                        :options="$c_tutores"
                        option-value="id"
                        option-label="email"
                        placeholder="Seleccione una opción" />

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <x-ui.checkbox
                            name="requiere_seguro_unam"
                            label="Requiere seguro UNAM"
                            wire:model="form.requiere_seguro_unam"
                            help="Marcar cuando la solicitud requiera documentación o trámite de seguro institucional." />
                    </div>

                    <x-ui.input
                        label="Beneficiario del seguro UNAM"
                        name="seguro_unam_beneficiario"
                        wire:model="form.seguro_unam_beneficiario" />

                    <div class="md:col-span-2">
                        <x-ui.textarea
                            label="Información adicional"
                            name="informacion_adicional"
                            wire:model="form.informacion_adicional"
                            rows="5" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                    <x-ui.button variant="secondary" href="{{ route('solicitudes.show', $solicitud) }}">
                        Cancelar
                    </x-ui.button>

                    <x-ui.button type="submit" wire:loading.attr="disabled">
                        Guardar cambios
                    </x-ui.button>
                </div>
            </form>
        </section>

        @if ($this->esTipoVisitante())
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <form wire:submit.prevent="guardarVisitante" class="space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Visitante principal</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Registre los datos base del visitante asociado al expediente.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <x-ui.select
                            label="Tipo de visitante"
                            name="tipo_visitante_id"
                            wire:model="visitanteForm.tipo_visitante_id"
                            :options="$c_tipos_visitante"
                            placeholder="Seleccione una opción" />

                        <x-ui.input
                            label="Estudiante asociado"
                            name="estudiante_asociado_id"
                            type="number"
                            wire:model="visitanteForm.estudiante_asociado_id"
                            placeholder="ID del estudiante asociado"
                            help="Selector definitivo pendiente para el mini módulo de estudiantes asociados." />

                        <x-ui.input label="Nombre" name="nombre" wire:model="visitanteForm.nombre" />
                        <x-ui.input label="Apellidos" name="apellidos" wire:model="visitanteForm.apellidos" />
                        <x-ui.input label="Correo" name="email" type="email" wire:model="visitanteForm.email" />

                        <x-ui.select
                            label="País del visitante"
                            name="pais_id"
                            wire:model="visitanteForm.pais_id"
                            :options="$c_paises"
                            placeholder="Seleccione una opción" />

                        <x-ui.input label="Institución" name="institucion" wire:model="visitanteForm.institucion" />
                        <x-ui.input label="Lugar" name="lugar" wire:model="visitanteForm.lugar" />
                        <x-ui.input label="Fecha de inicio" name="fecha_inicio" type="date" wire:model="visitanteForm.fecha_inicio" />
                        <x-ui.input label="Fecha de fin" name="fecha_fin" type="date" wire:model="visitanteForm.fecha_fin" />
                    </div>

                    <div class="flex justify-end border-t border-gray-100 pt-4">
                        <x-ui.button type="submit" wire:loading.attr="disabled">
                            Guardar visitante
                        </x-ui.button>
                    </div>
                </form>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <form wire:submit.prevent="guardarRequerimientos" class="space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Requerimientos de visita</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Seleccione solo los requerimientos necesarios para la visita. No son obligatorios.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        @foreach ($c_requerimientos_visitante as $requerimiento)
                            <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 text-sm hover:bg-gray-50">
                                <input type="checkbox" wire:model="requerimientosSeleccionados" value="{{ $requerimiento->id }}"
                                    class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                                <span>
                                    <span class="block font-medium text-gray-800">
                                        {{ $requerimiento->nombre }}
                                    </span>

                                    @if ($requerimiento->descripcion)
                                        <span class="block text-xs text-gray-500">
                                            {{ $requerimiento->descripcion }}
                                        </span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>

                    @error('requerimientosSeleccionados')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @error('requerimientosSeleccionados.*')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end border-t border-gray-100 pt-4">
                        <x-ui.button type="submit" wire:loading.attr="disabled">
                            Guardar requerimientos
                        </x-ui.button>
                    </div>
                </form>
            </section>
        @endif
    @endif

    @if ($paso === 2 && $solicitud->requiere_recursos)
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <form wire:submit.prevent="guardarRecursos" class="space-y-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Recursos solicitados</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Capture los recursos asociados al expediente. Puede registrar uno o varios bloques según el origen del recurso.
                        </p>
                    </div>

                    <x-ui.button type="button" variant="secondary" wire:click="agregarRecurso">
                        Agregar recurso
                    </x-ui.button>
                </div>

                <div class="space-y-4">
                    @forelse($recursosForm as $index => $recurso)
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <h4 class="text-sm font-semibold text-gray-900">
                                    Recurso {{ $index + 1 }}
                                </h4>

                                <button type="button" wire:click="quitarRecurso({{ $index }})"
                                    class="text-sm font-medium text-red-600 hover:text-red-700">
                                    Quitar
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <x-ui.select
                                    label="Origen"
                                    name="recursosForm.{{ $index }}.origen_id"
                                    wire:model="recursosForm.{{ $index }}.origen_id"
                                    :options="$c_origenes_recurso"
                                    placeholder="Seleccione una opción" />

                                <x-ui.input
                                    label="Proyecto / nombre"
                                    name="recursosForm.{{ $index }}.proyecto_nombre"
                                    wire:model="recursosForm.{{ $index }}.proyecto_nombre" />

                                <x-ui.input
                                    label="Días nacionales"
                                    name="recursosForm.{{ $index }}.dias_n"
                                    type="number"
                                    wire:model="recursosForm.{{ $index }}.dias_n" />

                                <x-ui.input
                                    label="Días internacionales"
                                    name="recursosForm.{{ $index }}.dias_i"
                                    type="number"
                                    wire:model="recursosForm.{{ $index }}.dias_i" />

                                @foreach (['cuota' => 'Cuota', 'avion' => 'Avión', 'otro' => 'Otro'] as $campo => $label)
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ $label }}</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <input type="number" step="0.01" min="0"
                                                wire:model="recursosForm.{{ $index }}.{{ $campo }}"
                                                class="col-span-2 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                                            <select wire:model="recursosForm.{{ $index }}.{{ $campo }}_divisa"
                                                class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">Divisa</option>

                                                @foreach ($c_divisas as $divisa)
                                                    <option value="{{ $divisa->id }}">
                                                        {{ $divisa->clave }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="md:col-span-2">
                                    <x-ui.textarea
                                        label="Información adicional del recurso"
                                        name="recursosForm.{{ $index }}.informacion_adicional"
                                        wire:model="recursosForm.{{ $index }}.informacion_adicional"
                                        rows="3" />
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                            No hay recursos capturados. Use “Agregar recurso” para iniciar.
                        </div>
                    @endforelse
                </div>

                <div class="flex justify-end border-t border-gray-100 pt-4">
                    <x-ui.button type="submit" wire:loading.attr="disabled">
                        Guardar recursos
                    </x-ui.button>
                </div>
            </form>
        </section>
    @endif

    @if ($paso === 3)
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <form wire:submit.prevent="subirDocumentos" class="space-y-5">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Documentos del expediente</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Adjunte documentos relacionados con la solicitud. Esta sección es flexible y no bloquea el envío.
                    </p>
                </div>

                <x-ui.input-file name="documentosUpload" label="Adjuntar documentos" wire:model="documentosUpload"
                    multiple drag-drop drag-text="Arrastra documentos aquí o haz clic para seleccionarlos"
                    drag-hint="Puede subir varios archivos. Tamaño máximo sugerido: 10 MB por archivo."
                    help="Los documentos son flexibles y no bloquean el envío de la solicitud." />

                <div class="flex justify-end border-t border-gray-100 pt-4">
                    <x-ui.button type="submit" wire:loading.attr="disabled" wire:target="subirDocumentos,documentosUpload">
                        Subir documento(s)
                    </x-ui.button>
                </div>
            </form>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Documentos adjuntos</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Los documentos cargados forman parte del expediente de la solicitud.
                </p>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($solicitud->documentos as $documento)
                    <div class="flex items-start justify-between gap-4 rounded-lg border border-gray-200 p-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $documento->nombreDisplay() }}
                            </p>

                            <p class="mt-1 text-xs text-gray-500">
                                {{ $documento->sizeDisplay() }}
                                @if ($documento->mime_type)
                                    · {{ $documento->mime_type }}
                                @endif
                                @if ($documento->uploadedBy)
                                    · Subido por {{ $documento->uploadedBy->fullname() ?? ($documento->uploadedBy->emailResolved() ?? 'identidad institucional') }}
                                @endif
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                {{ $documento->path }}
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <x-ui.button variant="secondary" size="sm" href="{{ route('solicitudes.documentos.download', $documento) }}">
                                Descargar
                            </x-ui.button>

                            @can('update', $solicitud)
                                <x-ui.button type="button" variant="danger" size="sm" wire:click="confirmarEliminarDocumento({{ $documento->id }})">
                                    Eliminar
                                </x-ui.button>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                        No hay documentos adjuntos. Puede enviar la solicitud sin documentos, pero el sistema mostrará esta advertencia en la revisión final.
                    </div>
                @endforelse
            </div>
        </section>
    @endif

    @if ($paso === 4)
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Revisión final</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Revise la información capturada antes de enviar formalmente la solicitud.
                    </p>
                </div>

                <span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium {{ $solicitud->estatusBadgeClass() }}">
                    {{ $solicitud->estatusNombre() }}
                </span>
            </div>

            @error('envio')
                <x-ui.alert type="error" class="mt-4" :dismissible="false">
                    {{ $message }}
                </x-ui.alert>
            @enderror

            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 p-4">
                    <h4 class="text-sm font-semibold text-gray-900">Datos generales</h4>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Folio</dt>
                            <dd class="text-gray-800">{{ $solicitud->folioDisplay() }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Tipo</dt>
                            <dd class="text-gray-800">{{ $solicitud->tipoSolicitud?->nombre ?? 'Sin tipo' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Motivo</dt>
                            <dd class="text-gray-800">{{ $solicitud->motivo?->nombre ?? 'Sin motivo' }}</dd>
                        </div>
                        @if ($solicitud->motivo_otro)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Motivo otro</dt>
                                <dd class="text-gray-800">{{ $solicitud->motivo_otro }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Periodo</dt>
                            <dd class="text-gray-800">
                                {{ optional($solicitud->fecha_inicio)->format('d/m/Y') ?? 'Sin inicio' }} —
                                {{ optional($solicitud->fecha_fin)->format('d/m/Y') ?? 'Sin fin' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-lg border border-gray-200 p-4">
                    <h4 class="text-sm font-semibold text-gray-900">Documentos</h4>
                    @if ($solicitud->documentos->isNotEmpty())
                        <ul class="mt-3 space-y-2 text-sm text-gray-700">
                            @foreach ($solicitud->documentos as $documento)
                                <li class="flex items-center justify-between gap-3">
                                    <span>{{ $documento->nombreDisplay() }}</span>
                                    <span class="text-xs text-gray-500">{{ $documento->sizeDisplay() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-3 text-sm text-amber-700">
                            No hay documentos adjuntos. Esto no bloquea el envío, pero quedará visible en el expediente.
                        </p>
                    @endif
                </div>
            </div>

            @if ($solicitud->requiere_recursos)
                <div class="mt-4 rounded-lg border border-gray-200 p-4">
                    <h4 class="text-sm font-semibold text-gray-900">Recursos solicitados</h4>
                    @if ($solicitud->recursos->isNotEmpty())
                        <div class="mt-3 space-y-3">
                            @foreach ($solicitud->recursos as $recurso)
                                <div class="rounded-lg border border-gray-100 bg-gray-50 p-3 text-sm text-gray-700">
                                    <div class="font-medium text-gray-900">
                                        {{ $recurso->proyecto_nombre ?: 'Recurso sin proyecto/nombre' }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        Días nacionales: {{ $recurso->dias_n ?? 0 }} ·
                                        Días internacionales: {{ $recurso->dias_i ?? 0 }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-3 text-sm text-red-700">
                            Esta solicitud requiere recursos y aún no tiene recursos capturados.
                        </p>
                    @endif
                </div>
            @endif

            @if ($this->esTipoVisitante())
                <div class="mt-4 rounded-lg border border-gray-200 p-4">
                    <h4 class="text-sm font-semibold text-gray-900">Visitante</h4>
                    @if ($solicitud->visitante)
                        <dl class="mt-3 grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Nombre</dt>
                                <dd class="text-gray-800">
                                    {{ trim(($solicitud->visitante->nombre ?? '') . ' ' . ($solicitud->visitante->apellidos ?? '')) ?: 'Sin nombre' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Correo</dt>
                                <dd class="text-gray-800">{{ $solicitud->visitante->email ?? 'Sin correo' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Institución</dt>
                                <dd class="text-gray-800">{{ $solicitud->visitante->institucion ?? 'Sin institución' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Lugar</dt>
                                <dd class="text-gray-800">{{ $solicitud->visitante->lugar ?? 'Sin lugar' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="mt-3 text-sm text-red-700">
                            Esta solicitud es de visitante y aún no tiene datos de visitante capturados.
                        </p>
                    @endif
                </div>
            @endif

            <x-ui.alert type="warning" class="mt-5" :dismissible="false">
                Al enviar la solicitud se asignará folio institucional y el expediente pasará a revisión. Después del envío, la edición quedará restringida conforme al estado de la solicitud.
            </x-ui.alert>

            <div class="mt-5 flex justify-end gap-3 border-t border-gray-100 pt-4">
                <x-ui.button type="button" variant="secondary" wire:click="pasoAnterior">
                    Regresar
                </x-ui.button>

                @can('send', $solicitud)
                    <x-ui.button type="button" variant="primary" wire:click="enviarSolicitud" wire:loading.attr="disabled">
                        Enviar solicitud
                    </x-ui.button>
                @endcan
            </div>
        </section>
    @endif

    <x-ui.confirm-delete-modal
        model="confirmDeleteModal"
        title="Eliminar documento"
        message="Esta acción eliminará el documento del expediente y también el archivo físico asociado."
        warning="Esta operación no se puede deshacer."
        cancel-action="cancelarEliminarDocumento"
        confirm-action="eliminarDocumento"
        confirm-text="Eliminar documento"
        cancel-text="Cancelar"
    />
</div>

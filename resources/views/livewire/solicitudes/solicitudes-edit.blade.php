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
                        <label class="mb-1 block text-sm font-medium text-amber-900">
                            Solicitante propietario
                        </label>

                        @if ($can_modify_owner)
                            <select wire:model="form.owner_id"
                                class="w-full rounded-lg border-amber-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                <option value="">Seleccione la identidad propietaria de la solicitud</option>

                                @foreach ($c_owner_identities as $identity)
                                    <option value="{{ $identity->id }}">
                                        {{ $identity->fullname() ?? 'Identidad sin nombre' }}
                                        @if ($identity->emailResolved())
                                            - {{ $identity->emailResolved() }}
                                        @endif
                                        [{{ $identity->identity_type }} #{{ $identity->id }}]
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-2 text-xs text-amber-800">
                                Esta solicitud fue creada por administracion a nombre de otra identidad.
                                Puede modificar el propietario si fue capturado incorrectamente.
                            </p>
                        @else
                            <div class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm text-gray-800">
                                {{ $solicitud->owner?->fullname() ?? 'Identidad sin nombre' }}
                                @if ($solicitud->owner?->emailResolved())
                                    - {{ $solicitud->owner->emailResolved() }}
                                @endif
                                [{{ $solicitud->owner?->identity_type ?? 'sin tipo' }} #{{ $solicitud->owner_id }}]
                            </div>

                            <p class="mt-2 text-xs text-amber-800">
                                Esta solicitud fue creada directamente por el propietario; el owner_id se muestra solo
                                como
                                referencia administrativa.
                            </p>
                        @endif

                        @error('form.owner_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Tipo de solicitud
                        </label>
                        <select wire:model="form.tipo_solicitud_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccione una opcion</option>

                            @foreach ($c_tipos_solicitud as $tipo)
                                <option value="{{ $tipo->id }}">
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('form.tipo_solicitud_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 md:col-span-2">
                        <label class="flex items-start gap-3 text-sm text-gray-700">
                            <input type="checkbox" wire:model="form.requiere_recursos"
                                class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                            <span>
                                <span class="block font-medium text-gray-800">
                                    Requiere recursos
                                </span>
                                <span class="block text-xs text-gray-500">
                                    Para ausencia con recursos, solo recursos y recursos IRyA para estudiantes se
                                    activará automáticamente.
                                    En visitantes puede marcarse si aplica.
                                </span>
                            </span>
                        </label>

                        @error('form.requiere_recursos')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Motivo
                        </label>
                        <select wire:model="form.motivo_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccione una opcion</option>

                            @foreach ($c_motivos as $motivo)
                                <option value="{{ $motivo->id }}">
                                    {{ $motivo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('form.motivo_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Motivo otro
                        </label>

                        <input type="text" wire:model="form.motivo_otro"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Especifique el motivo si seleccionó Otro">

                        @error('form.motivo_otro')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Fecha de inicio
                        </label>
                        <input type="date" wire:model="form.fecha_inicio"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('form.fecha_inicio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Fecha de fin
                        </label>
                        <input type="date" wire:model="form.fecha_fin"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('form.fecha_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            País
                        </label>

                        <select wire:model="form.pais_id"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccione una opción</option>

                            @foreach ($c_paises as $pais)
                                <option value="{{ $pais->id }}">
                                    {{ $pais->nombre }}
                                </option>
                            @endforeach
                        </select>

                        @error('form.pais_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Nombre del evento
                        </label>

                        <input type="text" wire:model="form.nombre_evento"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @error('form.nombre_evento')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Tipo de presentación
                        </label>

                        <input type="text" wire:model="form.tipo_presentacion"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ponencia, cartel, charla, participación, etc.">

                        @error('form.tipo_presentacion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Institución
                        </label>

                        <input type="text" wire:model="form.institucion"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @error('form.institucion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Anfitrión
                        </label>

                        <input type="text" wire:model="form.anfitrion"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @error('form.anfitrion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Lugar
                        </label>

                        <input type="text" wire:model="form.lugar"
                            class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @error('form.lugar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Tutor / responsable institucional
                    </label>

                    <select wire:model="form.tutor_id"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Seleccione una opción</option>

                        @foreach ($c_tutores as $tutor)
                            <option value="{{ $tutor->id }}">
                                {{ $tutor->fullname() ?? ($tutor->emailResolved() ?? 'Identidad #' . $tutor->id) }}
                            </option>
                        @endforeach
                    </select>

                    @error('form.tutor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <label class="flex items-start gap-3 text-sm text-gray-700">
                        <input type="checkbox" wire:model="form.requiere_seguro_unam"
                            class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">

                        <span>
                            <span class="block font-medium text-gray-800">
                                Requiere seguro UNAM
                            </span>
                            <span class="block text-xs text-gray-500">
                                Marcar cuando la solicitud requiera documentación o trámite de seguro institucional.
                            </span>
                        </span>
                    </label>

                    @error('form.requiere_seguro_unam')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Beneficiario del seguro UNAM
                    </label>

                    <input type="text" wire:model="form.seguro_unam_beneficiario"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('form.seguro_unam_beneficiario')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Informacion adicional
                    </label>
                    <textarea wire:model="form.informacion_adicional" rows="5"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    @error('form.informacion_adicional')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div
                    class="flex flex-col gap-3 border-t border-gray-100 pt-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <span
                            class="inline-flex rounded-full border px-2.5 py-1 text-xs font-medium {{ $solicitud->estatusBadgeClass() }}">
                            {{ $solicitud->estatusNombre() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('solicitudes.show', $solicitud) }}"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </a>

                        <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                            wire:loading.attr="disabled">
                            Guardar cambios
                        </button>

                        {{--
                        @can('send', $solicitud)
                            <button type="button" wire:click="enviarSolicitud"
                                wire:confirm="Al enviar la solicitud ya no podra editarla libremente. Desea continuar?"
                                class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700"
                                wire:loading.attr="disabled">
                                Enviar solicitud
                            </button>
                        @endcan
                        --}}
                    </div>
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
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Tipo de visitante
                            </label>

                            <select wire:model="visitanteForm.tipo_visitante_id"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccione una opción</option>

                                @foreach ($c_tipos_visitante as $tipoVisitante)
                                    <option value="{{ $tipoVisitante->id }}">
                                        {{ $tipoVisitante->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            @error('visitanteForm.tipo_visitante_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Estudiante asociado
                            </label>

                            <input type="number" wire:model="visitanteForm.estudiante_asociado_id"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="ID del estudiante asociado">

                            @error('visitanteForm.estudiante_asociado_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <p class="mt-1 text-xs text-gray-500">
                                Selector definitivo pendiente para el mini módulo de estudiantes asociados.
                            </p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nombre</label>
                            <input type="text" wire:model="visitanteForm.nombre"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('visitanteForm.nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Apellidos</label>
                            <input type="text" wire:model="visitanteForm.apellidos"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('visitanteForm.apellidos')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Correo</label>
                            <input type="email" wire:model="visitanteForm.email"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('visitanteForm.email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                País del visitante
                            </label>

                            <select wire:model="visitanteForm.pais_id"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccione una opción</option>

                                @foreach ($c_paises as $pais)
                                    <option value="{{ $pais->id }}">
                                        {{ $pais->nombre }}
                                    </option>
                                @endforeach
                            </select>

                            @error('visitanteForm.pais_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Institucion</label>
                            <input type="text" wire:model="visitanteForm.institucion"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('visitanteForm.institucion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Lugar</label>
                            <input type="text" wire:model="visitanteForm.lugar"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('visitanteForm.lugar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Fecha de inicio</label>
                            <input type="date" wire:model="visitanteForm.fecha_inicio"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('visitanteForm.fecha_inicio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Fecha de fin</label>
                            <input type="date" wire:model="visitanteForm.fecha_fin"
                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('visitanteForm.fecha_fin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-gray-100 pt-4">
                        <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                            wire:loading.attr="disabled">
                            Guardar visitante
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <form wire:submit.prevent="guardarRequerimientos" class="space-y-5">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            Requerimientos de visita
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Seleccione solo los requerimientos necesarios para la visita. No son obligatorios.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        @foreach ($c_requerimientos_visitante as $requerimiento)
                            <label
                                class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 text-sm hover:bg-gray-50">
                                <input type="checkbox" wire:model="requerimientosSeleccionados"
                                    value="{{ $requerimiento->id }}"
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
                        <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                            wire:loading.attr="disabled">
                            Guardar requerimientos
                        </button>
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
                            Capture los recursos asociados al expediente. Puede registrar uno o varios bloques según el
                            origen del recurso.
                        </p>
                    </div>

                    <button type="button" wire:click="agregarRecurso"
                        class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Agregar recurso
                    </button>
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
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Origen</label>
                                    <select wire:model="recursosForm.{{ $index }}.origen_id"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Seleccione una opción</option>

                                        @foreach ($c_origenes_recurso as $origen)
                                            <option value="{{ $origen->id }}">
                                                {{ $origen->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("recursosForm.$index.origen_id")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Proyecto /
                                        nombre</label>
                                    <input type="text"
                                        wire:model="recursosForm.{{ $index }}.proyecto_nombre"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error("recursosForm.$index.proyecto_nombre")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Días nacionales</label>
                                    <input type="number" min="0"
                                        wire:model="recursosForm.{{ $index }}.dias_n"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error("recursosForm.$index.dias_n")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">Días
                                        internacionales</label>
                                    <input type="number" min="0"
                                        wire:model="recursosForm.{{ $index }}.dias_i"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error("recursosForm.$index.dias_i")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                @foreach (['cuota' => 'Cuota', 'avion' => 'Avión', 'otro' => 'Otro'] as $campo => $label)
                                    <div>
                                        <label
                                            class="mb-1 block text-sm font-medium text-gray-700">{{ $label }}</label>
                                        <div class="grid grid-cols-3 gap-2">
                                            <input type="number" step="0.01" min="0"
                                                wire:model="recursosForm.{{ $index }}.{{ $campo }}"
                                                class="col-span-2 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                                            <select
                                                wire:model="recursosForm.{{ $index }}.{{ $campo }}_divisa"
                                                class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">Divisa</option>

                                                @foreach ($c_divisas as $divisa)
                                                    <option value="{{ $divisa->id }}">
                                                        {{ $divisa->clave }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @error("recursosForm.$index.$campo")
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror

                                        @error("recursosForm.$index.{$campo}_divisa")
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach

                                <div class="md:col-span-2">
                                    <label class="mb-1 block text-sm font-medium text-gray-700">
                                        Información adicional del recurso
                                    </label>

                                    <textarea wire:model="recursosForm.{{ $index }}.informacion_adicional" rows="3"
                                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>

                                    @error("recursosForm.$index.informacion_adicional")
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
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
                    <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        wire:loading.attr="disabled">
                        Guardar recursos
                    </button>
                </div>
            </form>
        </section>
    @endif

    @if ($paso === 3)
        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <form wire:submit.prevent="subirDocumentos" class="space-y-5">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        Documentos del expediente
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Adjunte documentos relacionados con la solicitud. Esta sección es flexible y no bloquea el
                        envío.
                    </p>
                </div>

                <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Adjuntar documentos
                    </label>

                    <input type="file" wire:model="documentosUpload" multiple
                        class="mt-2 block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">

                    <p class="mt-2 text-xs text-gray-500">
                        Puede subir varios archivos. Tamaño máximo sugerido: 10 MB por archivo.
                    </p>

                    @error('documentosUpload')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @error('documentosUpload.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="documentosUpload" class="mt-2 text-sm text-gray-500">
                        Preparando archivo(s)...
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-100 pt-4">
                    <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700"
                        wire:loading.attr="disabled" wire:target="subirDocumentos,documentosUpload">
                        Subir documento(s)
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">
                    Documentos adjuntos
                </h3>

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
                                    · Subido por
                                    {{ $documento->uploadedBy->fullname() ?? ($documento->uploadedBy->emailResolved() ?? 'identidad institucional') }}
                                @endif
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                {{ $documento->path }}
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <a href="{{ route('solicitudes.documentos.download', $documento) }}"
                                class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Descargar
                            </a>

                            @can('update', $solicitud)
                                <button type="button" wire:click="eliminarDocumento({{ $documento->id }})"
                                    wire:confirm="¿Desea eliminar este documento del expediente?"
                                    class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
                                    Eliminar
                                </button>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500">
                        No hay documentos adjuntos. Puede enviar la solicitud sin documentos, pero el sistema mostrará
                        esta advertencia en la revisión final.
                    </div>
                @endforelse
            </div>
        </section>
    @endif
</div>

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
    @endif
</div>

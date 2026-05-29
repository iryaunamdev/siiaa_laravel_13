<div class="space-y-6">
    <x-ui.header title="Mi perfil" description="Resumen de los datos institucionales registrados en el sistema." />

    @if (!$this->tienePerfilPublico)
        <x-ui.alert variant="warning">
            Aún no tienes un perfil público registrado en el directorio institucional.
            La información visible deberá ser creada o revisada por un editor.
        </x-ui.alert>
    @elseif (!$this->perfilPublicado)
        <x-ui.alert variant="info">
            Tu perfil público existe, pero todavía no está publicado en el directorio institucional.
            La visibilidad se administra desde el módulo Directorio.
        </x-ui.alert>
    @else
        <x-ui.alert variant="success">
            Tu perfil público está activo y visible en el directorio institucional.
        </x-ui.alert>
    @endif

    <x-ui.panel>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <div class="text-xs uppercase tracking-wide text-zinc-500">Nombre</div>
                <div class="font-medium text-zinc-900">{{ $this->nombreBase }}</div>
            </div>

            <div>
                <div class="text-xs uppercase tracking-wide text-zinc-500">Correo institucional</div>
                <div class="font-medium text-zinc-900">{{ $identityLink?->email ?? '—' }}</div>
            </div>

            <div>
                <div class="text-xs uppercase tracking-wide text-zinc-500">Tipo de identidad</div>
                <div class="font-medium text-zinc-900">
                    @if ($this->isPersonalIrya)
                        Personal IRyA
                    @elseif ($this->isEstudianteSiiap)
                        Estudiante SIIAP
                    @else
                        {{ $identityLink?->identity_type ?? '—' }}
                    @endif
                </div>
            </div>
        </div>
    </x-ui.panel>

    @if ($this->isPersonalIrya)
        <x-ui.panel>
            <x-slot:title>Datos generales</x-slot:title>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Nombre</div>
                    <div class="font-medium text-zinc-900">{{ $persona?->nombre ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Apellido paterno</div>
                    <div class="font-medium text-zinc-900">{{ $persona?->apellidop ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Apellido materno</div>
                    <div class="font-medium text-zinc-900">{{ $persona?->apellidom ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Correo</div>
                    <div class="font-medium text-zinc-900">{{ $persona?->email ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">CURP</div>
                    <div class="font-medium text-zinc-900">{{ $persona?->curp ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">RFC</div>
                    <div class="font-medium text-zinc-900">{{ $persona?->rfc ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Sexo</div>
                    <div class="font-medium text-zinc-900">{{ $persona?->sexo?->nombre ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Nacionalidad</div>
                    <div class="font-medium text-zinc-900">{{ $persona?->nacionalidad?->nombre ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Fecha de nacimiento</div>
                    <div class="font-medium text-zinc-900">
                        {{ optional($persona?->fecha_nacimiento)->format('d/m/Y') ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Estado</div>
                    <div class="font-medium text-zinc-900">
                        {{ $persona?->activo ? 'Activo' : 'Inactivo' }}
                    </div>
                </div>
            </div>
        </x-ui.panel>

        <x-ui.panel>
            <x-slot:title>Ingreso actual</x-slot:title>

            @if ($this->ingresoActual)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Tipo de personal</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->ingresoActual?->tipoPersonal?->nombre ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Nombramiento</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->ingresoActual?->nombramiento?->nombre ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Contrato</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->ingresoActual?->contrato?->nombre ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Escolaridad</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->ingresoActual?->escolaridad?->nombre ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Número de trabajador</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->ingresoActual?->numero_trabajador ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">CUV</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->ingresoActual?->cuv ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Fecha de ingreso</div>
                        <div class="font-medium text-zinc-900">
                            {{ optional($this->ingresoActual?->fecha_ingreso)->format('d/m/Y') ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Estado ingreso</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->ingresoActual?->activo ? 'Activo' : 'Inactivo' }}
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-zinc-500">
                    No se encontró un ingreso principal activo asociado.
                </p>
            @endif
        </x-ui.panel>

        @include('livewire.personas._mi-perfil_perfil-publico', [
            'perfilPublico' => $this->perfilPublico,
        ])

        <x-ui.panel>
            <x-slot:title>Datos editables del perfil público</x-slot:title>

            <form wire:submit.prevent="savePublicFields" class="space-y-4">
                <x-ui.input wire:model.defer="homepage_url" name="homepage_url" label="URL personal" type="url"
                    placeholder="https://..." />

                <x-ui.textarea wire:model.defer="observaciones" name="observaciones"
                    label="Semblanza breve / resumen público" rows="4"
                    placeholder="Breve resumen para uso editorial del directorio..." />

                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    Puedes actualizar tu URL personal y una semblanza breve.
                    Estos cambios no publican automáticamente tu perfil; la visibilidad se controla desde el módulo
                    Directorio.
                </div>

                <div class="flex justify-end">
                    <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled"
                        wire:target="savePublicFields">
                        Guardar datos editables
                    </x-ui.button>
                </div>
            </form>
        </x-ui.panel>

        <x-ui.panel>
            <x-slot:title>Perfil académico</x-slot:title>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">ORCID</div>
                    <div class="font-medium text-zinc-900">{{ $this->perfilAcademico?->orcid ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Scopus ID</div>
                    <div class="font-medium text-zinc-900">{{ $this->perfilAcademico?->scopus_id ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">SNI</div>
                    <div class="font-medium text-zinc-900">{{ $this->perfilAcademico?->sni?->nombre ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Vigencia SNI</div>
                    <div class="font-medium text-zinc-900">
                        {{ optional($this->perfilAcademico?->sni_vigencia)->format('d/m/Y') ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">PRIDE</div>
                    <div class="font-medium text-zinc-900">{{ $this->perfilAcademico?->pride?->nombre ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Vigencia PRIDE</div>
                    <div class="font-medium text-zinc-900">
                        {{ optional($this->perfilAcademico?->pride_vigencia)->format('d/m/Y') ?? '—' }}
                    </div>
                </div>

                <div class="md:col-span-3">
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Área de investigación</div>
                    <div class="font-medium text-zinc-900">{{ $this->perfilAcademico?->research_area ?? '—' }}</div>
                </div>

                <div class="md:col-span-3">
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Palabras clave académicas</div>
                    <div class="font-medium text-zinc-900">{{ $this->perfilAcademico?->academic_keywords ?? '—' }}
                    </div>
                </div>
            </div>
        </x-ui.panel>
    @endif

    @if ($this->isEstudianteSiiap)
        <x-ui.panel>
            <x-slot:title>Datos generales</x-slot:title>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Nombre</div>
                    <div class="font-medium text-zinc-900">{{ $estudiante?->nombre ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Apellido paterno</div>
                    <div class="font-medium text-zinc-900">{{ $estudiante?->apellidop ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Apellido materno</div>
                    <div class="font-medium text-zinc-900">{{ $estudiante?->apellidom ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Correo</div>
                    <div class="font-medium text-zinc-900">{{ $estudiante?->email ?? ($identityLink?->email ?? '—') }}
                    </div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Correo alterno</div>
                    <div class="font-medium text-zinc-900">{{ $estudiante?->email_alt ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Grado actual</div>
                    <div class="font-medium text-zinc-900">{{ $estudiante?->grado_actual_nombre ?? '—' }}</div>
                </div>

                <div>
                    <div class="text-xs uppercase tracking-wide text-zinc-500">Estado</div>
                    <div class="font-medium text-zinc-900">
                        {{ $estudiante?->activo ? 'Activo' : 'No vigente' }}
                    </div>
                </div>
            </div>
        </x-ui.panel>

        @include('livewire.personas._mi-perfil_perfil-publico', [
            'perfilPublico' => $this->perfilPublico,
            'readonlyNotice' =>
                'Esta información forma parte del perfil público asociado a tu identidad institucional y se muestra en modo solo lectura. Cualquier ajuste deberá solicitarse al editor correspondiente.',
        ])

        <x-ui.panel>
            <x-slot:title>Inscripción actual</x-slot:title>

            @if ($this->inscripcionActual)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Semestre</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->inscripcionActual?->semestre?->nombre ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Grado</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->inscripcionActual?->grado?->nombre ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-zinc-500">Adscripción</div>
                        <div class="font-medium text-zinc-900">
                            {{ $this->inscripcionActual?->adscripcion?->nombre ?? '—' }}
                        </div>
                    </div>
                </div>
            @else
                <p class="text-sm text-zinc-500">
                    No se encontró inscripción actual asociada al IRyA.
                </p>
            @endif
        </x-ui.panel>

        <x-ui.panel>
            <x-slot:title>Comité tutor actual</x-slot:title>

            @if ($this->comiteTutorActual && $this->comiteTutorActual->count())
                <div class="divide-y divide-zinc-100">
                    @foreach ($this->comiteTutorActual as $integrante)
                        @php
                            $tutor = $integrante->tutor;
                        @endphp

                        <div class="py-3 text-sm">
                            <div class="font-medium text-zinc-900">
                                {{ $tutor?->fullname ?? ($tutor?->nombre_completo ?? ($tutor?->nombre ?? 'Tutor sin nombre')) }}
                            </div>

                            <div class="mt-1 grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-zinc-500">
                                <div>
                                    Grado: {{ $tutor?->grado?->nombre ?? '—' }}
                                </div>

                                <div>
                                    Adscripción: {{ $tutor?->adscripcion?->nombre ?? '—' }}
                                </div>

                                <div>
                                    Correo: {{ $tutor?->email ?? '—' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-zinc-500">
                    No se encontró comité tutor registrado para la inscripción actual.
                </p>
            @endif

            <div class="mt-4 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-600">
                Esta información proviene de SIIAP y se muestra en modo solo lectura.
            </div>
        </x-ui.panel>
    @endif
</div>

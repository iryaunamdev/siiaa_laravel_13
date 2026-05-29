<div class="space-y-6">
    <x-ui.header title="Directorio institucional"
        description="Gestión de información pública y académica asociada a identidades institucionales." />

    <x-ui.panel>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-4">
                <x-ui.input wire:model.live.debounce.400ms="search" name="search" label="Buscar"
                    placeholder="Nombre, correo, área, ADS..." />
            </div>

            <div class="lg:col-span-3">
                <x-ui.select wire:model.live="tipo" name="tipo" label="Tipo" :options="[
                    'todos' => 'Todos',
                    'siiaa' => 'Todo el personal IRyA',
                    'investigador' => 'Investigadores',
                    'tecnico_academico' => 'Técnicos académicos',
                    'posdoctorado' => 'Posdoctorados',
                    'administrativo' => 'Administrativos',
                    'siiap_student' => 'Todos los estudiantes',
                    'estudiante_maestria' => 'Estudiantes de maestría',
                    'estudiante_doctorado' => 'Estudiantes de doctorado',
                    /*'servicio_social' => 'Servicio social',
                     'trabajador_sindicalizado' => 'Trabajadores sindicalizados',*/
                ]" />
            </div>

            <div class="lg:col-span-3">
                <x-ui.select wire:model.live="estadoPerfil" name="estadoPerfil" label="Estado" :options="[
                    'todos' => 'Todos',
                    'con_perfil_publico' => 'Con perfil público',
                    'sin_perfil_publico' => 'Sin perfil público',
                    'visible' => 'Visible',
                    'oculto' => 'Oculto/inactivo',
                ]" />
            </div>

            <div class="lg:col-span-2 flex justify-end">
                <x-ui.button type="button" variant="secondary" wire:click="resetFilters">
                    Limpiar
                </x-ui.button>
            </div>
        </div>
    </x-ui.panel>

    <x-ui.panel>
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
            <div class="space-y-1">
                <h2 class="text-base font-semibold text-zinc-900">
                    Registros del directorio
                </h2>

                <p class="text-sm text-zinc-500">
                    Mostrando identidades con información pública y académica asociada.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                @can('directorio.export')
                    <a href="{{ $this->exportUrl('csv') }}">
                        <x-ui.button type="button" variant="secondary" size="sm">
                            CSV
                        </x-ui.button>
                    </a>

                    <a href="{{ $this->exportUrl('xlsx') }}">
                        <x-ui.button type="button" variant="secondary" size="sm">
                            Excel
                        </x-ui.button>
                    </a>

                    <a href="{{ $this->exportUrl('json') }}">
                        <x-ui.button type="button" variant="secondary" size="sm">
                            JSON
                        </x-ui.button>
                    </a>
                @endcan

                @can('directorio.update')
                    @if ($editMode)
                        <x-ui.button type="button" size="sm" variant="primary" wire:click="saveVisibleRows"
                            wire:loading.attr="disabled" wire:target="saveVisibleRows">
                            Guardar cambios
                        </x-ui.button>
                    @endif

                    <x-ui.button type="button" size="sm" :variant="$editMode ? 'primary' : 'secondary'" wire:click="toggleEditMode">
                        {{ $editMode ? 'Modo edición activo' : 'Activar edición' }}
                    </x-ui.button>
                @endcan
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="min-w-[1500px]">
                <x-ui.table>
                    <x-ui.table.head>
                        <x-ui.table.row>
                            <x-ui.table.header>Nombre</x-ui.table.header>
                            <x-ui.table.header>Tipo</x-ui.table.header>
                            <x-ui.table.header>Estado institucional</x-ui.table.header>
                            <x-ui.table.header>Área / Area</x-ui.table.header>
                            <x-ui.table.header>Contacto</x-ui.table.header>
                            <x-ui.table.header>Académico / ADS</x-ui.table.header>
                            <x-ui.table.header>Publicación</x-ui.table.header>
                        </x-ui.table.row>
                    </x-ui.table.head>

                    <x-ui.table.body>
                        @forelse ($identidades as $identity)
                            @php
                                $registro = $directorioService->mapIdentity($identity);

                                $perfilPublico = $identity->perfilPublico;
                                $perfilAcademico = $identity->perfilAcademico;

                                $nombreCompleto = $registro['nombre_completo'] ?: 'Sin nombre';

                                $row = $rows[$identity->id] ?? null;

                                $tipoLabel = match ($identity->identity_type) {
                                    'siiaa' => 'Personal IRyA',
                                    'siiap_student' => 'Estudiante SIIAP',
                                    default => $identity->identity_type,
                                };

                                $estadoInstitucional = $registro['estado_institucional'];

                                $estadoVariant = match ($estadoInstitucional) {
                                    'vigente' => 'success',
                                    'gracia' => 'warning',
                                    'no_vigente' => 'danger',
                                    'no_encontrado' => 'danger',
                                    'activo' => 'success',
                                    'inactivo' => 'neutral',
                                    default => 'neutral',
                                };

                                $estadoLabel = match ($estadoInstitucional) {
                                    'vigente' => 'Inscrito',
                                    'gracia' => 'No inscrito',
                                    'no_vigente' => 'No vigente',
                                    'no_encontrado' => 'No encontrado',
                                    'activo' => 'Activo',
                                    'inactivo' => 'Inactivo',
                                    default => 'Sin estado',
                                };

                                $tipoLabel = $registro['directorio_tipo_label'];
                            @endphp

                            <x-ui.table.row>
                                <x-ui.table.cell>
                                    <div class="space-y-2 min-w-80">
                                        @if ($editMode && $row)
                                            <div class="grid grid-cols-1 gap-2">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <x-ui.input wire:model.defer="rows.{{ $identity->id }}.titulo_es"
                                                        name="titulo_es_{{ $identity->id }}" label="Título ES"
                                                        placeholder="Dr., Dra., M. en C..." />

                                                    <x-ui.input wire:model.defer="rows.{{ $identity->id }}.titulo_en"
                                                        name="titulo_en_{{ $identity->id }}" label="Título EN"
                                                        placeholder="Dr., MSc..." />
                                                </div>

                                                <x-ui.input wire:model.defer="rows.{{ $identity->id }}.nombre_publico"
                                                    name="nombre_publico_{{ $identity->id }}" label="Nombre público" />

                                                <x-ui.input
                                                    wire:model.defer="rows.{{ $identity->id }}.apellido_publico"
                                                    name="apellido_publico_{{ $identity->id }}"
                                                    label="Apellido público" />
                                            </div>
                                        @else
                                            <div class="font-medium text-zinc-900">
                                                {{ $nombreCompleto }}
                                            </div>

                                            @if ($registro['titulo_en'])
                                                <div class="text-xs text-zinc-500">
                                                    EN: {{ $registro['titulo_en'] }}
                                                </div>
                                            @endif

                                            <div class="text-xs text-zinc-500">
                                                ID {{ $identity->id }}

                                                @if (!$perfilPublico)
                                                    · datos base
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </x-ui.table.cell>

                                <x-ui.table.cell>
                                    <x-ui.badge variant="neutral">
                                        {{ $tipoLabel }}
                                    </x-ui.badge>
                                </x-ui.table.cell>

                                <x-ui.table.cell>
                                    <div class="space-y-1">
                                        <x-ui.badge :variant="$estadoVariant">
                                            {{ $estadoLabel }}
                                        </x-ui.badge>

                                        @if ($identity->identity_type === 'siiap_student' && $estadoInstitucional === 'gracia')
                                            <div class="text-xs text-amber-700">
                                                Sin inscripción actual.
                                            </div>
                                        @endif
                                    </div>
                                </x-ui.table.cell>

                                <x-ui.table.cell>
                                    <div class="space-y-2 min-w-72">
                                        @if ($editMode && $row)
                                            <x-ui.input wire:model.defer="rows.{{ $identity->id }}.area_es"
                                                name="area_es_{{ $identity->id }}" label="Área ES" />

                                            <x-ui.input wire:model.defer="rows.{{ $identity->id }}.area_en"
                                                name="area_en_{{ $identity->id }}" label="Área EN" />
                                        @else
                                            <div class="text-sm text-zinc-700">
                                                {{ $registro['area_es'] ?: 'Sin área' }}
                                            </div>

                                            @if ($registro['area_en'])
                                                <div class="text-xs text-zinc-500">
                                                    {{ $registro['area_en'] }}
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </x-ui.table.cell>

                                <x-ui.table.cell>
                                    <div class="space-y-2 min-w-80">
                                        @if ($editMode && $row)
                                            <div class="grid grid-cols-2 gap-2">
                                                <x-ui.input wire:model.defer="rows.{{ $identity->id }}.oficina"
                                                    name="oficina_{{ $identity->id }}" label="Oficina" />

                                                <x-ui.input
                                                    wire:model.defer="rows.{{ $identity->id }}.extension_red_unam"
                                                    name="extension_red_unam_{{ $identity->id }}" label="Red UNAM" />
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <x-ui.input
                                                    wire:model.defer="rows.{{ $identity->id }}.telefono_morelia"
                                                    name="telefono_morelia_{{ $identity->id }}" label="Tel. Morelia" />

                                                <x-ui.input wire:model.defer="rows.{{ $identity->id }}.telefono_cdmx"
                                                    name="telefono_cdmx_{{ $identity->id }}" label="Tel. CDMX" />
                                            </div>

                                            <x-ui.input wire:model.defer="rows.{{ $identity->id }}.email_publico"
                                                name="email_publico_{{ $identity->id }}" label="Email público"
                                                type="email" />

                                            <x-ui.input wire:model.defer="rows.{{ $identity->id }}.homepage_url"
                                                name="homepage_url_{{ $identity->id }}" label="Homepage"
                                                type="url" />
                                        @else
                                            <div class="text-sm text-zinc-700">
                                                {{ $registro['email_publico'] ?: 'Sin email público' }}
                                            </div>

                                            <div class="text-xs text-zinc-500">
                                                Oficina: {{ $registro['oficina'] ?: '—' }}
                                            </div>

                                            <div class="text-xs text-zinc-500">
                                                Red UNAM: {{ $registro['extension_red_unam'] ?: '—' }}
                                            </div>

                                            @if ($registro['telefono_morelia'] || $registro['telefono_cdmx'])
                                                <div class="text-xs text-zinc-500">
                                                    Morelia: {{ $registro['telefono_morelia'] ?: '—' }}
                                                    · CDMX: {{ $registro['telefono_cdmx'] ?: '—' }}
                                                </div>
                                            @endif

                                            @if ($registro['homepage_url'])
                                                <div class="text-xs text-zinc-500 truncate max-w-72">
                                                    {{ $registro['homepage_url'] }}
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </x-ui.table.cell>

                                <x-ui.table.cell>
                                    <div class="space-y-2 min-w-80">
                                        @if ($editMode && $row)
                                            <div class="grid grid-cols-2 gap-2">
                                                <x-ui.input wire:model.defer="rows.{{ $identity->id }}.orcid"
                                                    name="orcid_{{ $identity->id }}" label="ORCID" />

                                                <x-ui.input wire:model.defer="rows.{{ $identity->id }}.scopus_id"
                                                    name="scopus_id_{{ $identity->id }}" label="Scopus ID" />
                                            </div>

                                            <x-ui.input wire:model.defer="rows.{{ $identity->id }}.ads_author_query"
                                                name="ads_author_query_{{ $identity->id }}"
                                                label="ADS Author Query" />

                                            <x-ui.input wire:model.defer="rows.{{ $identity->id }}.ads_profile_url"
                                                name="ads_profile_url_{{ $identity->id }}" label="ADS Profile URL"
                                                type="url" />

                                            <x-ui.input wire:model.defer="rows.{{ $identity->id }}.ads_library_url"
                                                name="ads_library_url_{{ $identity->id }}" label="ADS Library URL"
                                                type="url" />

                                            <x-ui.textarea wire:model.defer="rows.{{ $identity->id }}.research_area"
                                                name="research_area_{{ $identity->id }}" label="Research area"
                                                rows="2" />

                                            <x-ui.textarea
                                                wire:model.defer="rows.{{ $identity->id }}.academic_keywords"
                                                name="academic_keywords_{{ $identity->id }}"
                                                label="Academic keywords" rows="2" />
                                        @else
                                            @if ($registro['orcid'])
                                                <div class="text-xs text-zinc-700">
                                                    ORCID: {{ $registro['orcid'] }}
                                                </div>
                                            @endif

                                            @if ($registro['ads_author_query'] || $registro['ads_profile_url'] || $registro['ads_library_url'])
                                                <x-ui.badge variant="success">
                                                    ADS capturado
                                                </x-ui.badge>
                                            @else
                                                <x-ui.badge variant="warning">
                                                    ADS pendiente
                                                </x-ui.badge>
                                            @endif

                                            @if ($registro['research_area'])
                                                <div class="text-xs text-zinc-500 line-clamp-2">
                                                    {{ $registro['research_area'] }}
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </x-ui.table.cell>

                                <x-ui.table.cell>
                                    @if ($perfilAcademico?->ads_author_query || $perfilAcademico?->ads_profile_url || $perfilAcademico?->ads_library_url)
                                        <x-ui.badge variant="success">
                                            ADS
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning">
                                            Pendiente
                                        </x-ui.badge>
                                    @endif
                                </x-ui.table.cell>

                                <x-ui.table.cell>
                                    <div class="space-y-2 min-w-40">
                                        @if ($editMode && $row)
                                            <label class="flex items-center gap-2 text-sm text-zinc-700">
                                                <input type="checkbox"
                                                    wire:model.defer="rows.{{ $identity->id }}.active"
                                                    class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                                                Activo
                                            </label>

                                            <label class="flex items-center gap-2 text-sm text-zinc-700">
                                                <input type="checkbox"
                                                    wire:model.defer="rows.{{ $identity->id }}.visible"
                                                    class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500">
                                                Visible
                                            </label>

                                            <x-ui.input wire:model.defer="rows.{{ $identity->id }}.sort_order"
                                                name="sort_order_{{ $identity->id }}" label="Orden" type="number"
                                                min="0" />

                                            <x-ui.button type="button" size="sm" variant="primary"
                                                wire:click="saveRow({{ $identity->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="saveRow({{ $identity->id }})">
                                                Guardar
                                            </x-ui.button>
                                        @else
                                            @if ($perfilPublico?->active && $perfilPublico?->visible)
                                                <x-ui.badge variant="success">
                                                    Visible
                                                </x-ui.badge>
                                            @elseif ($perfilPublico)
                                                <x-ui.badge variant="neutral">
                                                    Oculto
                                                </x-ui.badge>
                                            @else
                                                <x-ui.badge variant="warning">
                                                    Sin perfil
                                                </x-ui.badge>
                                            @endif
                                        @endif
                                    </div>
                                </x-ui.table.cell>
                            </x-ui.table.row>
                        @empty
                            <x-ui.table.row>
                                <x-ui.table.cell colspan="7">
                                    <x-ui.table.empty>
                                        No se encontraron registros para el directorio.
                                    </x-ui.table.empty>
                                </x-ui.table.cell>
                            </x-ui.table.row>
                        @endforelse
                    </x-ui.table.body>
                </x-ui.table>
            </div>
        </div>

        <div class="mt-4">
            {{ $identidades->links() }}
        </div>
    </x-ui.panel>
</div>

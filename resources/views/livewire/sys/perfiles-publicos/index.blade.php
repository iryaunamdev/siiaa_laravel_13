<div class="space-y-6">
    <x-ui.panel>

        <div class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-72">
                <x-ui.input label="Buscar" wire:model.live.debounce.400ms="search" placeholder="Nombre, correo o área" />
            </div>

            <div class="w-full sm:w-56">
                <x-ui.select label="Tipo de identidad" wire:model.live="identityType">
                    <option value="">Todos</option>
                    @foreach ($identityTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-ui.select>
            </div>

            <div class="w-full sm:w-44">
                <x-ui.select label="Visibilidad" wire:model.live="visibility">
                    <option value="">Todos</option>
                    <option value="visible">Visibles</option>
                    <option value="hidden">Ocultos</option>
                </x-ui.select>
            </div>

            <div class="w-full sm:w-44">
                <x-ui.select label="Estado" wire:model.live="status">
                    <option value="">Todos</option>
                    <option value="active">Activos</option>
                    <option value="inactive">Inactivos</option>
                </x-ui.select>
            </div>

            <x-ui.button variant="secondary" wire:click="resetFilters">
                Limpiar filtros
            </x-ui.button>
            <x-ui.button wire:click="openSyncModal">
                Sincronizar perfiles
            </x-ui.button>
        </div>
    </x-ui.panel>

    <x-ui.panel title="Perfiles públicos"
        description="Administra los perfiles públicos de los miembros de la comunidad UNAM.">
        <x-ui.table>
            <x-ui.table.head>
                <x-ui.table.row>
                    <x-ui.table.header>Perfil</x-ui.table.header>
                    <x-ui.table.header>Identidad</x-ui.table.header>
                    <x-ui.table.header>Área</x-ui.table.header>
                    <x-ui.table.header>Correo</x-ui.table.header>
                    <x-ui.table.header>Estado</x-ui.table.header>
                    <x-ui.table.header class="text-right">Acciones</x-ui.table.header>
                </x-ui.table.row>
            </x-ui.table.head>

            <x-ui.table.body>
                @forelse ($perfiles as $perfil)
                    <x-ui.table.row wire:key="perfil-{{ $perfil->id }}">
                        <x-ui.table.cell>
                            <div class="flex items-center gap-3">
                                <x-ui.avatar :name="$perfil->resolvedName()" :src="$perfil->resolvedPhotoUrl()" />

                                <div>
                                    <div class="font-medium text-zinc-900">
                                        {{ $perfil->resolvedName() ?? 'Sin nombre' }}
                                    </div>
                                    <div class="text-xs text-zinc-500">
                                        {{ $perfil->resolvedTitulo() ?? 'Sin título público' }}
                                    </div>
                                </div>
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="text-sm text-zinc-700">
                                {{ $perfil->identityLink?->identity_type }}
                            </div>
                            <div class="text-xs text-zinc-500">
                                ID: {{ $perfil->identity_link_id }}
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="max-w-xs text-sm text-zinc-700">
                                {{ $perfil->resolvedArea() ?? 'Sin área capturada' }}
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="text-sm text-zinc-700">
                                {{ $perfil->resolvedEmail() ?? 'Sin correo' }}
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell>
                            <div class="flex flex-col gap-1">
                                <x-ui.badge :variant="$perfil->active ? 'success' : 'secondary'">
                                    {{ $perfil->active ? 'Activo' : 'Inactivo' }}
                                </x-ui.badge>

                                <x-ui.badge :variant="$perfil->visible ? 'info' : 'warning'">
                                    {{ $perfil->visible ? 'Visible' : 'Oculto' }}
                                </x-ui.badge>
                            </div>
                        </x-ui.table.cell>

                        <x-ui.table.cell class="text-right">
                            <div class="flex justify-end gap-2">
                                <x-ui.button size="sm" variant="secondary"
                                    wire:click="toggleVisible({{ $perfil->id }})">
                                    {{ $perfil->visible ? 'Ocultar' : 'Publicar' }}
                                </x-ui.button>

                                <x-ui.button size="sm" variant="secondary"
                                    wire:click="toggleActive({{ $perfil->id }})">
                                    {{ $perfil->active ? 'Desactivar' : 'Activar' }}
                                </x-ui.button>

                                <x-ui.button size="sm" wire:click="edit({{ $perfil->id }})">
                                    Editar
                                </x-ui.button>
                            </div>
                        </x-ui.table.cell>
                    </x-ui.table.row>
                @empty
                    <x-ui.table.row>
                        <x-ui.table.cell colspan="6">
                            <div class="py-8 text-center text-sm text-zinc-500">
                                No se encontraron perfiles públicos.
                            </div>
                        </x-ui.table.cell>
                    </x-ui.table.row>
                @endforelse
            </x-ui.table.body>
        </x-ui.table>

        <div class="mt-4">
            {{ $perfiles->links() }}
        </div>
    </x-ui.panel>

    <x-ui.modal wire:model="profileModal" size="3xl">
        <x-slot:title>
            Editar perfil público
        </x-slot:title>

        <div class="grid gap-4 md:grid-cols-2">
            <x-ui.input label="Título ES" wire:model="titulo_es" />
            <x-ui.input label="Título EN" wire:model="titulo_en" />

            <x-ui.input label="Nombre público" wire:model="nombre_publico" />
            <x-ui.input label="Apellido público" wire:model="apellido_publico" />

            <x-ui.input label="Área ES" wire:model="area_es" />
            <x-ui.input label="Área EN" wire:model="area_en" />

            <x-ui.input label="Oficina" wire:model="oficina" />
            <x-ui.input label="Extensión Red UNAM" wire:model="extension_red_unam" />

            <x-ui.input label="Teléfono Morelia" wire:model="telefono_morelia" />
            <x-ui.input label="Teléfono CDMX" wire:model="telefono_cdmx" />

            <x-ui.input label="Correo público" wire:model="email_publico" />
            <x-ui.input label="Página personal" wire:model="homepage_url" />

            <div class="md:col-span-2">
                <x-ui.textarea label="Observaciones" wire:model="observaciones" rows="3" />
            </div>

            <x-ui.input type="number" label="Orden" wire:model="sort_order" />

            <div class="flex items-center gap-6 pt-6">
                <x-ui.checkbox wire:model="active" label="Activo" />
                <x-ui.checkbox wire:model="visible" label="Visible" />
            </div>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <x-ui.button variant="secondary" wire:click="closeModal">
                    Cancelar
                </x-ui.button>

                <x-ui.button wire:click="save" wire:loading.attr="disabled">
                    Guardar cambios
                </x-ui.button>
            </div>
        </x-slot:footer>
    </x-ui.modal>
</div>

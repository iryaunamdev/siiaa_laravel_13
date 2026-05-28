<div class="space-y-4">
    <x-ui.panel>
        <div class="px-2 py-3">
            <x-slot name="title">
                {{ trim(($persona->nombre ?? '') . ' ' . ($persona->apellidop ?? '') . ' ' . ($persona->apellidom ?? '')) }}
                @if ($persona->activo)
                    <x-ui.badge variant="success">Activo</x-ui.badge>
                @else
                    <x-ui.badge variant="neutral">Inactivo</x-ui.badge>
                @endif
            </x-slot>
            @role(['admin', 'super-admin'])
                <x-slot name="description">
                    <div class="text-xs text-zinc-300">ID SIIAA: {{ $persona->id }}</div>

                </x-slot>
            @endrole

            <x-slot name="actions">
                <x-ui.button type="button" variant="secondary" href="{{ route('personas.index') }}">
                    Volver al listado
                </x-ui.button>

                @can('personas.update')
                    <x-ui.button type="button" variant="primary" href="{{ route('personas.edit', $persona) }}">
                        Editar
                    </x-ui.button>
                @endcan
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <section class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <div>
                        @include('livewire.personas._show_datos-generales')
                    </div>
                </section>

                <section class="rounded-2xl border border-zinc-200 bg-white p-5">
                    {{-- Navegación por secciones --}}
                    <div class="mb-6 overflow-x-auto border-zinc-200">
                        <nav class="flex min-w-max gap-2">


                            <button type="button" wire:click="setSection('ingresos')"
                                class="border-b-2 px-3 py-2 text-sm font-medium transition
                        {{ $section === 'ingresos'
                            ? 'border-blue-600 text-zinc-900 border-b-2'
                            : 'border-transparent text-zinc-500 hover:border-b-2 hover:border-zinc-300 hover:text-zinc-800' }}">
                                Ingresos institucionales
                            </button>

                            <button type="button" wire:click="setSection('academico')"
                                class="border-b-2 px-3 py-2 text-sm font-medium transition
                        {{ $section === 'academico'
                            ? 'border-blue-600 text-zinc-900'
                            : 'border-transparent text-zinc-500 hover:border-b-2 hover:border-zinc-300 hover:text-zinc-800' }}">
                                Perfil académico
                            </button>

                            <button type="button" wire:click="setSection('publico')"
                                class="border-b-2 px-3 py-2 text-sm font-medium transition
                        {{ $section === 'publico'
                            ? 'border-blue-600 text-zinc-900'
                            : 'border-transparent text-zinc-500 hover:border-b-2 hover:border-zinc-300 hover:text-zinc-800' }}">
                                Perfil público
                            </button>

                            <button type="button" wire:click="setSection('posdoc_becas')"
                                class="border-b-2 px-3 py-2 text-sm font-medium transition
                        {{ $section === 'posdoc_becas'
                            ? 'border-blue-600 text-zinc-900'
                            : 'border-transparent text-zinc-500 hover:border-b-2 hover:border-zinc-300 hover:text-zinc-800' }}">
                                Becas posdoctorales
                            </button>
                        </nav>
                    </div>

                    {{-- Sección: Ingresos institucionales --}}
                    @if ($section === 'ingresos')
                        @include('livewire.personas._show_ingresos')
                    @endif

                    {{-- Sección: Perfil académico --}}
                    @if ($section === 'academico')
                        @include('livewire.personas._show_perfil-academico')
                    @endif

                    {{-- Sección: Perfil público --}}
                    @if ($section === 'publico')
                        @include('livewire.personas._show_perfil-publico')
                    @endif

                    {{-- Sección: Becas posdoctorales --}}
                    @if ($section === 'posdoc_becas')
                        @include('livewire.personas._show_becas-posdoctorales')
                    @endif

                </section>

            </div>
        </div>
    </x-ui.panel>
</div>

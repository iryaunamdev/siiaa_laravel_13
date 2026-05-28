<div>

    <x-ui.panel size="lg">
        <x-slot name="title">
            {{ $persona->fullname ? $persona->fullname : 'Editar persona' }}
            @if ($persona->activo)
                <x-ui.badge variant="success" textSize="text-xs">Activo</x-ui.badge>
            @else
                <x-ui.badge variant="neutral" textSize="text-xs">Inactivo</x-ui.badge>
            @endif
        </x-slot>

        <x-slot name="actions">
            <x-ui.button type="button" variant="secondary" href="{{ route('personas.index') }}">
                Volver al listado
            </x-ui.button>
        </x-slot>

        {{-- Navegación por secciones --}}
        <div class="mb-4 overflow-x-auto border-zinc-200 px-4">
            <nav class="flex min-w-max gap-4">
                <button type="button" wire:click="setSection('general')"
                    class="border-b-2 px-3 py-2 text-sm transition
                        {{ $section === 'general'
                            ? 'border-blue-600 text-zinc-900 border-b-2 font-medium'
                            : 'border-zinc-200 text-zinc-500 hover:border-b-2 hover:border-zinc-300 hover:text-zinc-800 hover:font-medium ' }}">
                    Datos generales
                </button>

                <button type="button" wire:click="setSection('ingresos')"
                    class="border-b-2 px-3 py-2 text-sm transition
                        {{ $section === 'ingresos'
                            ? 'border-blue-600 text-zinc-900 border-b-2 font-medium '
                            : 'border-zinc-200 text-zinc-500 hover:border-b-2 hover:border-zinc-300 hover:text-zinc-800 hover:font-medium' }}">
                    Ingresos institucionales
                </button>

                <button type="button" wire:click="setSection('academico')"
                    class="border-b-2 px-3 py-2 text-sm transition
                        {{ $section === 'academico'
                            ? 'border-blue-600 text-zinc-900 border-b-2 font-medium'
                            : 'border-zinc-200 text-zinc-500 hover:border-b-2 hover:border-zinc-300 hover:text-zinc-800 hover:font-medium' }}">
                    Perfil académico
                </button>

                <button type="button" wire:click="setSection('publico')"
                    class="border-b-2 px-3 py-2 text-sm transition
                        {{ $section === 'publico'
                            ? 'border-blue-600 text-zinc-900 border-b-2 font-medium'
                            : 'border-zinc-200 text-zinc-500 hover:border-b-2 hover:border-zinc-300 hover:text-zinc-800 hover:font-medium' }}">
                    Perfil público
                </button>

                @if ($persona->esPosdoctorado())
                    <button type="button" wire:click="setSection('posdoc_becas')"
                        class="border-b-2 px-3 py-2 text-sm transition
                            {{ $section === 'posdoc_becas'
                                ? 'border-blue-600 text-zinc-900 border-b-2 font-medium'
                                : 'border-zinc-200 text-zinc-500 hover:border-b-2 hover:border-zinc-300 hover:text-zinc-800 hover:font-medium' }}">
                        Becas posdoctorales
                    </button>
                @endif
            </nav>
        </div>

        {{-- Sección: Datos generales --}}
        @if ($section === 'general')
            <section class="space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between px-4 pb-3">
                    <h3 class="text-base font-semibold text-zinc-800">
                        Datos generales
                    </h3>
                    <p class="mt-1 text-sm text-zinc-500">

                    </p>
                </div>

                @include('livewire.personas._form_datos-generales')
            </section>
        @endif

        {{-- Sección: Ingresos institucionales --}}
        @if ($section === 'ingresos')
            <section class="space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between px-4 pb-3">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-800">
                            Ingresos institucionales
                        </h3>

                        <p class="mt-1 text-sm text-zinc-500">
                            Historial de ingresos, tipo de personal, vigencia y estatus institucional de la persona.
                        </p>
                    </div>

                    @can('personas.manage_ingresos')
                        <x-ui.button type="button" variant="primary" wire:click="createIngreso"
                            wire:loading.attr="disabled">
                            Agregar ingreso
                        </x-ui.button>
                    @endcan
                </div>
                @include('livewire.personas._form_ingresos')
            </section>
        @endif

        {{-- Sección: Perfil académico --}}
        @if ($section === 'academico')
            <section class="space-y-5 p">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between px-4 pb-3">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-800">
                            Perfil académico
                        </h3>

                        <p class="mt-1 text-sm text-zinc-500">
                            Información académica, identificadores bibliográficos, SNI, PRIDE y líneas de investigación.
                        </p>
                    </div>

                    @can('personas.manage_ingresos')
                        <x-ui.button type="button" variant="primary" wire:click="createIngreso"
                            wire:loading.attr="disabled">
                            Agregar ingreso
                        </x-ui.button>
                    @endcan
                </div>
                @include('livewire.personas._form_perfil-academico')
            </section>
        @endif

        {{-- Sección: Perfil público --}}
        @if ($section === 'publico')
            <section class="space-y-5 p">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between px-4 pb-3">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-800">
                            Perfil público
                        </h3>

                        <p class="mt-1 text-sm text-zinc-500">
                            Información visible en el directorio y sitio web institucional.
                        </p>
                    </div>
                </div>
                @include('livewire.personas._form_perfil-publico')
            </section>
        @endif

        {{-- Sección: Becas posdoctorales --}}
        @if ($section === 'posdoc_becas' && $persona->esPosdoctorado())
            <section class="space-y-5 p">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between px-4 pb-3">
                    <div>
                        <h3 class="text-base font-semibold text-zinc-800">
                            Becas posdoctorales
                        </h3>

                        <p class="mt-1 text-sm text-zinc-500">
                            Historial de becas asociadas a la estancia posdoctoral de la persona.
                        </p>
                    </div>

                    @can('personas.manage_posdoc_becas')
                        <x-ui.button type="button" variant="primary" wire:click="createPosdocBeca"
                            wire:loading.attr="disabled">
                            Agregar beca
                        </x-ui.button>
                    @endcan
                </div>
                @include('livewire.personas._form_becas-posdoctorales')
            </section>
        @endif
    </x-ui.panel>

    <x-ui.confirm-delete-modal model="deleteIngresoModal" title="Confirmar eliminación"
        message="Esta acción eliminará el ingreso institucional seleccionado. Si el modelo utiliza eliminación lógica, el historial podrá conservarse internamente."
        confirmText="Eliminar ingreso" cancelText="Cancelar" confirmAction="deleteIngresoConfirmed"
        cancelAction="resetDeleteIngresoForm" />
    <x-ui.confirm-delete-modal model="deletePosdocBecaModal" title="Confirmar eliminación"
        message="Esta acción eliminará la beca posdoctoral seleccionada. Si el modelo utiliza eliminación lógica, el historial podrá conservarse internamente."
        confirmText="Eliminar beca" cancelText="Cancelar" confirmAction="deletePosdocBecaConfirmed"
        cancelAction="resetDeletePosdocBecaForm" />

</div>

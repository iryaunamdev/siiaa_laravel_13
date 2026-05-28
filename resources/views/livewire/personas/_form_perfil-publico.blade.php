<div class="px-4 py-3">
    <form wire:submit.prevent="savePerfilPublico" class="rounded-2xl border border-zinc-200 bg-white px-4 py-3">
        <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex flex-wrap gap-2">
                @if ($persona->identityLink?->perfilPublico)
                    @if ($persona->identityLink->perfilPublico->visible)
                        <x-ui.badge variant="success">Visible</x-ui.badge>
                    @else
                        <x-ui.badge variant="neutral">No visible</x-ui.badge>
                    @endif

                    @if ($persona->identityLink->perfilPublico->active)
                        <x-ui.badge variant="success">Activo</x-ui.badge>
                    @else
                        <x-ui.badge variant="neutral">Inactivo</x-ui.badge>
                    @endif
                @else
                    <x-ui.badge variant="neutral">Sin perfil público</x-ui.badge>
                @endif
            </div>
        </div>

        @if (!$persona->identityLink)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                Esta persona todavía no tiene una identidad institucional resuelta. Para crear su perfil
                público, primero debe existir un registro en <strong>identity_links</strong>.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="col-span-4">
                    <x-ui.input wire:model.defer="titulo_es" name="titulo_es" label="Título en español"
                        placeholder="Ej. Técnico Académico" />
                </div>
                <div class="col-span-4">
                    <x-ui.input wire:model.defer="titulo_en" name="titulo_en" label="Título en inglés"
                        placeholder="Ej. Academic Technician" />
                </div>
                <div class="col-span-2">
                    <x-ui.input wire:model.defer="sort_order" name="sort_order" type="number" min="0"
                        label="Orden de aparición" />
                </div>
            </div>


            <div class="grid gap-4 md:grid-cols-3 mt-4">
                <x-ui.input wire:model.defer="nombre_publico" name="nombre_publico" label="Nombre público" />

                <x-ui.input wire:model.defer="apellido_publico" name="apellido_publico" label="Apellido(s) público" />

                <x-ui.input wire:model.defer="email_publico" name="email_publico" type="email"
                    label="Correo electrónico público" />
            </div>

            <hr class="my-6">

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <x-ui.textarea wire:model.defer="area_es" name="area_es"
                        label="Área de investigación / descripción (español)" rows="3" />
                </div>

                <div>
                    <x-ui.textarea wire:model.defer="area_en" name="area_en"
                        label="Área de investigación / descripción (inglés)" rows="3" />
                </div>
            </div>

            <hr class="my-6">

            <div class="grid gap-4 md:grid-cols-12">
                <div class="md:col-span-1">
                    <x-ui.input wire:model.defer="oficina" name="oficina" label="Oficina" />
                </div>

                <div class="md:col-span-2">
                    <x-ui.input wire:model.defer="extension_red_unam" name="extension_red_unam" label="Ext. RedUNAM" />
                </div>

                <div class="md:col-span-2">
                    <x-ui.input wire:model.defer="telefono_morelia" name="telefono_morelia" label="Tel. Morelia" />
                </div>

                <div class="md:col-span-2">
                    <x-ui.input wire:model.defer="telefono_cdmx" name="telefono_cdmx" label="Tel. CDMX" />
                </div>

                <div class="md:col-span-5">
                    <x-ui.input wire:model.defer="homepage_url" name="homepage_url" type="url"
                        label="Sitio web personal" placeholder="https://..." />
                </div>
            </div>

            <hr class="my-6">

            <div class="flex flex-col gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
                    <input type="checkbox" wire:model.defer="perfil_publico_active"
                        class="rounded border-zinc-300 text-zinc-800 shadow-sm focus:ring-zinc-500">
                    Perfil público activo
                </label>

                <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
                    <input type="checkbox" wire:model.defer="perfil_publico_visible"
                        class="rounded border-zinc-300 text-zinc-800 shadow-sm focus:ring-zinc-500">
                    Mostrar en directorio público
                </label>
            </div>

            <div class="mt-6">
                <x-ui.textarea wire:model.defer="perfil_publico_observaciones" name="perfil_publico_observaciones"
                    label="Observaciones" rows="3" />
            </div>
        @endif

        @if ($persona->identityLink)
            <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end mt-4">
                <x-ui.button type="button" variant="secondary" wire:click="fillPerfilPublicoForm">
                    Descartar cambios
                </x-ui.button>

                <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled"
                    wire:target="savePerfilPublico">
                    Guardar perfil público
                </x-ui.button>
            </div>
        @endif
    </form>
</div>

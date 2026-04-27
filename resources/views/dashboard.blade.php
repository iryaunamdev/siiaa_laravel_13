<x-layouts::app :title="__('Dashboard')">
    {{-- <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div> --}}
    <x-ui.button>Guardar</x-ui.button>
    <x-ui.input-date name="fecha_inicio" label="Fecha de inicio" wire:model="fecha_inicio" />
    <div x-data="{ open: true }">
        <x-ui.button @click="open = !open">toggle</x-ui.button>

        <div x-show="open" x-collapse>
            contenido
        </div>
    </div>
    <hr />
    <x-ui.accordion variant="form" multiple persist persist-key="users.form.sections">
        <x-ui.accordion-item name="general" title="Datos generales" description="Información básica del usuario." open>
            {{-- campos del formulario --}}
        </x-ui.accordion-item>

        <x-ui.accordion-item name="security" title="Seguridad y acceso"
            description="Credenciales, estado y autenticación.">
            {{-- campos de seguridad --}}
        </x-ui.accordion-item>
    </x-ui.accordion>


</x-layouts::app>

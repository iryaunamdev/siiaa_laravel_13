<form wire:submit.prevent="savePosdocBeca" class="rounded-2xl border border-zinc-200 bg-white px-4 py-3 mb-6">
    <div class="mb-5">
        <h4 class="text-sm font-semibold text-zinc-800">
            {{ $posdocBecaId ? 'Editar beca posdoctoral' : 'Nueva beca posdoctoral' }}
        </h4>

        <p class="mt-1 text-sm text-zinc-500">
            Captura el tipo de beca, periodo, asesor asociado y estado de la estancia.
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-12">
        <div class="col-span-4">
            <x-ui.select wire:model.defer="beca_id" name="beca_id" label="Beca" placeholder="Selecciona el tipo de beca"
                :options="$c_posdocBecas" required />
        </div>

        <div class="col-span-4">
            <x-ui.select wire:model.defer="asesor_id" name="asesor_id" label="Asesor"
                placeholder="Selecciona tutor (IRyA)" :options="$asesores" />
        </div>
        <div class="col-span-2">
            <x-ui.input wire:model.defer="beca_fecha_inicio" name="beca_fecha_inicio" type="date"
                label="Fecha de inicio" />
        </div>
        <div class="col-span-2">
            <x-ui.input wire:model.defer="beca_fecha_fin" name="beca_fecha_fin" type="date" label="Fecha de fin" />
        </div>
    </div>


    <div class="flex flex-col gap-3 mt-6">
        <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
            <input type="checkbox" wire:model.defer="beca_principal"
                class="rounded border-zinc-300 text-zinc-800 shadow-sm focus:ring-zinc-500">
            Marcar como beca principal
        </label>

        <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
            <input type="checkbox" wire:model.defer="beca_activo"
                class="rounded border-zinc-300 text-zinc-800 shadow-sm focus:ring-zinc-500">
            Beca activa
        </label>
    </div>

    <div class="mt-6">
        <x-ui.textarea wire:model.defer="beca_observaciones" name="beca_observaciones" label="Observaciones"
            rows="3" />
    </div>


    <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        <x-ui.button type="button" variant="secondary" wire:click="resetPosdocBecaForm">
            Cancelar
        </x-ui.button>

        <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="savePosdocBeca">
            Guardar beca
        </x-ui.button>
    </div>
</form>

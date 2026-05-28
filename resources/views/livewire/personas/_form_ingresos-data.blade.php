<div class="flex flex-col gap-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-ui.input wire:model.defer="numero_trabajador" name="numero_trabajador" label="Número de trabajador" />

        <x-ui.input wire:model.defer="cuv" name="cuv" label="CUV" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <x-ui.select wire:model.defer="tipo_personal_id" name="tipo_personal_id" label="Tipo de personal"
            placeholder="Selecciona una opción" :options="$c_tiposPersonal" required />

        <x-ui.select wire:model.defer="nombramiento_id" name="nombramiento_id" label="Nombramiento"
            placeholder="Selecciona una opción" :options="$c_nombramientos" />

        <x-ui.select wire:model.defer="contrato_id" name="contrato_id" label="Tipo de contrato"
            placeholder="Selecciona una opción" :options="$c_contratos" />

        <x-ui.select wire:model.defer="escolaridad_id" name="escolaridad_id" label="Escolaridad"
            placeholder="Selecciona una opción" :options="$c_escolaridades" />

        <x-ui.input wire:model.defer="fecha_ingreso" name="fecha_ingreso" type="date" label="Fecha de ingreso" />

        <x-ui.input wire:model.defer="fecha_nombramiento" name="fecha_nombramiento" type="date"
            label="Fecha de nombramiento" />

        <x-ui.input wire:model.defer="fecha_definitividad" name="fecha_definitividad" type="date"
            label="Fecha de definitividad" />

        <x-ui.input wire:model.defer="fecha_baja" name="fecha_baja" type="date" label="Fecha de baja" />
    </div>

    <div class="flex flex-col md:col-span-2 xl:col-span-3">
        <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
            <input type="checkbox" wire:model.defer="ingreso_principal"
                class="rounded border-zinc-300 text-zinc-800 shadow-sm focus:ring-zinc-500">
            Marcar como ingreso principal
        </label>

        <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
            <input type="checkbox" wire:model.defer="ingreso_activo"
                class="rounded border-zinc-300 text-zinc-800 shadow-sm focus:ring-zinc-500">
            Ingreso activo
        </label>
    </div>

    <div class="flex gap-2 sm:flex-row sm:justify-end">
        <x-ui.button type="button" variant="secondary" wire:click="resetIngresoForm">
            Cancelar
        </x-ui.button>

        <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="saveIngreso">
            Guardar cambios
        </x-ui.button>
    </div>
</div>

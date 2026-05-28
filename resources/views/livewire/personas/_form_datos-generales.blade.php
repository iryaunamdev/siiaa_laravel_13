<div class="px-4 py-3">
    <form wire:submit.prevent="saveGeneral" class="rounded-2xl border border-zinc-200 bg-white px-4 py-3">
        <div class="grid gap-4 md:grid-cols-3 ">
            <x-ui.input wire:model.defer="nombre" name="nombre" label="Nombre" required />

            <x-ui.input wire:model.defer="apellidop" name="apellidop" label="Apellido paterno" />

            <x-ui.input wire:model.defer="apellidom" name="apellidom" label="Apellido materno" />

            <x-ui.input wire:model.defer="email" name="email" type="email" label="Correo electrónico" />

            <x-ui.input wire:model.defer="curp" name="curp" label="CURP" maxlength="18" />

            <x-ui.input wire:model.defer="rfc" name="rfc" label="RFC" maxlength="13" />

            <div class="grid md:grid-cols-2 gap-4">
                <x-ui.input wire:model.defer="fecha_nacimiento" name="fecha_nacimiento" type="date"
                    label="Fecha de nacimiento" />

                <x-ui.select wire:model.defer="sexo_id" name="sexo_id" label="Sexo" placeholder="Selecciona"
                    :options="$c_sexos" />
            </div>

            <x-ui.select wire:model.defer="nacionalidad_id" name="nacionalidad_id" label="Nacionalidad"
                placeholder="Selecciona una opción" :options="$c_nacionalidades" />

            <div class="md:col-span-2 xl:col-span-3">
                <x-ui.checkbox wire:model.defer="activo" name="activo" label="Persona activa" />
            </div>

            <div class="col-span-full flex flex-col-reverse gap-2 sm:flex-row justify-end">
                <x-ui.button type="button" variant="secondary" wire:click="fillForm">
                    Descartar cambios
                </x-ui.button>

                <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="saveGeneral">
                    Guardar cambios
                </x-ui.button>
            </div>
        </div>
    </form>
</div>

 {{-- Modal crear / editar --}}
 <x-ui.modal wire:model="formModal" maxWidth="3xl">
     <x-slot:title>
         {{ $personaId ? 'Editar persona' : 'Registrar persona' }}
     </x-slot:title>

     <div class="grid gap-4 md:grid-cols-2 p-6">
         <x-ui.input wire:model.defer="nombre" name="nombre" label="Nombre" required />

         <x-ui.input wire:model.defer="apellidop" name="apellidop" label="Apellido paterno" />

         <x-ui.input wire:model.defer="apellidom" name="apellidom" label="Apellido materno" />

         <x-ui.input wire:model.defer="email" name="email" type="email" label="Correo electrónico" />

         <x-ui.input wire:model.defer="curp" name="curp" label="CURP" maxlength="18" />

         <x-ui.input wire:model.defer="rfc" name="rfc" label="RFC" maxlength="13" />

         <x-ui.input wire:model.defer="fecha_nacimiento" name="fecha_nacimiento" type="date"
             label="Fecha de nacimiento" />

         {{-- Estos selects quedan preparados. Después los conectamos a catálogos reales. --}}
         <x-ui.input wire:model.defer="sexo_id" name="sexo_id" type="number" label="Sexo ID" />

         <x-ui.input wire:model.defer="nacionalidad_id" name="nacionalidad_id" type="number" label="Nacionalidad ID" />

         <div class="md:col-span-2">
             <x-ui.textarea wire:model.defer="observaciones" name="observaciones" label="Observaciones"
                 rows="3" />
         </div>

         <div class="md:col-span-2">
             <label class="inline-flex items-center gap-2 text-sm text-zinc-700">
                 <input type="checkbox" wire:model.defer="activo"
                     class="rounded border-zinc-300 text-zinc-800 shadow-sm focus:ring-zinc-500">
                 Persona activa
             </label>
         </div>
     </div>

     <x-slot:footer>
         <div class="flex justify-end gap-2">
             <x-ui.button type="button" variant="secondary" wire:click="$set('formModal', false)">
                 Cancelar
             </x-ui.button>

             <x-ui.button type="button" variant="primary" wire:click="save" wire:loading.attr="disabled"
                 wire:target="save">
                 Guardar
             </x-ui.button>
         </div>
     </x-slot:footer>
 </x-ui.modal>

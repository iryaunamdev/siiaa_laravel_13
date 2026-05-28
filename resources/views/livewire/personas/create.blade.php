<x-ui.panel size="lg" title="Registro de personal IRyA"
    description="Captura los datos generales de la persona. Después de guardar, podrás completar
                    su información institucional, ingresos, perfil académico, perfil público y otros
                    datos relacionados.">
    <x-slot name="actions">
        <x-ui.button type="button" variant="secondary" href="{{ route('personas.index') }}">
            Cancelar
        </x-ui.button>
    </x-slot>

    {{-- Formulario --}}
    <form wire:submit.prevent="save" class="space-y-6 px-4 py-3">
        @include('livewire.personas._form_datos-generales')
        <div class="flex justify-end">
            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                Guardar y continuar
            </x-ui.button>
        </div>
    </form>
</x-ui.panel>

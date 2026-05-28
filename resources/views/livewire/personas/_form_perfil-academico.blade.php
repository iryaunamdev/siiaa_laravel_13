<div class="px-4 py-3">
    <form wire:submit.prevent="savePerfilAcademico" class="rounded-2xl border border-zinc-200 bg-white px-4 py-3">
        <div class="grid gap-4 grid-cols-1 md:grid-cols-4">
            <x-ui.select wire:model.defer="sni_id" name="sni_id" label="SNI" placeholder="Selecciona una opción"
                :options="$c_sni" />

            <x-ui.input wire:model.defer="sni_vigencia" name="sni_vigencia" type="date" label="Vigencia SNI" />

            <x-ui.select wire:model.defer="pride_id" name="pride_id" label="PRIDE" placeholder="Selecciona una opción"
                :options="$c_pride" />

            <x-ui.input wire:model.defer="pride_vigencia" name="pride_vigencia" type="date" label="Vigencia PRIDE" />
        </div>

        <div class="grid gap-4 grid-cols-1 md:grid-cols-3 mt-6">
            <x-ui.input wire:model.defer="orcid" name="orcid" label="ORCID" placeholder="0000-0000-0000-0000" />

            <x-ui.input wire:model.defer="scopus_id" name="scopus_id" label="Scopus ID" />

            <x-ui.input wire:model.defer="ads_author_query" name="ads_author_query" label="Consulta de autor ADS"
                placeholder='Ej. author:"Apellido, Nombre"' />
        </div>

        <div class="grid gap-4 grid-cols-1 md:grid-cols-2 mt-6">
            <div>
                <x-ui.input wire:model.defer="ads_profile_url" name="ads_profile_url" type="url"
                    label="URL de perfil ADS" />
            </div>

            <div>
                <x-ui.input wire:model.defer="ads_library_url" name="ads_library_url" type="url"
                    label="URL de biblioteca ADS" />
            </div>

            <div>
                <x-ui.textarea wire:model.defer="research_area" name="research_area" label="Área de investigación"
                    rows="3" />
            </div>

            <div>
                <x-ui.textarea wire:model.defer="academic_keywords" name="academic_keywords"
                    label="Palabras clave académicas" rows="3" />
            </div>
        </div>

        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end mt-4">
            <x-ui.button type="button" variant="secondary" wire:click="fillPerfilAcademicoForm">
                Descartar cambios
            </x-ui.button>

            <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled"
                wire:target="savePerfilAcademico">
                Guardar cambios
            </x-ui.button>
        </div>
    </form>
</div>

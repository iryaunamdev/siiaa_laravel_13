@if ($perfilAcademico)
    <section class="border border-zinc-200 rounded-xl hover:bg-zinc-50/70 px-4 py-3">
        <div class="flex justify-between">
            <div class="text-sm">
                {{ $perfilAcademico->pride?->nombre ?? 'Sin PRIDE' }}
                <div class="mt-1 text-sm">
                    <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">Vigencia PRIDE</span><br>
                    {{ $perfilAcademico->pride_vigencia?->format('d/m/Y') ?? '---' }}
                </div>
            </div>
            <div class="text-sm">
                {{ $perfilAcademico->sni?->nombre ?? 'Sin SNI' }}
                <div class="mt-1 text-sm">
                    <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">Vigencia SNI</span><br>
                    {{ $perfilAcademico->sni_vigencia?->format('d/m/Y') ?? '---' }}
                </div>
            </div>
            <div class="mt-1 text-sm">
                <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">Área de investigación</span><br>
                {{ $perfilAcademico->research_area ?? '---' }}
            </div>
        </div>
        <div class="flex justify-between mt-2">
            <div class="mt-1 text-sm">
                <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">ORCID</span><br>
                {{ $perfilAcademico->orcid ?: '---' }}
            </div>
            <div class="mt-1 text-sm">
                <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">ID Scopus</span><br>
                {{ $perfilAcademico->scopus_id ?: '---' }}
            </div>
            <div class="mt-1 text-sm">
                <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">Biblioteca ADS</span><br>
                {{ $perfilAcademico->ads_library_url ?: '---' }}
            </div>
        </div>
    </section>
@else
    <div class="rounded-xl border border-dashed border-zinc-200 bg-zinc-50 px-4 py-8 text-center">
        <p class="text-sm font-medium text-zinc-600">
            No hay perfil académico registrado.
        </p>

        <p class="mt-1 text-xs text-zinc-400">
            Cuando se capture el perfil académico desde la edición de persona, aparecerá en esta sección.
        </p>
    </div>
@endif

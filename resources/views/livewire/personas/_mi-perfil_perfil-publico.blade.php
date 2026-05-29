<x-ui.panel>
    <x-slot:title>Perfil público</x-slot:title>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Título ES</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->titulo_es ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Título EN</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->titulo_en ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Nombre público</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->nombre_publico ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Apellido público</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->apellido_publico ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Área ES</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->area_es ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Área EN</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->area_en ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Oficina</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->oficina ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Extensión Red UNAM</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->extension_red_unam ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Teléfono Morelia</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->telefono_morelia ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Teléfono CDMX</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->telefono_cdmx ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Correo público</div>
            <div class="font-medium text-zinc-900">{{ $perfilPublico?->email_publico ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">URL personal</div>
            <div class="font-medium text-zinc-900 break-all">{{ $perfilPublico?->homepage_url ?? '—' }}</div>
        </div>

        <div>
            <div class="text-xs uppercase tracking-wide text-zinc-500">Publicado</div>
            <div class="font-medium text-zinc-900">
                @if ($perfilPublico?->active && $perfilPublico?->visible)
                    Visible
                @elseif ($perfilPublico)
                    No publicado
                @else
                    Sin perfil público
                @endif
            </div>
        </div>

        <div class="md:col-span-4">
            <div class="text-xs uppercase tracking-wide text-zinc-500">Semblanza / observaciones</div>
            <div class="font-medium text-zinc-900 whitespace-pre-line">
                {{ $perfilPublico?->observaciones ?? '—' }}
            </div>
        </div>
    </div>

    @if (!empty($readonlyNotice))
        <div class="mt-4 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-600">
            {{ $readonlyNotice }}
        </div>
    @endif
</x-ui.panel>

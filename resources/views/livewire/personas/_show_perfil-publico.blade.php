@if ($perfilPublico)
    <section class="rounded-2xl border border-zinc-200 bg-white p-5">

        <div class="mb-5 flex flex-wrap gap-2">
            @if ($perfilPublico->visible)
                <x-ui.badge variant="success" text="Visible" />
            @else
                <x-ui.badge variant="neutral" text="No visible" />
            @endif

            @if ($perfilPublico->active)
                <x-ui.badge variant="success" text="Activo" />
            @else
                <x-ui.badge variant="neutral" text="Inactivo" />
            @endif
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Título en español
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->titulo_es ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Título en inglés
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->titulo_en ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Orden de aparición
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->sort_order ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Nombre público
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->nombre_publico ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Apellido público
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->apellido_publico ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Correo público
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->email_publico ?: '—' }}
                </p>
            </div>

            <div class="md:col-span-2 xl:col-span-3">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Área / descripción en español
                </p>
                <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-zinc-700">
                    {{ $perfilPublico->area_es ?: '—' }}
                </p>
            </div>

            <div class="md:col-span-2 xl:col-span-3">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Área / descripción en inglés
                </p>
                <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-zinc-700">
                    {{ $perfilPublico->area_en ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Oficina
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->oficina ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Extensión RedUNAM
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->extension_red_unam ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Teléfono Morelia
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->telefono_morelia ?: '—' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Teléfono CDMX
                </p>
                <p class="mt-1 text-sm text-zinc-700">
                    {{ $perfilPublico->telefono_cdmx ?: '—' }}
                </p>
            </div>

            <div class="md:col-span-2 xl:col-span-3">
                <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                    Sitio web personal
                </p>

                @if ($perfilPublico->homepage_url)
                    <a href="{{ $perfilPublico->homepage_url }}" target="_blank" rel="noopener noreferrer"
                        class="mt-1 inline-block break-all text-sm text-blue-600 hover:underline">
                        {{ $perfilPublico->homepage_url }}
                    </a>
                @else
                    <p class="mt-1 text-sm text-zinc-700">—</p>
                @endif
            </div>

            @if ($perfilPublico->observaciones)
                <div class="md:col-span-2 xl:col-span-3">
                    <p class="text-xs font-medium uppercase tracking-wide text-zinc-400">
                        Observaciones
                    </p>
                    <p class="mt-1 whitespace-pre-line text-sm leading-relaxed text-zinc-700">
                        {{ $perfilPublico->observaciones }}
                    </p>
                </div>
            @endif
        </div>

    </section>
@else
    <div class="rounded-xl border border-dashed border-zinc-200 bg-zinc-50 px-4 py-8 text-center">
        <p class="text-sm font-medium text-zinc-600">
            No hay perfil público registrado.
        </p>

        <p class="mt-1 text-xs text-zinc-400">
            El perfil público se crea desde la identidad institucional de la persona.
        </p>
    </div>
@endif

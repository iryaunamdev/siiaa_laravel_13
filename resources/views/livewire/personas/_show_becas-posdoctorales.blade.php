@if ($persona->esPosdoctorado())
    <section class="rounded-2xl border border-zinc-200 bg-white p-5">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h3 class="text-base font-semibold text-zinc-800">
                    Becas posdoctorales
                </h3>

                <p class="mt-1 text-sm text-zinc-500">
                    Historial de becas asociadas a la estancia posdoctoral de la persona.
                </p>
            </div>

            @can('personas.manage_posdoc_becas')
                <x-ui.button href="{{ route('personas.edit', $persona) }}?section=posdoc_becas" variant="secondary"
                    size="sm">
                    Editar becas
                </x-ui.button>
            @endcan
        </div>

        @if ($posdocBecas->isNotEmpty())
            <div class="overflow-x-auto rounded-xl border border-zinc-200">
                <table class="min-w-full divide-y divide-zinc-200 text-sm">
                    <thead class="bg-zinc-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-zinc-600">
                                Beca
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-zinc-600">
                                Asesor
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-zinc-600">
                                Periodo
                            </th>

                            <th class="px-4 py-3 text-center font-semibold text-zinc-600">
                                Estado
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-100 bg-white">
                        @foreach ($posdocBecas as $posdocBeca)
                            <tr class="hover:bg-zinc-50/70">
                                <td class="px-4 py-3 align-top">
                                    <div class="font-medium text-zinc-800">
                                        {{ $posdocBeca->beca?->nombre ?? '—' }}
                                    </div>

                                    @if ($posdocBeca->observaciones)
                                        <div class="mt-1 max-w-md text-xs leading-relaxed text-zinc-400">
                                            {{ $posdocBeca->observaciones }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-3 align-top text-zinc-600">
                                    {{ $posdocBeca->asesor?->fullname ?? '—' }}
                                </td>

                                <td class="px-4 py-3 align-top text-zinc-600">
                                    <div>
                                        <span class="font-medium text-zinc-700">Inicio:</span>
                                        {{ $posdocBeca->fecha_inicio ? $posdocBeca->fecha_inicio->format('d/m/Y') : '—' }}
                                    </div>

                                    <div class="mt-1 text-xs text-zinc-500">
                                        <span class="font-medium">Fin:</span>
                                        {{ $posdocBeca->fecha_fin ? $posdocBeca->fecha_fin->format('d/m/Y') : 'Actual / sin fecha' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 align-top">
                                    <div class="flex flex-col items-center gap-2">
                                        @if ($posdocBeca->principal)
                                            <x-ui.badge variant="success" text="Principal" />
                                        @endif

                                        @if ($posdocBeca->activo)
                                            <x-ui.badge variant="success" text="Activo" />
                                        @else
                                            <x-ui.badge variant="neutral" text="Inactivo" />
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-zinc-200 bg-zinc-50 px-4 py-8 text-center">
                <p class="text-sm font-medium text-zinc-600">
                    No hay becas posdoctorales registradas.
                </p>

                <p class="mt-1 text-xs text-zinc-400">
                    Cuando se capture una beca desde la edición de persona, aparecerá en esta sección.
                </p>
            </div>
        @endif
    </section>
@else
    <section class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50/60 p-6">
        <h3 class="text-base font-semibold text-zinc-800">
            Becas posdoctorales
        </h3>

        <p class="mt-2 text-sm text-zinc-500">
            Esta sección solo aplica para personas con ingreso principal de tipo posdoctoral.
        </p>
    </section>
@endif

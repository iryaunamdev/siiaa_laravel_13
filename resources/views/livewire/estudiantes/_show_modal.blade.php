<x-ui.modal wire:model="showResumenModal" size="5xl">
    @if ($selectedEstudiante)
        <div class="space-y-4 px-4 py-3">

            <section class="pt-4">
                <div class="flex justify-between">
                    <h3 class="text-lg font-semibold text-zinc-800">
                        {{ $selectedEstudiante->fullname }}
                    </h3>

                    <p class="text-sm text-zinc-500">
                        ID SIIAP: {{ $selectedEstudiante->id }}

                    </p>
                </div>
                <div class="text-sm font-light text-blue-500">
                    {{ $selectedEstudiante?->email }}
                </div>
            </section>

            <section>
                <h4 class="text-[0.65rem] font-semibold uppercase text-zinc-700 mb-3">
                    Última inscripción
                </h4>

                @if ($ultimaInscripcion)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <div class="text-zinc-500">Semestre</div>
                            <div class="font-medium text-zinc-800">
                                {{ $ultimaInscripcion->semestre?->nombre ?? 'No registrado' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-zinc-500">Grado</div>
                            <div class="font-medium text-zinc-800">
                                {{ $ultimaInscripcion->grado?->nombre ?? 'No registrado' }}
                            </div>
                        </div>

                        <div class="col-span-2">
                            <div class="text-zinc-500">Adscripción</div>
                            <div class="font-medium text-zinc-800">
                                {{ $ultimaInscripcion->adscripcion?->nombre ?? 'No registrada' }}
                            </div>
                        </div>
                    </div>
                @else
                    <x-ui.alert variant="warning">
                        No se encontró inscripción reciente para este estudiante.
                    </x-ui.alert>
                @endif
            </section>

            <section>
                <h4 class="text-[0.65rem] font-semibold uppercase text-zinc-700 mb-3">
                    Comité tutor
                </h4>

                @if ($comiteReciente->isNotEmpty())
                    <div class="border border-zinc-100 rounded-xl overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-zinc-50 uppercase text-zinc-500 text-[0.65rem]">
                                <tr>
                                    <th class="text-left px-4 py-2">Tutor</th>
                                    <th class="text-left px-4 py-2">Adscripción</th>
                                    <th class="text-left px-4 py-2"></th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-zinc-100">
                                @foreach ($comiteReciente as $integrante)
                                    <tr>
                                        <td class="px-4 py-2 text-zinc-800">
                                            {{ $integrante->tutor?->fullname ?? 'Sin nombre' }}
                                        </td>

                                        <td class="px-4 py-2 italic text-zinc-600">
                                            {{ $integrante->tutor?->adscripcion?->nombre ?? 'No registrada' }}
                                        </td>

                                        <td class="px-4 py-2">
                                            @if ($integrante->principal)
                                                <x-ui.badge variant="success"
                                                    textSize="text-[0.65rem]">Principal</x-ui.badge>
                                            @else
                                                <x-ui.badge variant="secondary"
                                                    textSize="text-[0.65rem]">Comité</x-ui.badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-ui.alert variant="warning">
                        No se encontró comité tutor asociado a la inscripción más reciente.
                    </x-ui.alert>
                @endif
            </section>

            <section class="hidden">
                <h4 class="text-sm font-semibold text-zinc-700 mb-3">
                    Historial de inscripciones
                </h4>

                @if ($historialInscripciones->isNotEmpty())
                    <div class="border border-zinc-100 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 text-zinc-500">
                                <tr>
                                    <th class="text-left px-4 py-2">Semestre</th>
                                    <th class="text-left px-4 py-2">Grado</th>
                                    <th class="text-left px-4 py-2">Programa</th>
                                    <th class="text-left px-4 py-2">Adscripción</th>
                                    <th class="text-left px-4 py-2">Comité</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-zinc-100">
                                @foreach ($historialInscripciones as $inscripcion)
                                    <tr>
                                        <td class="px-4 py-2 text-zinc-800">
                                            {{ $inscripcion->semestre?->nombre ?? '—' }}
                                        </td>

                                        <td class="px-4 py-2 text-zinc-600">
                                            {{ $inscripcion->grado?->nombre ?? '—' }}
                                        </td>

                                        <td class="px-4 py-2 text-zinc-600">
                                            {{ $inscripcion->programa?->nombre ?? '—' }}
                                        </td>

                                        <td class="px-4 py-2 text-zinc-600">
                                            {{ $inscripcion->adscripcion?->nombre ?? '—' }}
                                        </td>

                                        <td class="px-4 py-2 text-zinc-600">
                                            {{ $inscripcion->comite?->count() ?? 0 }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <x-ui.alert variant="info">
                        No se encontró historial de inscripciones.
                    </x-ui.alert>
                @endif
            </section>
        </div>

    @endif

    <x-slot:footer>
        <x-ui.button type="button" variant="secondary" wire:click="closeResumen">
            Cerrar
        </x-ui.button>
    </x-slot:footer>
</x-ui.modal>

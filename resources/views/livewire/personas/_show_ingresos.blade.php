<section>
    @if ($ingresos->isNotEmpty())
        @foreach ($ingresos as $ingreso)
            <div class="border border-zinc-200 rounded-xl hover:bg-zinc-50/70 px-4 py-3">
                <div class="flex justify-between">
                    <div>
                        <div class="font-medium text-zinc-800">
                            {{ $ingreso->tipoPersonal?->nombre ?? '---' }}
                        </div>

                        <div class="mt-1 text-xs text-zinc-500">
                            {{ $ingreso->nombramiento?->nombre ?? 'Sin nombramiento registrado' }}
                        </div>
                    </div>
                    <div class="flex space-x-6">
                        <div class="mt-3 text-sm  text-zinc-500">
                            <span class="uppercase font-semibold text-[0.670rem] text-zinc-700">No.
                                Trabajador</span><br>
                            {{ $ingreso->numero_trabajador ?? '---' }}
                        </div>
                        <div class="mt-3 text-sm  text-zinc-500">
                            <span class="uppercase font-semibold text-[0.670rem] text-zinc-700">CUV</span><br>
                            {{ $ingreso->cuv ?? '---' }}
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <div class="mt-3 text-sm">
                        <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">Contrato</span><br>
                        {{ $ingreso->contrato?->nombre ?? '' }}
                    </div>
                    <div class="mt-3 text-sm">
                        <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">Escolaridad</span><br>
                        {{ $ingreso->escolaridad?->nombre ?? '' }}
                    </div>
                    <div class="mt-3 text-sm">
                        <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">F. Ingreso</span><br>
                        {{ $ingreso->fecha_ingreso?->format('d/m/Y') ?? '---' }}
                    </div>
                    <div class="mt-3 text-sm">
                        <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">F.
                            Nombramiento</span><br>
                        {{ $ingreso->fecha_nombramiento?->format('d/m/Y') ?? '---' }}
                    </div>
                    <div class="mt-3 text-sm">
                        <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">F.
                            Definitividad</span><br>
                        {{ $ingreso->fecha_definitividad?->format('d/m/Y') ?? '---' }}
                    </div>
                    @if ($ingreso->fecha_baja)
                        <div class="mt-3 text-sm">
                            <span class="uppercase font-semibold text-[0.670rem] text-zinc-400">F. Baja</span><br>
                            {{ $ingreso->fecha_baja?->format('d/m/Y') ?? '' }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="rounded-xl border border-dashed border-zinc-200 bg-zinc-50 px-4 py-8 text-center">
            <p class="text-sm font-medium text-zinc-600">
                No hay ingresos institucionales registrados.
            </p>

            <p class="mt-1 text-xs text-zinc-400">
                Cuando se capture un ingreso desde la edición de persona, aparecerá en esta sección.
            </p>
        </div>
    @endif
</section>

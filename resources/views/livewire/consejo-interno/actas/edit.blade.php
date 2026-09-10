<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">
                {{ $acta ? 'Editar acta' : 'Nueva acta' }}
            </h1>

            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                {{ $acta
                    ? 'Actualiza los datos generales del acta.'
                    : 'Registra los datos generales de una nueva acta del Consejo Interno.' }}
            </p>
        </div>

        <a
            href="{{ route('consejo-interno.actas.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
        >
            Volver al listado
        </a>
    </div>

    @if (session('status'))
        <div
            class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300"
        >
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="guardar">
        <div
            class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"
        >
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">
                Datos generales
            </h2>

            <div class="mt-5 grid gap-5 md:grid-cols-2">

                {{-- Número de acta --}}
                <div>
                    <label
                        for="numero_acta"
                        class="block text-sm font-medium text-zinc-700 dark:text-zinc-300"
                    >
                        Número de acta
                    </label>

                    <input
                        id="numero_acta"
                        type="text"
                        wire:model.blur="numero_acta"
                        class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                    >

                    @error('numero_acta')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Fecha --}}
                <div>
                    <label
                        for="fecha"
                        class="block text-sm font-medium text-zinc-700 dark:text-zinc-300"
                    >
                        Fecha
                    </label>

                    <input
                        id="fecha"
                        type="date"
                        wire:model.blur="fecha"
                        class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                    >

                    @error('fecha')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Título --}}
                <div class="md:col-span-2">
                    <label
                        for="titulo"
                        class="block text-sm font-medium text-zinc-700 dark:text-zinc-300"
                    >
                        Título
                    </label>

                    <input
                        id="titulo"
                        type="text"
                        wire:model.blur="titulo"
                        class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                    >

                    @error('titulo')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Reunión opcional --}}
                <div class="md:col-span-2">
                    <label
                        for="reunion_id"
                        class="block text-sm font-medium text-zinc-700 dark:text-zinc-300"
                    >
                        Reunión asociada
                    </label>

                    <select
                        id="reunion_id"
                        wire:model="reunion_id"
                        class="mt-1 block w-full rounded-lg border-zinc-300 text-sm shadow-sm focus:border-zinc-500 focus:ring-zinc-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                    >
                        <option value="">
                            Acta independiente
                        </option>

                        @foreach ($reuniones as $reunion)
                            <option value="{{ $reunion->id }}">
                                {{ $reunion->fecha?->format('d/m/Y') ?? 'Sin fecha' }}
                                — {{ $reunion->titulo }}
                                — {{ $reunion->estatus }}
                            </option>
                        @endforeach
                    </select>

                    @error('reunion_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ $message }}
                        </p>
                    @enderror

                    <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">
                        La reunión es opcional. El acta conserva autonomía y no hereda documentos ni otros datos operativos.
                    </p>
                </div>
            </div>

            @if ($acta)
                <div class="mt-5 rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950/60">
                    <div class="text-zinc-600 dark:text-zinc-400">
                        Estatus:
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $acta->estatus }}
                        </span>
                    </div>

                    <div class="mt-1 text-zinc-600 dark:text-zinc-400">
                        Año:
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                            {{ $acta->year ?? '—' }}
                        </span>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex items-center justify-end gap-3">
                <a
                    href="{{ route('consejo-interno.actas.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-white"
                >
                    Guardar acta
                </button>
            </div>
        </div>
    </form>

</div>

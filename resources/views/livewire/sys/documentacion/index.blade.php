<div class="space-y-6">

    <x-ui.header title="Documentación" subtitle="Manuales, guías técnicas y documentos de apoyo del SIIAA." />

    <div class="flex gap-6">

        {{-- Sidebar --}}
        <aside class="w-72 shrink-0">
            <x-ui.panel title="Documentos">

                <div class="mb-4">
                    <x-ui.input wire:model.live.debounce.300ms="search" name="search" placeholder="Buscar documento..." />
                </div>

                <div class="max-h-[calc(100vh-16rem)] space-y-5 overflow-y-auto pr-1">

                    @forelse ($groupedDocuments as $category => $items)
                        <div>
                            <h3 class="mb-2 px-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                {{ $category }}
                            </h3>

                            <div class="space-y-1">
                                @foreach ($items as $document)
                                    <button type="button" wire:click="selectDocument('{{ $document['slug'] }}')"
                                        class="w-full rounded-lg px-3 py-2 text-left text-sm transition
                                            {{ ($selectedDocument['slug'] ?? null) === $document['slug']
                                                ? 'bg-sky-50 text-sky-700 ring-1 ring-sky-200'
                                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">

                                        <div class="truncate font-medium">
                                            {{ $document['title'] }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <x-ui.alert type="info">
                            No hay documentos disponibles para tu usuario.
                        </x-ui.alert>
                    @endforelse

                </div>

            </x-ui.panel>
        </aside>

        {{-- Contenido --}}
        <main class="min-w-0 flex-1">
            <x-ui.panel>

                @if ($selectedDocument)
                    <div class="mb-6 border-b border-slate-100 pb-4">
                        <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">
                            {{ $selectedDocument['category'] ?? 'General' }}
                        </p>
                        <div class="mb-2 text-xs text-slate-400">
                            Documentación /
                            {{ $selectedDocument['category'] ?? 'General' }} /
                            {{ $selectedDocument['title'] }}
                        </div>
                        <h2 class="text-2xl font-semibold text-slate-800">
                            {{ $selectedDocument['title'] }}
                        </h2>
                    </div>

                    <article class="prose prose-slate max-w-none">
                        {!! $this->htmlContent !!}
                    </article>
                @else
                    <p class="text-sm text-slate-500">
                        Selecciona un documento para visualizarlo.
                    </p>
                @endif

            </x-ui.panel>
        </main>

    </div>
</div>

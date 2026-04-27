@props([
    'mode' => 'safe', // safe | warn | restricted
])

<div x-data="{ isMobile: window.innerWidth < 1024 }" x-init="window.addEventListener('resize', () => isMobile = window.innerWidth < 1024)">
    {{-- MODO RESTRICTIVO --}}
    @if ($mode === 'restricted')
        <template x-if="isMobile">
            <div class="flex items-center justify-center">
                <div class="w-full max-w-lg">
                    <x-ui.panel>
                        <x-slot:header>
                            <h2 class="text-sm font-semibold text-slate-800">
                                Resolución insuficiente
                            </h2>
                        </x-slot:header>

                        <div class="text-sm text-slate-600">
                            Este módulo requiere una resolución mínima recomendada equivalente a una tablet en
                            orientación horizontal.
                        </div>
                    </x-ui.panel>
                </div>
            </div>
        </template>

        <template x-if="!isMobile">
            <div>
                {{ $slot }}
            </div>
        </template>
    @else
        {{-- SAFE y WARN simplemente renderizan contenido --}}
        {{ $slot }}
    @endif
</div>

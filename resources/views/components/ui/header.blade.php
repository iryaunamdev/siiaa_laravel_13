<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="flex h-16 w-full items-center justify-between px-4 sm:px-6 lg:px-8">

        {{-- IZQUIERDA --}}
        <div class="flex min-w-0 items-center gap-3">
            <button type="button"
                class="inline-flex items-center justify-center rounded-lg p-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900 lg:hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500/30"
                @click="sidebarOpen = true" aria-label="Abrir menú">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <x-ui.breadcrumbs :items="$breadcrumbs ?? []" />
        </div>

        {{-- DERECHA --}}
        <x-ui.dropdown position="bottom" align="right" width="w-56">
            <x-slot name="trigger">
                <button type="button"
                    class="flex items-center gap-3 rounded-lg px-2 py-1.5 transition hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-500/30"
                    aria-haspopup="true">
                    <div class="hidden text-right sm:block">
                        <div class="text-sm font-medium text-slate-800">
                            {{ auth()->user()->name }}
                        </div>
                        <div class="text-xs text-slate-500">
                            Sesión activa
                        </div>
                    </div>

                    <x-ui.avatar :user="auth()->user()" />

                    <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4 text-slate-400 sm:block"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06Z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </x-slot>

            {{-- Header del dropdown --}}
            <div class="border-b border-slate-100 px-4 py-3">
                <div class="text-sm font-medium text-slate-800">
                    {{ auth()->user()->name }}
                </div>
                <div class="truncate text-xs text-slate-500">
                    {{ auth()->user()->email }}
                </div>
            </div>

            {{-- Opciones --}}
            <x-ui.dropdown-link href="#">
                Perfil público
            </x-ui.dropdown-link>

            <x-ui.dropdown-link href="#">
                Configuración
            </x-ui.dropdown-link>

            <div class="border-t border-slate-100"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-ui.dropdown-button danger onclick="event.preventDefault(); this.closest('form').submit();">
                    Cerrar sesión
                </x-ui.dropdown-button>
            </form>
        </x-ui.dropdown>

    </div>
</header>

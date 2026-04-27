<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'SIIAA') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">
        {{-- Overlay móvil --}}
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"
            @click="sidebarOpen = false" x-cloak></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-72 transform bg-transparent transition-transform duration-300 ease-out lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <x-sidemenu></x-sidemenu>
        </aside>

        {{-- Contenedor principal --}}
        <div class="flex min-h-screen flex-1 flex-col">
            <x-ui.header />
            <x-ui.resolution-warning />

            <main class="flex-1">
                <div class="w-full mx-auto px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>

            <x-ui.footer />
        </div>
    </div>

    @livewireScripts
</body>

</html>

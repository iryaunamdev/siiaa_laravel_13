<x-ui.sidebar>
    <div class="flex flex-col h-full justify-between">
        <div>
            <x-ui.menu.section title="General" />
            <x-ui.menu>
                <x-ui.menu.item :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-ui.menu.item>
            </x-ui.menu>



            <x-ui.menu.section title="Administración" />
            <x-ui.menu>
                <x-ui.menu.item :href="route('sys.users.index')" :active="request()->routeIs('users.*')">
                    Usuarios
                </x-ui.menu.item>
            </x-ui.menu>
        </div>
        <div>
            <div class="mt-6 border-t border-slate-200 pt-4">
                <x-ui.menu>
                    <x-ui.menu.item :href="route('profile.edit')" :active="request()->routeIs('settings.*')">
                        Perfil
                    </x-ui.menu.item>
                </x-ui.menu>
            </div>
        </div>
    </div>
</x-ui.sidebar>

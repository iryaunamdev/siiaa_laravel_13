<x-ui.panel size="full" class="max-w-full overflow-hidden">
    <x-slot:title>
        Matriz de permisos
    </x-slot:title>

    <x-slot:description>
        Consulta de roles y permisos del sistema.
        @can('matrix.edit')
            Puedes activar o desactivar permisos por rol.
        @else
            Modo solo lectura.
        @endcan
    </x-slot:description>

    <div class="max-w-full overflow-hidden bg-white">
        <div class="max-h-[calc(100vh-18rem)] max-w-full overflow-auto">
            <table class="w-max min-w-full table-auto divide-y divide-slate-200 text-sm">
                <thead class="sticky top-0 z-30 bg-slate-50">
                    <tr>
                        <th class="sticky left-0 z-40 bg-slate-50 px-4 py-3 text-left font-semibold text-slate-700 ">
                            Permiso
                        </th>

                        @foreach ($roles as $role)
                            <th class="px-4 py-3 text-center font-semibold text-slate-700 whitespace-nowrap ">
                                {{ $role->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @foreach ($groupedPermissions as $module => $modulePermissions)
                        {{-- Header de módulo --}}
                        <tr class="bg-slate-100">
                            <td
                                class="sticky left-0 z-20 bg-slate-100 px-4 py-2 font-semibold text-slate-700 uppercase text-xs">
                                {{ $module }}
                            </td>

                            @foreach ($roles as $role)
                                @php
                                    $state = $this->getModuleState($module, $role->name);
                                @endphp

                                <td class="px-4 py-2 text-center">
                                    @can('matrix.edit')
                                        <div class="flex justify-center">
                                            <input type="checkbox"
                                                wire:click="toggleModule('{{ $module }}', '{{ $role->name }}', {{ $state !== 'all' ? 'true' : 'false' }})"
                                                @checked($state === 'all') x-data x-init="$el.indeterminate = {{ $state === 'partial' ? 'true' : 'false' }}"
                                                class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                        </div>
                                    @endcan
                                </td>
                            @endforeach
                        </tr>

                        {{-- Permisos del módulo --}}
                        @foreach ($modulePermissions as $permission)
                            <tr class="hover:bg-slate-50/70">
                                <td class="sticky left-0 z-10 bg-white px-4 py-3 font-mono text-xs text-slate-700">
                                    {{ $permission->name }}
                                </td>

                                @foreach ($roles as $role)
                                    @php
                                        $checked = $matrix[$permission->name][$role->name] ?? false;
                                    @endphp

                                    <td class="px-4 py-3 text-center">
                                        @can('matrix.edit')
                                            <x-ui.checkbox :checked="$checked"
                                                wire:click="togglePermission('{{ $permission->name }}', '{{ $role->name }}')"
                                                align="center" />
                                        @else
                                            @if ($checked)
                                                <span class="text-emerald-600 text-xs">Sí</span>
                                            @else
                                                <span class="text-slate-400 text-xs">No</span>
                                            @endif
                                        @endcan
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-ui.panel>

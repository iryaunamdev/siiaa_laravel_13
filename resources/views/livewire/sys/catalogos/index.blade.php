<x-ui.panel size="full">

    <x-slot:title>
        Catálogos del sistema
    </x-slot:title>

    <x-slot:description>
        Administración de catálogos e items
    </x-slot:description>

    {{-- 🔹 Filtros --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-end px-4 py-3">

        <div class="w-full md:w-1/3">
            <x-ui.input wire:model.live="search" placeholder="Buscar catálogo..." />
        </div>

        <div class="w-full md:w-48">
            <x-ui.select wire:model.live="status" :options="[
                ['value' => 'all', 'label' => 'Todos'],
                ['value' => 'active', 'label' => 'Activos'],
                ['value' => 'inactive', 'label' => 'Inactivos'],
            ]" />
        </div>

        <div class="flex-1 text-right">
            @can('catalogos.create')
                <x-ui.button class="font-semibold align-center" wire:click="createCatalogo">
                    <x-ui.icon name="plus" class="w-4 h-4" />
                    <span class="ml-2">Catálogo</span>
                </x-ui.button>
            @endcan
        </div>

    </div>



    {{-- 🔹 Grid --}}
    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 px-4 py-3">

        @forelse ($catalogos as $catalogo)
            <div
                class="flex h-[280px] min-h-0 flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm">

                {{-- 🔹 Header --}}
                <div class="shrink-0 flex items-start justify-between border-b border-zinc-100 px-4 py-3">

                    <div class="cursor-pointer" wire:click="editCatalogo({{ $catalogo->id }})">
                        <p class="text-sm font-semibold text-zinc-800">
                            {{ $catalogo->nombre }}
                        </p>
                        <p class="text-[0.65rem] text-zinc-500">
                            [{{ $catalogo->id }}] {{ $catalogo->clave }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2">

                        @can('catalogos_items.create')
                            <x-ui.button variant="link" size="sm" wire:click.stop="createItem({{ $catalogo->id }})"
                                class="shrink-0" title="Agregar item">
                                <x-ui.icon name="plus" class="w-4 h-4 text-blue-500 hover:text-blue-600" />
                            </x-ui.button>
                        @endcan

                        @can('catalogos.delete')
                            <x-ui.button variant="link" size="sm" title="Eliminar catálogo"
                                wire:click.stop="confirmDeleteCatalogo({{ $catalogo->id }})">
                                <x-ui.icon name="trash" class="w-4 h-4 text-red-500 hover:text-red-600" />
                            </x-ui.button>
                        @endcan
                    </div>
                </div>

                {{-- 🔹 Body (items) --}}
                <div class="min-h-0 flex-1 overflow-y-auto px-2 py-2" x-data x-init="window.SIIAA.sortable($el, {
                    onSort(ids) {
                        $wire.updateItemOrder(ids)
                    }
                })">
                    @forelse ($catalogo->items as $item)
                        <div data-sortable-item data-id="{{ $item->id }}"
                            class="flex items-center gap-2 px-2 py-1.5 hover:bg-zinc-50 rounded-lg">

                            @can('catalogos_items.order')
                                <button type="button" data-sortable-handle
                                    class="shrink-0 cursor-grab text-zinc-400 hover:text-zinc-700"
                                    title="Arrastrar para ordenar">
                                    ⋮⋮
                                </button>
                            @endcan

                            {{-- Checkbox --}}
                            <div class="shrink-0">
                                <x-ui.checkbox :checked="$item->activo" wire:click="toggleItem({{ $item->id }})" />
                            </div>

                            {{-- Info clickable --}}
                            <div class="min-w-0 flex-1 cursor-pointer" wire:click="editItem({{ $item->id }})">
                                <p class="truncate text-sm text-zinc-700 leading-tight">
                                    {{ $item->nombre }}
                                </p>

                                <p class="truncate text-[0.65rem] text-zinc-400 leading-tight">
                                    [{{ $item->id }}] {{ $item->clave }}
                                </p>
                            </div>

                            {{-- Delete --}}
                            @can('catalogos.items.delete')
                                <button wire:click.stop="confirmDeleteItem({{ $item->id }})" class="shrink-0  text-sm"
                                    title="Eliminar item">
                                    <x-ui.icon name="trash" class="w-4 h-4 text-red-500 hover:text-red-600" />
                                </button>
                            @endcan

                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-zinc-400">
                            Catalogo sin elemetos.
                        </div>
                    @endforelse

                </div>

                {{-- 🔹 Footer --}}
                <div class="shrink-0 border-t border-zinc-100 px-4 py-2 text-xs text-zinc-500">

                    <div class="flex items-center justify-between gap-2">

                        <span>Total: {{ $catalogo->items_count }}</span>

                        <span class="text-emerald-600">
                            Activos: {{ $catalogo->items_activos_count }}
                        </span>

                        <span class="text-zinc-400">
                            Inactivos: {{ $catalogo->items_inactivos_count }}
                        </span>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-span-full py-12 text-center text-zinc-500">
                No hay catálogos registrados.
            </div>
        @endforelse

    </div>
    @include('livewire.sys.catalogos._catalogo_modal')
    @include('livewire.sys.catalogos._item_modal')
    <x-ui.confirm-delete-modal model="deleteCatalogoModal" title="Eliminar catálogo" :name="$deleteCatalogoNombre"
        warning="Esta acción solo será permitida si el catálogo no tiene items registrados."
        cancel-action="$set('deleteCatalogoModal', false)" confirm-action="deleteCatalogo" />
    <x-ui.confirm-delete-modal model="deleteItemModal" title="Eliminar item" :name="$deleteItemNombre"
        confirm-action="deleteItem" cancel-action="$set('deleteItemModal', false)" />
</x-ui.panel>

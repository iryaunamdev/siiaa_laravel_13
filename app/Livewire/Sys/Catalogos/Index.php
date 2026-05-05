<?php

namespace App\Livewire\Sys\Catalogos;

use App\Models\Catalogo;
use App\Models\CatalogoItem;
use Livewire\Component;
use Illuminate\Validation\Rule;

class Index extends Component
{
    public string $search = '';
    public string $status = 'all'; // all | active | inactive
    public bool $catalogoModal = false;

    public ?int $catalogoId = null;

    public string $catalogoClave = '';
    public string $catalogoNombre = '';
    public ?string $catalogoDescripcion = null;
    public bool $catalogoActivo = true;
    public bool $deleteCatalogoModal = false;
    public ?int $deleteCatalogoId = null;
    public string $deleteCatalogoNombre = '';

    //ITEMS
    public bool $itemModal = false;

    public ?int $itemId = null;
    public ?int $itemCatalogoId = null;

    public string $itemClave = '';
    public string $itemNombre = '';
    public ?string $itemDescripcion = null;
    public bool $itemActivo = true;

    public bool $deleteItemModal = false;
    public ?int $deleteItemId = null;
    public string $deleteItemNombre = '';

    public bool $itemBulkMode = false;
    public string $itemBulkText = '';

    public function updatedSearch(): void
    {
        // Evita quedarse con modales abiertos al cambiar contexto
        $this->closeAllModals();
    }

    public function updatedStatus(): void
    {
        $this->closeAllModals();
    }

    public function createCatalogo(): void
    {
        abort_unless(auth()->user()->can('catalogos.create'), 403);

        $this->resetCatalogoForm();

        $this->catalogoModal = true;
    }

    public function resetCatalogoForm(): void
    {
        $this->catalogoId = null;

        $this->catalogoClave = '';
        $this->catalogoNombre = '';
        $this->catalogoDescripcion = null;
        $this->catalogoActivo = true;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function getCatalogosProperty()
    {
        return Catalogo::query()
            ->with([
                'items' => fn($q) => $q->ordered(),
            ])
            ->withCount([
                'items',
                'items as items_activos_count' => fn($q) => $q->where('activo', true),
                'items as items_inactivos_count' => fn($q) => $q->where('activo', false),
            ])
            ->search($this->search)
            ->when($this->status === 'active', fn($q) => $q->activos())
            ->when($this->status === 'inactive', fn($q) => $q->inactivos())
            ->orderBy('nombre')
            ->get();
    }

    public function editCatalogo(int $id): void
    {
        abort_unless(auth()->user()->can('catalogos.update'), 403);

        $catalogo = Catalogo::findOrFail($id);

        $this->catalogoId = $catalogo->id;
        $this->catalogoClave = $catalogo->clave;
        $this->catalogoNombre = $catalogo->nombre;
        $this->catalogoDescripcion = $catalogo->descripcion;
        $this->catalogoActivo = $catalogo->activo;

        $this->resetErrorBag();
        $this->resetValidation();

        $this->catalogoModal = true;
    }

    public function saveCatalogo(): void
    {
        abort_unless(
            auth()->user()->can($this->catalogoId ? 'catalogos.update' : 'catalogos.create'),
            403
        );

        $this->validate([
            'catalogoClave' => [
                'required',
                'string',
                'max:30',
                'unique:catalogos,clave,' . $this->catalogoId,
            ],
            'catalogoNombre' => ['required', 'string', 'max:255'],
            'catalogoDescripcion' => ['nullable', 'string'],
            'catalogoActivo' => ['boolean'],
        ]);

        Catalogo::updateOrCreate(
            ['id' => $this->catalogoId],
            [
                'clave' => $this->catalogoClave,
                'nombre' => $this->catalogoNombre,
                'descripcion' => $this->catalogoDescripcion,
                'activo' => $this->catalogoActivo,
            ]
        );

        $this->catalogoModal = false;

        $this->dispatch(
            'toast',
            type: 'success',
            message: $this->catalogoId ? 'Catálogo actualizado' : 'Catálogo creado'
        );

        $this->resetCatalogoForm();
    }

    public function confirmDeleteCatalogo(int $id): void
    {
        abort_unless(auth()->user()->can('catalogos.delete'), 403);

        $catalogo = Catalogo::withCount('items')->findOrFail($id);

        $this->deleteCatalogoId = $catalogo->id;
        $this->deleteCatalogoNombre = $catalogo->nombre;
        $this->deleteCatalogoModal = true;
    }

    public function deleteCatalogo(): void
    {
        abort_unless(auth()->user()->can('catalogos.delete'), 403);

        $catalogo = Catalogo::withCount('items')->findOrFail($this->deleteCatalogoId);

        if ($catalogo->items_count > 0) {
            $this->dispatch('toast', type: 'error', message: 'No se puede eliminar un catálogo con items.');
            return;
        }

        $catalogo->delete();

        $this->deleteCatalogoModal = false;

        $this->dispatch('toast', type: 'success', message: 'Catálogo eliminado');
    }

    public function toggleItem($itemId)
    {
        $item = CatalogoItem::find($itemId);

        if (!$item) return;

        $item->activo = !$item->activo;
        $item->save();

        $this->dispatch('toast', type: 'success', message: 'Estado actualizado');
    }

    public function createItem(int $catalogoId): void
    {
        abort_unless(auth()->user()->can('catalogos_items.create'), 403);

        $this->resetItemForm();

        $this->itemCatalogoId = $catalogoId;

        $this->itemModal = true;
    }

    public function editItem(int $id): void
    {
        abort_unless(auth()->user()->can('catalogos_items.update'), 403);

        $item = CatalogoItem::findOrFail($id);

        $this->itemId = $item->id;
        $this->itemCatalogoId = $item->catalogo_id;

        $this->itemClave = $item->clave;
        $this->itemNombre = $item->nombre;
        $this->itemDescripcion = $item->descripcion;
        $this->itemActivo = $item->activo;

        $this->resetErrorBag();
        $this->resetValidation();

        $this->itemModal = true;
    }

    public function saveItem(): void
    {
        abort_unless(
            auth()->user()->can($this->itemId ? 'catalogos_items.update' : 'catalogos.items.create'),
            403
        );

        if ($this->itemBulkMode && !$this->itemId) {
            $this->saveBulkItems();
            return;
        }

        $this->validate([
            'itemCatalogoId' => ['required', 'exists:catalogos,id'],
            'itemClave' => [
                'required',
                'string',
                'max:30',
                Rule::unique('catalogos_items', 'clave')
                    ->where('catalogo_id', $this->itemCatalogoId)
                    ->ignore($this->itemId),
            ],
            'itemNombre' => ['required', 'string', 'max:255'],
            'itemDescripcion' => ['nullable', 'string'],
            'itemActivo' => ['boolean'],
        ]);

        $nextOrden = CatalogoItem::where('catalogo_id', $this->itemCatalogoId)
            ->max('orden') + 1;

        CatalogoItem::updateOrCreate(
            ['id' => $this->itemId],
            [
                'catalogo_id' => $this->itemCatalogoId,
                'clave' => $this->itemClave,
                'nombre' => $this->itemNombre,
                'descripcion' => $this->itemDescripcion,
                'activo' => $this->itemActivo,
                'orden' => $this->itemId
                    ? CatalogoItem::find($this->itemId)?->orden
                    : $nextOrden,
            ]
        );

        $this->itemModal = false;

        $this->dispatch(
            'toast',
            type: 'success',
            message: $this->itemId ? 'Item actualizado' : 'Item creado'
        );

        $this->resetItemForm();
    }

    public function saveBulkItems(): void
    {
        abort_unless(auth()->user()->can('catalogos.items.create'), 403);

        $this->validate([
            'itemCatalogoId' => ['required', 'exists:catalogos,id'],
            'itemBulkText' => ['required', 'string'],
        ]);

        $lines = preg_split('/\r\n|\r|\n/', trim($this->itemBulkText));

        $items = [];
        $duplicatedInText = [];
        $existingKeys = [];

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode(',', $line, 2));

            if (count($parts) < 2 || blank($parts[0]) || blank($parts[1])) {
                $this->addError(
                    'itemBulkText',
                    'La línea ' . ($lineNumber + 1) . ' no cumple el formato CLAVE, NOMBRE.'
                );
                return;
            }

            $clave = $parts[0];
            $nombre = $parts[1];

            if (isset($items[$clave])) {
                $duplicatedInText[] = $clave;
                continue;
            }

            $items[$clave] = [
                'clave' => $clave,
                'nombre' => $nombre,
            ];
        }

        if (!empty($duplicatedInText)) {
            $this->addError(
                'itemBulkText',
                'Hay claves duplicadas en el texto: ' . implode(', ', array_unique($duplicatedInText))
            );
            return;
        }

        $existingKeys = CatalogoItem::where('catalogo_id', $this->itemCatalogoId)
            ->whereIn('clave', array_keys($items))
            ->pluck('clave')
            ->toArray();

        if (!empty($existingKeys)) {
            $this->addError(
                'itemBulkText',
                'Estas claves ya existen en el catálogo: ' . implode(', ', $existingKeys)
            );
            return;
        }

        $nextOrden = (CatalogoItem::where('catalogo_id', $this->itemCatalogoId)->max('orden') ?? 0) + 1;

        foreach ($items as $item) {
            CatalogoItem::create([
                'catalogo_id' => $this->itemCatalogoId,
                'clave' => $item['clave'],
                'nombre' => $item['nombre'],
                'descripcion' => null,
                'activo' => true,
                'orden' => $nextOrden++,
            ]);
        }

        $this->itemModal = false;

        $this->dispatch('toast', type: 'success', message: 'Items importados correctamente');

        $this->resetItemForm();
    }

    public function confirmDeleteItem(int $id): void
    {
        abort_unless(auth()->user()->can('catalogos.items.delete'), 403);

        $item = CatalogoItem::findOrFail($id);

        $this->deleteItemId = $item->id;
        $this->deleteItemNombre = $item->nombre;
        $this->deleteItemModal = true;
    }

    public function deleteItem(): void
    {
        abort_unless(auth()->user()->can('catalogos.items.delete'), 403);

        CatalogoItem::findOrFail($this->deleteItemId)->delete();

        $this->deleteItemModal = false;

        $this->dispatch('toast', type: 'success', message: 'Item eliminado');
    }

    public function resetItemForm(): void
    {
        $this->itemId = null;

        $this->itemCatalogoId = null;

        $this->itemClave = '';
        $this->itemNombre = '';
        $this->itemDescripcion = null;
        $this->itemActivo = true;

        $this->itemBulkMode = false;
        $this->itemBulkText = '';

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updateItemOrder(array $orderedIds): void
    {
        abort_unless(auth()->user()->can('catalogos_items.order'), 403);

        foreach ($orderedIds as $index => $id) {
            CatalogoItem::whereKey($id)->update([
                'orden' => $index + 1,
            ]);
        }

        $this->dispatch('toast', type: 'success', message: 'Orden actualizado');
    }

    public function closeAllModals(): void
    {
        $this->catalogoModal = false;
        $this->itemModal = false;
        $this->deleteCatalogoModal = false;
        $this->deleteItemModal = false;

        // Limpieza opcional
        $this->resetCatalogoForm();
        $this->resetItemForm();
    }

    public function render()
    {
        return view('livewire.sys.catalogos.index', [
            'catalogos' => $this->catalogos,
        ]);
    }
}

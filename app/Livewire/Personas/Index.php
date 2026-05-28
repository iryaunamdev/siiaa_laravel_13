<?php

namespace App\Livewire\Personas;

use App\Models\CatalogoItem;
use App\Models\Persona;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public string $search = '';
    public string $status = 'active';
    public int $perPage = 10;

    public bool $deleteModal = false;
    public ?int $deleteId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => 'active'],
        'perPage' => ['except' => 15],
    ];

    public ?int $tipoPersonalId = null;
    public $c_tiposPersonal;

    public function mount()
    {
        $this->c_tiposPersonal = CatalogoItem::whereHas('catalogo', function ($query) {
            $query->where('clave', 'TIPOS_PERSONAL');
        })
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedTipoPersonalId(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'status',
            'tipoPersonalId',
        ]);

        $this->status = 'active';

        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $this->authorize('personas.update');

        $persona = Persona::query()->findOrFail($id);

        $persona->update([
            'activo' => ! (bool) $persona->activo,
        ]);

        $this->dispatch(
            'toast',
            type: 'success',
            message: $persona->activo
                ? 'Persona activada correctamente.'
                : 'Persona inactivada correctamente.'
        );
    }

    public function confirmDelete(int $id): void
    {
        $this->authorize('personas.delete');

        $this->deleteId = $id;
        $this->deleteModal = true;
    }

    public function deleteConfirmed(): void
    {
        $this->authorize('personas.delete');

        if (! $this->deleteId) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'No se encontró el registro a eliminar.'
            );

            return;
        }

        $persona = Persona::query()->findOrFail($this->deleteId);

        $persona->delete();

        $this->resetDeleteForm();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Persona eliminada correctamente.'
        );
    }

    public function resetDeleteForm(): void
    {
        $this->deleteId = null;
        $this->deleteModal = false;
    }

    public function render()
    {
        $personas = Persona::query()
            ->when($this->search !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellidop', 'like', "%{$search}%")
                        ->orWhere('apellidom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('curp', 'like', "%{$search}%")
                        ->orWhere('rfc', 'like', "%{$search}%");
                });
            })
            ->when($this->status === 'active', fn($query) => $query->where('activo', true))
            ->when($this->status === 'inactive', fn($query) => $query->where('activo', false))
            ->when($this->tipoPersonalId, function ($query) {
                $query->whereHas('ingresoPrincipal', function ($q) {
                    $q->where('tipo_personal_id', $this->tipoPersonalId);
                });
            })
            ->orderBy('apellidop')
            ->orderBy('apellidom')
            ->orderBy('nombre')
            ->paginate($this->perPage);

        return view('livewire.personas.index', [
            'personas' => $personas,
        ]);
    }
}

<?php

namespace App\Livewire\ConsejoInterno\Reuniones;

use App\Models\ConsejoInterno\CiReunion;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $estatus = '';

    public int $perPage = 15;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEstatus(): void
    {
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search', 'estatus']);
        $this->resetPage();
    }

    public function eliminar(int $reunionId): void
    {
        $reunion = CiReunion::query()->findOrFail($reunionId);

        $this->authorize('delete', $reunion);

        $reunion->delete();

        $this->resetPage();

        session()->flash('status', 'La reunión fue eliminada correctamente.');
    }

    public function render()
    {
        $reuniones = CiReunion::query()
            ->withCount([
                'participantes',
                'puntosSolicitud',
                'puntosOtros',
                'documentos',
            ])
            ->buscar($this->search)
            ->when($this->estatus !== '', function ($query) {
                $query->where('estatus', $this->estatus);
            })
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.consejo-interno.reuniones.index', [
            'reuniones' => $reuniones,
            'estatusOptions' => ConsejoInternoCatalogos::estadosReunion(),
        ])->layout('layouts.app');
    }
}

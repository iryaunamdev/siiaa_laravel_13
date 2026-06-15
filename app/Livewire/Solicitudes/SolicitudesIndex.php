<?php

namespace App\Livewire\Solicitudes;

use App\Models\Solicitudes\Solicitud;
use App\Models\CatalogoItem;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class SolicitudesIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public ?int $estatus_id = null;

    public ?int $tipo_solicitud_id = null;

    public int $perPage = 15;

    /* CATALOGOS */
    public $c_estatus, $c_tipos_solicitud;

    public function mount(): void
    {
        $this->authorize('viewAny', Solicitud::class);

        $this->c_estatus = $this->catalogoItems('SOL_EST');
        $this->c_tipos_solicitud = $this->catalogoItems('SOLTIPOS');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEstatusId(): void
    {
        $this->resetPage();
    }

    public function updatedTipoSolicitudId(): void
    {
        $this->resetPage();
    }

    protected function catalogoItems(string $catalogoClave)
    {
        return CatalogoItem::query()
            ->whereHas('catalogo', fn($query) => $query->where('clave', $catalogoClave))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }

    public function render()
    {
        $user = auth()->user();
        $identityId = activeIdentityLinkId();

        $query = Solicitud::query()
            ->with([
                'owner',
                'tipoSolicitud',
                'estatus',
                'visitante',
            ])
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subquery) {
                    $subquery
                        ->where('folio', 'like', "%{$this->search}%")
                        ->orWhere('informacion_adicional', 'like', "%{$this->search}%");
                });
            })
            ->when($this->estatus_id, fn($query) => $query->where('estatus_id', $this->estatus_id))
            ->when($this->tipo_solicitud_id, fn($query) => $query->where('tipo_solicitud_id', $this->tipo_solicitud_id));

        /*
    |--------------------------------------------------------------------------
    | Alcance del listado
    |--------------------------------------------------------------------------
    |
    | manage: ve todo.
    | review: ve solicitudes institucionales.
    | access: ve solo solicitudes propias.
    |
    */
        if (! $user->can('solicitudes.manage') && ! $user->can('solicitudes.review')) {
            $query->where('owner_id', $identityId);
        }

        return view('livewire.solicitudes.solicitudes-index', [
            'solicitudes' => $query
                ->latest()
                ->paginate($this->perPage),
        ]);
    }
}
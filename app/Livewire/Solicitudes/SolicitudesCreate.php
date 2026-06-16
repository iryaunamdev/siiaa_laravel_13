<?php

namespace App\Livewire\Solicitudes;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use App\Models\Solicitudes\Solicitud;
use App\Services\Solicitudes\SolicitudServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class SolicitudesCreate extends Component
{
    use AuthorizesRequests;

    public array $form = [
        'owner_id' => null,
        'tipo_solicitud_id' => null,
    ];

    public $c_tipos_solicitud;


    public $c_owner_identities;

    public function mount(): void
    {
        $this->authorize('create', Solicitud::class);

        $this->c_tipos_solicitud = $this->catalogoItems('SOLTIPOS');
        $this->c_owner_identities = $this->canSelectOwner()
            ? $this->ownerIdentities()
            : collect();
    }

    public function save(SolicitudServiceInterface $solicitudService)
    {
        $this->authorize('create', Solicitud::class);

        $validated = $this->validate([
            'form.owner_id' => [
                $this->canSelectOwner() ? 'required' : 'nullable',
                'nullable',
                'integer',
                'exists:identity_links,id',
            ],
            'form.tipo_solicitud_id' => ['required', 'integer', 'exists:catalogos_items,id'],
        ]);

        $actorIdentityId = \currentIdentityId();
        $ownerId = $this->canSelectOwner()
            ? (int) $validated['form']['owner_id']
            : $actorIdentityId;

        abort_if(! $ownerId, 403, 'No se encontro una identidad institucional propietaria.');

        if (! $this->canSelectOwner()) {
            abort_if(! $actorIdentityId, 403, 'No se encontro una identidad institucional activa.');
        }

        $solicitud = $solicitudService->crearBorrador(
            $validated['form'],
            $ownerId,
            $actorIdentityId
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Solicitud creada correctamente.'
        );

        return redirect()->route('solicitudes.edit', $solicitud);
    }

    protected function canSelectOwner(): bool
    {
        return auth()->user()?->can('solicitudes.manage') ?? false;
    }

    protected function catalogoItems(string $catalogoClave)
    {
        return CatalogoItem::query()
            ->whereHas('catalogo', fn($query) => $query->where('clave', $catalogoClave))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }

    protected function ownerIdentities()
    {
        return IdentityLink::query()
            ->activas()
            ->orderBy('identity_type')
            ->orderBy('email')
            ->get();
    }

    public function render()
    {
        return view('livewire.solicitudes.solicitudes-create');
    }
}

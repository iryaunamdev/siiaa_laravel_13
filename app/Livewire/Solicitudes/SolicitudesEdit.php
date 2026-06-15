<?php

namespace App\Livewire\Solicitudes;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use App\Models\Solicitudes\Solicitud;
use App\Services\Solicitudes\SolicitudServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class SolicitudesEdit extends Component
{
    use AuthorizesRequests;

    public Solicitud $solicitud;

    public array $form = [
        'owner_id' => null,
        'tipo_solicitud_id' => null,
        'motivo_id' => null,
        'fecha_inicio' => null,
        'fecha_fin' => null,
        'informacion_adicional' => null,
    ];

    public array $visitanteForm = [
        'nombre' => null,
        'apellidos' => null,
        'email' => null,
        'institucion' => null,
        'lugar' => null,
        'fecha_inicio' => null,
        'fecha_fin' => null,
    ];

    public $c_tipos_solicitud;
    public $c_motivos;
    public $c_owner_identities;

    public bool $can_manage_owner = false;
    public bool $can_modify_owner = false;

    public function mount(Solicitud $solicitud): void
    {
        $this->solicitud = $solicitud->load([
            'owner', 'tipoSolicitud', 'motivo', 'estatus',
            'recursos', 'documentos', 'visitante', 'requerimientos',
        ]);

        $this->authorize('update', $this->solicitud);

        $this->can_manage_owner = $this->canManageOwner();
        $this->can_modify_owner = $this->canModifyOwner();
        $this->c_tipos_solicitud = $this->catalogoItems('SOLTIPOS');
        $this->c_motivos = $this->catalogoItems('SOLMOT');
        $this->c_owner_identities = $this->can_manage_owner ? $this->ownerIdentities() : collect();

        $this->form = [
            'owner_id' => $this->solicitud->owner_id,
            'tipo_solicitud_id' => $this->solicitud->tipo_solicitud_id,
            'motivo_id' => $this->solicitud->motivo_id,
            'fecha_inicio' => optional($this->solicitud->fecha_inicio)->format('Y-m-d'),
            'fecha_fin' => optional($this->solicitud->fecha_fin)->format('Y-m-d'),
            'informacion_adicional' => $this->solicitud->informacion_adicional,
        ];

        $this->visitanteForm = [
            'nombre' => $this->solicitud->visitante?->nombre,
            'apellidos' => $this->solicitud->visitante?->apellidos,
            'email' => $this->solicitud->visitante?->email,
            'institucion' => $this->solicitud->visitante?->institucion,
            'lugar' => $this->solicitud->visitante?->lugar,
            'fecha_inicio' => optional($this->solicitud->visitante?->fecha_inicio)->format('Y-m-d'),
            'fecha_fin' => optional($this->solicitud->visitante?->fecha_fin)->format('Y-m-d'),
        ];
    }

    public function save(SolicitudServiceInterface $solicitudService): void
    {
        $this->authorize('update', $this->solicitud);

        $validated = $this->validate([
            'form.owner_id' => [$this->can_modify_owner ? 'required' : 'nullable', 'nullable', 'integer', 'exists:identity_links,id'],
            'form.tipo_solicitud_id' => ['required', 'integer', 'exists:catalogos_items,id'],
            'form.motivo_id' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'form.fecha_inicio' => ['nullable', 'date'],
            'form.fecha_fin' => ['nullable', 'date', 'after_or_equal:form.fecha_inicio'],
            'form.informacion_adicional' => ['nullable', 'string', 'max:5000'],
        ]);

        $identityId = \currentIdentityId();
        abort_if(! $identityId, 403, 'No se encontro una identidad institucional activa.');

        if (! $this->can_modify_owner) {
            unset($validated['form']['owner_id']);
        }

        $this->solicitud = $solicitudService->actualizar($this->solicitud, $validated['form'], $identityId);

        $this->dispatch('toast', type: 'success', message: 'Solicitud actualizada correctamente.');
    }

    public function guardarVisitante(SolicitudServiceInterface $solicitudService): void
    {
        $this->authorize('update', $this->solicitud);

        $validated = $this->validate([
            'visitanteForm.nombre' => ['nullable', 'string', 'max:255'],
            'visitanteForm.apellidos' => ['nullable', 'string', 'max:255'],
            'visitanteForm.email' => ['nullable', 'email', 'max:255'],
            'visitanteForm.institucion' => ['nullable', 'string', 'max:255'],
            'visitanteForm.lugar' => ['nullable', 'string', 'max:255'],
            'visitanteForm.fecha_inicio' => ['nullable', 'date'],
            'visitanteForm.fecha_fin' => ['nullable', 'date', 'after_or_equal:visitanteForm.fecha_inicio'],
        ]);

        $identityId = \currentIdentityId();
        abort_if(! $identityId, 403, 'No se encontro una identidad institucional activa.');

        $this->solicitud = $solicitudService
            ->guardarVisitante($this->solicitud, $validated['visitanteForm'], $identityId)
            ->load(['owner', 'tipoSolicitud', 'motivo', 'estatus', 'recursos', 'documentos', 'visitante', 'requerimientos']);

        $this->dispatch('toast', type: 'success', message: 'Visitante guardado correctamente.');
    }

    public function enviar(SolicitudServiceInterface $solicitudService)
    {
        $this->authorize('send', $this->solicitud);

        $identityId = \currentIdentityId();
        abort_if(! $identityId, 403, 'No se encontro una identidad institucional activa.');

        $this->solicitud = $solicitudService->enviar($this->solicitud, $identityId);

        $this->dispatch('toast', type: 'success', message: 'Solicitud enviada correctamente.');

        return redirect()->route('solicitudes.show', $this->solicitud);
    }

    protected function canManageOwner(): bool
    {
        return auth()->user()?->can('solicitudes.manage') ?? false;
    }

    protected function canModifyOwner(): bool
    {
        if (! $this->canManageOwner()) {
            return false;
        }

        return $this->solicitud->created_by === null
            || (int) $this->solicitud->created_by !== (int) $this->solicitud->owner_id;
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
        return view('livewire.solicitudes.solicitudes-edit');
    }
}

<?php

namespace App\Livewire\Solicitudes;

use App\Models\Solicitudes\Solicitud;
use App\Services\Solicitudes\SolicitudServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class SolicitudesCreate extends Component
{
    use AuthorizesRequests;

    public array $form = [
        'tipo_solicitud_id' => null,
        'motivo_id' => null,
        'fecha_inicio' => null,
        'fecha_fin' => null,
        'informacion_adicional' => null,
    ];

    public function mount(): void
    {
        $this->authorize('create', Solicitud::class);
    }

    public function save(SolicitudServiceInterface $solicitudService)
    {
        $this->authorize('create', Solicitud::class);

        $validated = $this->validate([
            'form.tipo_solicitud_id' => ['required', 'integer', 'exists:catalogos_items,id'],
            'form.motivo_id' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'form.fecha_inicio' => ['nullable', 'date'],
            'form.fecha_fin' => ['nullable', 'date', 'after_or_equal:form.fecha_inicio'],
            'form.informacion_adicional' => ['nullable', 'string', 'max:5000'],
        ]);

        $identityId = activeIdentityLinkId();

        abort_if(! $identityId, 403, 'No se encontró una identidad institucional activa.');

        $solicitud = $solicitudService->crearBorrador(
            $validated['form'],
            $identityId
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Solicitud creada correctamente.'
        );

        return redirect()->route('solicitudes.edit', $solicitud);
    }

    public function render()
    {
        return view('livewire.solicitudes.solicitudes-create');
    }
}
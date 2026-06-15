<?php

namespace App\Livewire\Solicitudes;

use App\Models\Solicitudes\Solicitud;
use App\Services\Solicitudes\SolicitudServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class SolicitudesReview extends Component
{
    use AuthorizesRequests;

    public Solicitud $solicitud;

    public ?string $observaciones_sacad = null;

    public ?string $reject_reason = null;

    public function mount(Solicitud $solicitud): void
    {
        $this->solicitud = $solicitud->load([
            'owner',
            'tipoSolicitud',
            'motivo',
            'estatus',
            'recursos',
            'documentos',
            'visitante',
            'requerimientos.requerimiento',
        ]);

        $this->authorize('review', $this->solicitud);

        $this->observaciones_sacad = $this->solicitud->observaciones_sacad;
        $this->reject_reason = $this->solicitud->reject_reason;
    }

    public function aprobar(SolicitudServiceInterface $solicitudService)
    {
        $this->authorize('approve', $this->solicitud);

        $validated = $this->validate([
            'observaciones_sacad' => ['nullable', 'string', 'max:5000'],
        ]);

        $identityId = activeIdentityLinkId();

        abort_if(! $identityId, 403, 'No se encontró una identidad institucional activa.');

        $this->solicitud = $solicitudService->aprobarCi(
            $this->solicitud,
            $identityId,
            $validated['observaciones_sacad'] ?? null
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Solicitud aprobada correctamente.'
        );

        return redirect()->route('solicitudes.show', $this->solicitud);
    }

    public function rechazar(SolicitudServiceInterface $solicitudService)
    {
        $this->authorize('reject', $this->solicitud);

        $validated = $this->validate([
            'reject_reason' => ['required', 'string', 'max:5000'],
        ]);

        $identityId = activeIdentityLinkId();

        abort_if(! $identityId, 403, 'No se encontró una identidad institucional activa.');

        $this->solicitud = $solicitudService->rechazarCi(
            $this->solicitud,
            $identityId,
            $validated['reject_reason']
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Solicitud rechazada correctamente.'
        );

        return redirect()->route('solicitudes.show', $this->solicitud);
    }

    public function render()
    {
        return view('livewire.solicitudes.solicitudes-review');
    }
}
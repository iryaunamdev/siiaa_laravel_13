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
    }

    public function aprobar(SolicitudServiceInterface $solicitudService)
    {
        $this->authorize('approve', $this->solicitud);

        $validated = $this->validate([
            'observaciones_sacad' => ['nullable', 'string', 'max:5000'],
        ]);

        $identityId = \currentIdentityId();

        abort_if(! $identityId, 403, 'No se encontro una identidad institucional activa.');

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
            'observaciones_sacad' => ['required', 'string', 'max:5000'],
        ]);

        $identityId = \currentIdentityId();

        abort_if(! $identityId, 403, 'No se encontro una identidad institucional activa.');

        $this->solicitud = $solicitudService->rechazarCi(
            $this->solicitud,
            $identityId,
            $validated['observaciones_sacad']
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Solicitud no aprobada correctamente.'
        );

        return redirect()->route('solicitudes.show', $this->solicitud);
    }

    public function render()
    {
        return view('livewire.solicitudes.solicitudes-review');
    }
}

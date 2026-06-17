<?php

namespace App\Livewire\Solicitudes;

use App\Models\Solicitudes\Solicitud;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class SolicitudesShow extends Component
{
    use AuthorizesRequests;

    public Solicitud $solicitud;

    public function mount(Solicitud $solicitud): void
    {
        $this->solicitud = $solicitud->load([
            'owner',
            'creador',
            'actualizador',
            'enviadoPor',
            'tipoSolicitud',
            'motivo',
            'estatus',
            'pais',
            'tutor',
            'recursos.origen',
            'recursos.cuotaDivisa',
            'recursos.avionDivisa',
            'recursos.otroDivisa',
            'documentos.uploadedBy',
            'visitante',
            'requerimientos.requerimiento',
        ]);

        $this->authorize('view', $this->solicitud);
    }

    public function render()
    {
        return view('livewire.solicitudes.solicitudes-show');
    }
}

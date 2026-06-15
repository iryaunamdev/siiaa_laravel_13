<?php

namespace App\Livewire\Solicitudes;

use App\Models\Solicitudes\Solicitud;
use App\Services\Solicitudes\SolicitudServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class SolicitudesEdit extends Component
{
    use AuthorizesRequests;

    public Solicitud $solicitud;

    public array $form = [
        'tipo_solicitud_id' => null,
        'motivo_id' => null,
        'fecha_inicio' => null,
        'fecha_fin' => null,
        'informacion_adicional' => null,
    ];

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
            'requerimientos',
        ]);

        $this->authorize('update', $this->solicitud);

        $this->form = [
            'tipo_solicitud_id' => $this->solicitud->tipo_solicitud_id,
            'motivo_id' => $this->solicitud->motivo_id,
            'fecha_inicio' => optional($this->solicitud->fecha_inicio)->format('Y-m-d'),
            'fecha_fin' => optional($this->solicitud->fecha_fin)->format('Y-m-d'),
            'informacion_adicional' => $this->solicitud->informacion_adicional,
        ];
    }

    public function save(SolicitudServiceInterface $solicitudService): void
    {
        $this->authorize('update', $this->solicitud);

        $validated = $this->validate([
            'form.tipo_solicitud_id' => ['required', 'integer', 'exists:catalogos_items,id'],
            'form.motivo_id' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'form.fecha_inicio' => ['nullable', 'date'],
            'form.fecha_fin' => ['nullable', 'date', 'after_or_equal:form.fecha_inicio'],
            'form.informacion_adicional' => ['nullable', 'string', 'max:5000'],
        ]);

        $identityId = \currentIdentityId();

        abort_if(! $identityId, 403, 'No se encontro una identidad institucional activa.');

        $this->solicitud = $solicitudService->actualizar(
            $this->solicitud,
            $validated['form'],
            $identityId
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Solicitud actualizada correctamente.'
        );
    }

    public function enviar(SolicitudServiceInterface $solicitudService)
    {
        $this->authorize('send', $this->solicitud);

        $identityId = \currentIdentityId();

        abort_if(! $identityId, 403, 'No se encontro una identidad institucional activa.');

        $this->solicitud = $solicitudService->enviar(
            $this->solicitud,
            $identityId
        );

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Solicitud enviada correctamente.'
        );

        return redirect()->route('solicitudes.show', $this->solicitud);
    }

    public function render()
    {
        return view('livewire.solicitudes.solicitudes-edit');
    }
}

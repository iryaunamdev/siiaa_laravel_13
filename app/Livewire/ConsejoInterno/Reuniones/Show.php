<?php

namespace App\Livewire\ConsejoInterno\Reuniones;

use App\Models\ConsejoInterno\CiPunto;
use App\Models\ConsejoInterno\CiReunion;
use App\Services\ConsejoInterno\CiReunionServiceInterface;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Show extends Component
{
    use AuthorizesRequests;

    public CiReunion $reunion;

    public array $evaluaciones = [];

    public array $comentarios = [];

    public function mount(CiReunion $reunion): void
    {
        $this->authorize('view', $reunion);

        $this->reunion = $reunion;

        $this->cargarReunion();

        $this->cargarEvaluacionesPropias();
    }

    private function usuarioPuedeEvaluarSinIdentidad(): bool
    {
        return auth()->user()?->hasAnyRole(['admin-sistema', 'super-admin']) ?? false;
    }

    public function evaluar(int $puntoId, CiReunionServiceInterface $reunionService): void
    {
        $punto = CiPunto::query()
            ->where('reunion_id', $this->reunion->id)
            ->with('reunion')
            ->findOrFail($puntoId);

        $this->authorize('evaluate', $punto);

        $identityId = currentIdentityId();

        if (blank($identityId) && ! $this->usuarioPuedeEvaluarSinIdentidad()) {
            abort(403, 'No se encontró una identidad institucional activa.');
        }

        $data = $this->validate([
            "evaluaciones.{$puntoId}" => [
                'required',
                'string',
                Rule::in(ConsejoInternoCatalogos::evaluaciones()),
            ],
            "comentarios.{$puntoId}" => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $reunionService->evaluarPunto(
            $punto,
            $identityId,
            $data['evaluaciones'][$puntoId],
            $data['comentarios'][$puntoId] ?? null
        );

        $this->cargarReunion();

        $this->cargarEvaluacionesPropias();

        session()->flash('status', 'Evaluación registrada correctamente.');
    }

    private function cargarReunion(): void
    {
        $this->reunion = $this->reunion->fresh([
            'participantes.identidad',

            'puntosSolicitud.solicitud.owner',
            'puntosSolicitud.solicitud.tipoSolicitud',
            'puntosSolicitud.solicitud.estatus',
            'puntosSolicitud.evaluaciones.evaluacion',
            'puntosSolicitud.evaluaciones.identidad',

            'puntosOtros.evaluaciones.evaluacion',
            'puntosOtros.evaluaciones.identidad',
            'puntosOtros.resolvedBy',

            'documentos.uploadedBy',
            'concluidaPor',
        ])->loadCount([
            'participantes',
            'puntosSolicitud',
            'puntosOtros',
            'documentos',
        ]);
    }

    private function cargarEvaluacionesPropias(): void
    {
        $identityId = currentIdentityId();

        $evaluacionAdministrativa = blank($identityId)
            && $this->usuarioPuedeEvaluarSinIdentidad();

        if (blank($identityId) && ! $evaluacionAdministrativa) {
            return;
        }

        $puntos = $this->reunion->puntosSolicitud
            ->merge($this->reunion->puntosOtros);

        foreach ($puntos as $punto) {
            $evaluacion = $punto->evaluaciones
                ->first(function ($evaluacion) use ($identityId, $evaluacionAdministrativa) {
                    if ($evaluacionAdministrativa) {
                        return blank($evaluacion->identity_link_id);
                    }

                    return (int) $evaluacion->identity_link_id === (int) $identityId;
                });

            if (! $evaluacion) {
                continue;
            }

            $this->evaluaciones[$punto->id] = $evaluacion->evaluacion?->clave;
            $this->comentarios[$punto->id] = $evaluacion->comentarios;
        }
    }

    public function render()
    {
        return view('livewire.consejo-interno.reuniones.show', [
            'evaluacionOptions' => ConsejoInternoCatalogos::evaluaciones(),
        ])->layout('layouts.app');
    }
}

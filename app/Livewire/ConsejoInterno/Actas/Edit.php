<?php

namespace App\Livewire\ConsejoInterno\Actas;

use App\Models\ConsejoInterno\CiActa;
use App\Models\ConsejoInterno\CiReunion;
use App\Services\ConsejoInterno\CiActaServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    use AuthorizesRequests;

    public ?CiActa $acta = null;

    public string $reunion_id = '';
    public string $numero_acta = '';
    public string $titulo = '';
    public ?string $fecha = null;

    public function mount(?CiActa $acta = null): void
    {
        $this->acta = $acta?->exists
            ? $acta
            : null;

        if ($this->acta) {
            $this->authorize('update', $this->acta);

            $this->acta->load([
                'reunion',
                'creador',
                'actualizador',
                'publicadaPor',
            ]);

            $this->reunion_id = filled($this->acta->reunion_id)
                ? (string) $this->acta->reunion_id
                : '';

            $this->numero_acta = (string) $this->acta->numero_acta;
            $this->titulo = (string) $this->acta->titulo;
            $this->fecha = $this->acta->fecha?->format('Y-m-d');

            return;
        }

        $this->authorize('create', CiActa::class);
    }

    public function guardar(
        CiActaServiceInterface $actaService
    ): void {
        $data = $this->validate($this->rules());

        $identityId = currentIdentityId();

        if (
            blank($identityId)
            && ! $this->usuarioPuedeOperarSinIdentidad()
        ) {
            abort(
                403,
                'No se encontró una identidad institucional activa.'
            );
        }

        if ($this->acta) {
            $this->authorize('update', $this->acta);

            $this->acta = $actaService->actualizar(
                $this->acta,
                $data,
                $identityId
            );

            session()->flash(
                'status',
                'El acta fue actualizada correctamente.'
            );

            return;
        }

        $this->authorize('create', CiActa::class);

        $this->acta = $actaService->crear(
            $data,
            $identityId
        );

        session()->flash(
            'status',
            'El acta fue creada correctamente.'
        );

        $this->redirectRoute(
            'consejo-interno.actas.edit',
            $this->acta,
            navigate: true
        );
    }

    private function rules(): array
    {
        return [
            'reunion_id' => [
                'nullable',
                'integer',
                Rule::exists('ci_reuniones', 'id'),
            ],

            'numero_acta' => [
                'required',
                'string',
                'max:100',
            ],

            'titulo' => [
                'required',
                'string',
                'max:255',
            ],

            'fecha' => [
                'required',
                'date',
            ],
        ];
    }

    private function usuarioPuedeOperarSinIdentidad(): bool
    {
        return auth()->user()?->hasAnyRole([
            'admin-sistema',
            'super-admin',
        ]) ?? false;
    }

    public function render()
    {
        $reuniones = CiReunion::query()
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get([
                'id',
                'titulo',
                'fecha',
                'estatus',
            ]);

        return view(
            'livewire.consejo-interno.actas.edit',
            [
                'reuniones' => $reuniones,
            ]
        )->layout('layouts.app');
    }
}

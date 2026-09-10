<?php

namespace App\Livewire\ConsejoInterno\Actas;

use App\Models\ConsejoInterno\CiActa;
use App\Services\ConsejoInterno\CiActaServiceInterface;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $estatus = '';

    #[Url]
    public string $year = '';

    public int $perPage = 15;

    public function mount(): void
    {
        $this->authorize('viewAny', CiActa::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEstatus(): void
    {
        $this->resetPage();
    }

    public function updatingYear(): void
    {
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset([
            'search',
            'estatus',
            'year',
        ]);

        $this->resetPage();
    }

    public function eliminar(
        int $actaId,
        CiActaServiceInterface $actaService
    ): void {
        $acta = CiActa::query()->findOrFail($actaId);

        $this->authorize('delete', $acta);

        $actaService->eliminar($acta);

        $this->resetPage();

        session()->flash(
            'status',
            'El acta fue eliminada correctamente.'
        );
    }

    private function aplicarVisibilidad(Builder $query): void
    {
        $user = auth()->user();

        /*
         * Gestión puede consultar todo.
         */
        if ($user->can('ci.actas_manage')) {
            return;
        }

        $puedeVer = $user->can('ci.actas_view');

        $puedeEvaluar = $user->can('ci.actas_evaluate')
            || $user->hasAnyRole([
                'admin-sistema',
                'super-admin',
            ]);

        /*
         * Tiene ambos permisos:
         * publicadas + borradores que puede evaluar.
         */
        if ($puedeVer && $puedeEvaluar) {
            $query->where(function (Builder $query): void {
                $query
                    ->where(
                        'estatus',
                        ConsejoInternoCatalogos::ACTA_PUBLICADA
                    )
                    ->orWhere(function (Builder $query): void {
                        $this->aplicarActasEvaluables($query);
                    });
            });

            return;
        }

        /*
         * Solo consulta institucional.
         */
        if ($puedeVer) {
            $query->where(
                'estatus',
                ConsejoInternoCatalogos::ACTA_PUBLICADA
            );

            return;
        }

        /*
         * Solo evaluación.
         */
        if ($puedeEvaluar) {
            $this->aplicarActasEvaluables($query);

            return;
        }

        /*
         * Seguridad adicional.
         */
        $query->whereRaw('1 = 0');
    }

    private function aplicarActasEvaluables(Builder $query): void
    {
        $query->where(
            'estatus',
            ConsejoInternoCatalogos::ACTA_BORRADOR
        );

        $user = auth()->user();

        /*
         * Admin técnico: todos los borradores.
         */
        if ($user->hasAnyRole([
            'admin-sistema',
            'super-admin',
        ])) {
            return;
        }

        $identityId = currentIdentityId();

        if (blank($identityId)) {
            $query->whereRaw('1 = 0');

            return;
        }

        /*
         * Actas independientes o actas de reuniones
         * donde participa la identidad actual.
         */
        $query->where(function (Builder $query) use ($identityId): void {
            $query
                ->whereNull('reunion_id')
                ->orWhereHas(
                    'reunion.participantes',
                    function (Builder $query) use ($identityId): void {
                        $query->where(
                            'identity_link_id',
                            $identityId
                        );
                    }
                );
        });
    }

    public function render()
    {

        $query = CiActa::query()
            ->with('reunion');

        $this->aplicarVisibilidad($query);

        $query
            ->buscar($this->search)
            ->when(
                $this->estatus !== '',
                fn (Builder $query) => $query->where(
                    'estatus',
                    $this->estatus
                )
            )
            ->when(
                $this->year !== '',
                fn (Builder $query) => $query->where(
                    'year',
                    (int) $this->year
                )
            );

        $actas = $query
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate($this->perPage);

        $years = CiActa::query()
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view(
            'livewire.consejo-interno.actas.index',
            [
                'actas' => $actas,
                'years' => $years,
                'estatusOptions' => ConsejoInternoCatalogos::estadosActa(),
            ]
        )->layout('layouts.app');
    }
}

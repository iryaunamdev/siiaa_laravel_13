<?php

namespace App\Livewire\Estudiantes;

use App\Models\Siiap\CatalogoItem as SiiapCatalogoItem;
use App\Models\Siiap\Estudiante;
use App\Models\Siiap\EstudianteInscripcion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?string $grado = null;

    public bool $showResumenModal = false;

    public ?Estudiante $selectedEstudiante = null;

    public ?EstudianteInscripcion $ultimaInscripcion = null;

    public Collection $historialInscripciones;

    public Collection $comiteReciente;

    public Collection $c_grados;

    public function mount(): void
    {
        $this->historialInscripciones = collect();
        $this->comiteReciente = collect();

        $this->c_grados = SiiapCatalogoItem::query()
            ->porCatalogo('GRADOS')
            ->activo()
            ->whereIn('clave', ['MAE', 'DOC'])
            ->get();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedGrado(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'grado',
        ]);

        $this->resetPage();
    }

    public function openResumen(int $estudianteId): void
    {
        $this->selectedEstudiante = Estudiante::query()
            ->activosIrya()
            ->with([
                'sexo',
                'nacionalidad',

                // Ingresos: el programa base se obtiene desde aquí.
                'ingresos.semestre',
                'ingresos.grado',
                'ingresos.universidad',
                'ingresos.programa',
                'ingresos.procedencia',

                // Inscripciones: se usan para ficha, historial y comité.
                'inscripciones.semestre',
                'inscripciones.grado',
                'inscripciones.programa',
                'inscripciones.adscripcion',
                'inscripciones.comite.tutor',
                'inscripciones.comite.tutor.grado',
                'inscripciones.comite.tutor.adscripcion',
                'inscripciones.comite.tutor.sni',
                'inscripciones.comite.tutor.pride',
                'inscripciones.comite.tutor.contrato',
            ])
            ->findOrFail($estudianteId);

        $this->historialInscripciones = $this->selectedEstudiante->inscripciones
            ->sortByDesc(fn($inscripcion) => semestreOrdenValue($inscripcion->semestre?->nombre))
            ->values();

        $this->ultimaInscripcion = $this->historialInscripciones
            ->filter(fn($inscripcion) => $this->isInscripcionActivaIrya($inscripcion))
            ->first();

        $this->comiteReciente = $this->ultimaInscripcion?->comite ?? collect();

        $this->showResumenModal = true;
    }

    public function closeResumen(): void
    {
        $this->showResumenModal = false;

        $this->selectedEstudiante = null;
        $this->ultimaInscripcion = null;
        $this->historialInscripciones = collect();
        $this->comiteReciente = collect();
    }

    public function render()
    {
        $query = Estudiante::query()
            ->activosIrya()
            ->with([
                'sexo',
                'nacionalidad',

                // Para mostrar programa/grado desde ingresos si la vista lo requiere.
                'ingresos.grado',
                'ingresos.programa',
                'ingresos.semestre',

                // Para mostrar última inscripción y comité resumido.
                'inscripciones.semestre',
                'inscripciones.grado',
                'inscripciones.programa',
                'inscripciones.adscripcion',
                'inscripciones.comite.tutor',
            ])
            ->withCount([
                'ingresos',
                'inscripciones',
            ])
            ->ordenadoPorNombre();

        $this->applySearch($query);
        $this->applyGradoFilter($query);

        return view('livewire.estudiantes.index', [
            'estudiantes' => $query->paginate(15),
        ]);
    }

    protected function applySearch(Builder $query): void
    {
        $search = trim($this->search);

        if ($search === '') {
            return;
        }

        $query->where(function (Builder $query) use ($search) {
            $query
                ->where('nombre', 'like', "%{$search}%")
                ->orWhere('apellidop', 'like', "%{$search}%")
                ->orWhere('apellidom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");

            if (is_numeric($search)) {
                $query->orWhere('id', (int) $search);
            }
        });
    }

    protected function applyGradoFilter(Builder $query): void
    {
        if (! $this->grado) {
            return;
        }

        $query->whereHas('inscripciones', function (Builder $query) {
            $query
                ->activasIrya()
                ->whereHas('grado', function (Builder $query) {
                    $query->where('clave', $this->grado);
                });
        });
    }

    protected function isInscripcionActivaIrya(EstudianteInscripcion $inscripcion): bool
    {
        $semestres = ultimosTresSemestres(currentSemestre());

        return $inscripcion->adscripcion?->clave === 'IRyA'
            && in_array($inscripcion->semestre?->nombre, $semestres, true);
    }
}

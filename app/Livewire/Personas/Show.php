<?php

namespace App\Livewire\Personas;

use App\Models\Persona;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Show extends Component
{
    use AuthorizesRequests;

    public Persona $persona;

    public string $section = 'ingresos';

    public function mount(Persona $persona): void
    {
        $this->authorize('personas.view');

        /*
         * Se cargan relaciones generales de la ficha para evitar consultas
         * repetidas desde la vista y mantener la presentación en modo lectura.
         */
        $this->persona = $persona->load([
            'sexo',
            'nacionalidad',
            'identityLink.perfilAcademico.sni',
            'identityLink.perfilAcademico.pride',
            'identityLink.perfilPublico',
            'posdocBecas.beca',
            'posdocBecas.asesor',
        ]);
    }

    public function setSection(string $section): void
    {
        if (! in_array($section, $this->availableSections(), true)) {
            return;
        }

        $this->section = $section;
    }

    protected function availableSections(): array
    {
        return [
            'general',
            'ingresos',
            'academico',
            'publico',
            'posdoc_becas',
        ];
    }

    public function render()
    {
        return view('livewire.personas.show', [
            'ingresoPrincipal' => $this->persona->ingresoPrincipal()
                ->with([
                    'tipoPersonal',
                    'contrato',
                    'nombramiento',
                    'escolaridad',
                ])
                ->first(),

            'ingresos' => $this->persona->ingresos()
                ->with([
                    'tipoPersonal',
                    'contrato',
                    'nombramiento',
                    'escolaridad',
                ])
                ->orderByDesc('principal')
                ->orderByDesc('activo')
                ->orderByDesc('fecha_ingreso')
                ->get(),

            'perfilAcademico' => $this->persona->identityLink?->perfilAcademico,

            /*
         * Perfil público resuelto desde identityLink, no desde Persona.
         */
            'perfilPublico' => $this->persona->identityLink?->perfilPublico,

            'identityLink' => $this->persona->identityLink,

            'posdocBecas' => $this->persona->posdocBecas()
                ->with([
                    'beca',
                    'asesor',
                ])
                ->orderByDesc('principal')
                ->orderByDesc('activo')
                ->orderByDesc('fecha_inicio')
                ->get(),
        ]);
    }
}
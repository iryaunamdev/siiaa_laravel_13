<?php

namespace App\Livewire\Personas;

use App\Models\IdentityLink;
use App\Models\PerfilPublico;
use App\Models\Persona;
use App\Models\Siiap\Estudiante;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MiPerfil extends Component
{
    public ?IdentityLink $identityLink = null;

    public ?Persona $persona = null;

    public ?Estudiante $estudiante = null;

    public ?string $homepage_url = null;

    public ?string $observaciones = null;

    public function mount(): void
    {
        $this->identityLink = $this->resolveCurrentIdentity();

        if (! $this->identityLink) {
            abort(403, 'No tienes una identidad institucional asociada.');
        }

        $this->identityLink->load([
            'perfilPublico',
            'perfilAcademico.sni',
            'perfilAcademico.pride',
        ]);

        $this->loadIdentitySource();
        $this->fillEditableFields();
    }

    protected function resolveCurrentIdentity(): ?IdentityLink
    {
        $identityId = session('current_identity_id')
            ?? session('identity_link_id')
            ?? session('current_identity.link_id')
            ?? null;

        if ($identityId) {
            return IdentityLink::query()
                ->where('active', true)
                ->find($identityId);
        }

        $user = Auth::user();

        if (! $user) {
            return null;
        }

        return IdentityLink::query()
            ->where('email', $user->email)
            ->where('active', true)
            ->first();
    }

    protected function loadIdentitySource(): void
    {
        if ($this->identityLink->identity_type === IdentityLink::TYPE_SIIAA) {
            $this->persona = Persona::query()
                ->with([
                    'sexo',
                    'nacionalidad',
                    'identityLink.perfilPublico',
                    'identityLink.perfilAcademico.sni',
                    'identityLink.perfilAcademico.pride',
                    'ingresoPrincipal.tipoPersonal',
                    'ingresoPrincipal.contrato',
                    'ingresoPrincipal.nombramiento',
                    'ingresoPrincipal.escolaridad',
                ])
                ->find($this->identityLink->identity_id);

            return;
        }

        if ($this->identityLink->identity_type === IdentityLink::TYPE_SIIAP_STUDENT) {
            $this->estudiante = Estudiante::query()
                ->with([
                    'inscripciones.semestre',
                    'inscripciones.grado',
                    'inscripciones.programa',
                    'inscripciones.adscripcion',
                ])
                ->find($this->identityLink->identity_id);
        }
    }

    protected function fillEditableFields(): void
    {
        $perfilPublico = $this->perfilPublico;

        $this->homepage_url = $perfilPublico?->homepage_url;
        $this->observaciones = $perfilPublico?->observaciones;
    }

    public function getIsPersonalIryaProperty(): bool
    {
        return $this->identityLink?->identity_type === IdentityLink::TYPE_SIIAA;
    }

    public function getIsEstudianteSiiapProperty(): bool
    {
        return $this->identityLink?->identity_type === IdentityLink::TYPE_SIIAP_STUDENT;
    }

    public function getNombreBaseProperty(): string
    {
        if ($this->isPersonalIrya && $this->persona) {
            return trim(collect([
                $this->persona->nombre,
                $this->persona->apellidop,
                $this->persona->apellidom,
            ])->filter()->implode(' '));
        }

        if ($this->isEstudianteSiiap && $this->estudiante) {
            return trim(collect([
                $this->estudiante->nombre,
                $this->estudiante->apellidop,
                $this->estudiante->apellidom,
            ])->filter()->implode(' '));
        }

        return 'Sin nombre asociado';
    }

    public function getIngresoActualProperty()
    {
        return $this->persona?->ingresoPrincipal;
    }

    public function getPerfilPublicoProperty(): ?PerfilPublico
    {
        return $this->identityLink?->perfilPublico;
    }

    public function getPerfilAcademicoProperty()
    {
        return $this->identityLink?->perfilAcademico;
    }

    public function getInscripcionActualProperty()
    {
        return $this->estudiante?->inscripcion_actual;
    }

    public function getComiteTutorActualProperty()
    {
        return $this->inscripcionActual?->comite ?? collect();
    }

    protected function rules(): array
    {
        return [
            'homepage_url' => ['nullable', 'url', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:1500'],
        ];
    }

    public function savePublicFields(): void
    {
        if (! $this->isPersonalIrya) {
            abort(403, 'Solo el personal IRyA puede editar estos datos.');
        }

        $data = $this->validate();

        $perfilPublico = PerfilPublico::query()->firstOrNew([
            'identity_link_id' => $this->identityLink->id,
        ]);

        /*
         * Solo se actualizan campos permitidos desde Mi perfil.
         * Los campos editoriales se conservan tal como están:
         * active, visible, sort_order, directorio_tipo.
         */
        $perfilPublico->homepage_url = $data['homepage_url'] ?? null;
        $perfilPublico->observaciones = $data['observaciones'] ?? null;
        $perfilPublico->save();

        $this->identityLink->refresh();
        $this->identityLink->load([
            'perfilPublico',
            'perfilAcademico.sni',
            'perfilAcademico.pride',
        ]);

        $this->fillEditableFields();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Tu perfil fue actualizado correctamente.'
        );
    }

    public function getPerfilPublicadoProperty(): bool
    {
        return (bool) (
            $this->perfilPublico?->active
            && $this->perfilPublico?->visible
        );
    }

    public function getTienePerfilPublicoProperty(): bool
    {
        return (bool) $this->perfilPublico;
    }

    public function render()
    {
        return view('livewire.personas.mi-perfil');
    }
}
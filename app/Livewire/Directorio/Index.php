<?php

namespace App\Livewire\Directorio;

use App\Models\IdentityLink;
use App\Models\PerfilPublico;
use App\Models\PersonaPerfilAcademico;
use App\Models\Persona;
use App\Models\Siiap\Estudiante;
use App\Services\Directorio\DirectorioQueryService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $tipo = 'todos';

    #[Url(as: 'estado')]
    public string $estadoPerfil = 'todos';

    public bool $editMode = false;

    public int $perPage = 15;

    public array $rows = [];

    public function mount(): void
    {
        $this->authorize('directorio.view');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();

        if ($this->editMode) {
            $this->editMode = false;
            $this->rows = [];
        }
    }

    public function updatingTipo(): void
    {
        $this->resetPage();

        if ($this->editMode) {
            $this->editMode = false;
            $this->rows = [];
        }
    }

    public function updatingEstadoPerfil(): void
    {
        $this->resetPage();

        if ($this->editMode) {
            $this->editMode = false;
            $this->rows = [];
        }
    }

    public function toggleEditMode(): void
    {
        $this->authorize('directorio.update');

        $this->editMode = ! $this->editMode;

        if ($this->editMode) {
            $this->loadEditableRows();
        }
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'tipo',
            'estadoPerfil',
        ]);

        $this->resetPage();
    }

    public function exportUrl(string $format): string
    {
        return route('directorio.export', [
            'format' => $format,
            'q' => $this->search,
            'tipo' => $this->tipo,
            'estado' => $this->estadoPerfil,
        ]);
    }

    public function publicFeedUrl(string $format = 'json'): string
    {
        return url("/api/public/directorio.{$format}");
    }

    public function loadEditableRows(): void
    {
        $directorio = app(DirectorioQueryService::class);

        $identidades = $directorio->query([
            'search' => $this->search,
            'tipo' => $this->tipo,
            'estado_perfil' => $this->estadoPerfil,
        ])
            ->limit(100)
            ->get();

        $this->rows = [];

        foreach ($identidades as $identity) {
            $registro = $directorio->mapIdentity($identity);

            $this->rows[$identity->id] = [
                'identity_link_id' => $identity->id,
                'directorio_tipo' => $registro['directorio_tipo'],
                'sort_order' => $registro['sort_order'],

                // Perfil público: español / inglés
                'titulo_es' => $registro['titulo_es'],
                'titulo_en' => $registro['titulo_en'],
                'nombre_publico' => $registro['nombre_publico'],
                'apellido_publico' => $registro['apellido_publico'],
                'area_es' => $registro['area_es'],
                'area_en' => $registro['area_en'],

                // Contacto público
                'oficina' => $registro['oficina'],
                'extension_red_unam' => $registro['extension_red_unam'],
                'telefono_morelia' => $registro['telefono_morelia'],
                'telefono_cdmx' => $registro['telefono_cdmx'],
                'email_publico' => $registro['email_publico'],
                'homepage_url' => $registro['homepage_url'],

                // Estado editorial
                'active' => $registro['active'],
                'visible' => $registro['visible'],

                // Perfil académico
                'orcid' => $registro['orcid'],
                'scopus_id' => $registro['scopus_id'],
                'ads_author_query' => $registro['ads_author_query'],
                'ads_profile_url' => $registro['ads_profile_url'],
                'ads_library_url' => $registro['ads_library_url'],
                'research_area' => $registro['research_area'],
                'academic_keywords' => $registro['academic_keywords'],
            ];
        }
    }

    public function saveRow(int $identityLinkId): void
    {
        $this->authorize('directorio.update');

        try {
            $this->persistRow($identityLinkId);

            $this->dispatch(
                'toast',
                type: 'success',
                message: 'Registro del directorio actualizado correctamente.'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            $this->dispatch(
                'toast',
                type: 'error',
                message: 'No se pudo guardar el registro del directorio.'
            );
        }
    }

    protected function persistRow(int $identityLinkId): void
    {
        if (! isset($this->rows[$identityLinkId])) {
            throw new \RuntimeException('No se encontró la fila que se desea guardar.');
        }

        $identity = IdentityLink::query()->find($identityLinkId);

        if (! $identity) {
            throw new \RuntimeException('No se encontró la identidad institucional.');
        }

        $data = validator($this->rows[$identityLinkId], [
            // Perfil público
            'directorio_tipo' => ['nullable', 'string', 'max:80'],
            'titulo_es' => ['nullable', 'string', 'max:50'],
            'titulo_en' => ['nullable', 'string', 'max:50'],
            'nombre_publico' => ['nullable', 'string', 'max:255'],
            'apellido_publico' => ['nullable', 'string', 'max:255'],
            'area_es' => ['nullable', 'string', 'max:255'],
            'area_en' => ['nullable', 'string', 'max:255'],
            'oficina' => ['nullable', 'string', 'max:100'],
            'extension_red_unam' => ['nullable', 'string', 'max:50'],
            'telefono_morelia' => ['nullable', 'string', 'max:50'],
            'telefono_cdmx' => ['nullable', 'string', 'max:50'],
            'email_publico' => ['nullable', 'email', 'max:255'],
            'homepage_url' => ['nullable', 'url', 'max:255'],
            'active' => ['boolean'],
            'visible' => ['boolean'],

            // Perfil académico
            'orcid' => ['nullable', 'string', 'max:50'],
            'scopus_id' => ['nullable', 'string', 'max:100'],
            'ads_author_query' => ['nullable', 'string', 'max:255'],
            'ads_profile_url' => ['nullable', 'url', 'max:255'],
            'ads_library_url' => ['nullable', 'url', 'max:255'],
            'research_area' => ['nullable', 'string', 'max:1000'],
            'academic_keywords' => ['nullable', 'string', 'max:1000'],
        ])->validate();

        /*
     * El tipo se calcula desde la identidad real.
     * No se toma directamente desde el formulario para evitar inconsistencias.
     */
        $directorioTipo = $this->resolveDirectorioTipoForIdentity($identity);

        $publicData = [
            'directorio_tipo' => $directorioTipo,
            'titulo_es' => $data['titulo_es'] ?? null,
            'titulo_en' => $data['titulo_en'] ?? null,
            'nombre_publico' => $data['nombre_publico'] ?? null,
            'apellido_publico' => $data['apellido_publico'] ?? null,
            'area_es' => $data['area_es'] ?? null,
            'area_en' => $data['area_en'] ?? null,
            'oficina' => $data['oficina'] ?? null,
            'extension_red_unam' => $data['extension_red_unam'] ?? null,
            'telefono_morelia' => $data['telefono_morelia'] ?? null,
            'telefono_cdmx' => $data['telefono_cdmx'] ?? null,
            'email_publico' => $data['email_publico'] ?? null,
            'homepage_url' => $data['homepage_url'] ?? null,
            'active' => (bool) ($data['active'] ?? false),
            'visible' => (bool) ($data['visible'] ?? false),
            'sort_order' => $data['sort_order'] ?? null,
        ];

        $hasPublicData = collect($publicData)
            ->except(['active', 'visible'])
            ->filter(fn($value) => filled($value))
            ->isNotEmpty();

        if ($hasPublicData || $publicData['active'] || $publicData['visible']) {
            PerfilPublico::query()->updateOrCreate(
                ['identity_link_id' => $identity->id],
                $publicData
            );
        }

        $academicData = [
            'orcid' => $data['orcid'] ?? null,
            'scopus_id' => $data['scopus_id'] ?? null,
            'ads_author_query' => $data['ads_author_query'] ?? null,
            'ads_profile_url' => $data['ads_profile_url'] ?? null,
            'ads_library_url' => $data['ads_library_url'] ?? null,
            'research_area' => $data['research_area'] ?? null,
            'academic_keywords' => $data['academic_keywords'] ?? null,
        ];

        $hasAcademicData = collect($academicData)
            ->filter(fn($value) => filled($value))
            ->isNotEmpty();

        if ($hasAcademicData) {
            PersonaPerfilAcademico::query()->updateOrCreate(
                ['identity_link_id' => $identity->id],
                $academicData
            );
        }
    }

    public function saveVisibleRows(): void
    {
        $this->authorize('directorio.update');

        if (empty($this->rows)) {
            $this->dispatch(
                'toast',
                type: 'warning',
                message: 'No hay filas cargadas para guardar.'
            );

            return;
        }

        $saved = 0;
        $errors = 0;

        foreach (array_keys($this->rows) as $identityLinkId) {
            try {
                $this->persistRow((int) $identityLinkId);
                $saved++;
            } catch (\Throwable $e) {
                report($e);
                $errors++;
            }
        }

        if ($errors > 0) {
            $this->dispatch(
                'toast',
                type: 'warning',
                message: "Se guardaron {$saved} registros, pero {$errors} no pudieron guardarse."
            );

            return;
        }

        $this->loadEditableRows();

        $this->dispatch(
            'toast',
            type: 'success',
            message: "Se guardaron {$saved} registros del directorio."
        );
    }

    protected function resolveDirectorioTipoForIdentity(IdentityLink $identity): string
    {
        if ($identity->identity_type === IdentityLink::TYPE_SIIAA) {
            return $this->resolveTipoPersona($identity);
        }

        if ($identity->identity_type === IdentityLink::TYPE_SIIAP_STUDENT) {
            return $this->resolveTipoEstudiante($identity);
        }

        return 'otro';
    }

    protected function resolveTipoPersona(IdentityLink $identity): string
    {
        $persona = Persona::query()
            ->with('ingresoPrincipal.tipoPersonal')
            ->find($identity->identity_id);

        $clave = strtoupper((string) $persona?->ingresoPrincipal?->tipoPersonal?->clave);
        $nombre = strtolower((string) $persona?->ingresoPrincipal?->tipoPersonal?->nombre);

        return match (true) {
            $clave === 'INV' || str_contains($nombre, 'investig') => 'investigador',
            $clave === 'TAC' || str_contains($nombre, 'técnico') || str_contains($nombre, 'tecnico') => 'tecnico_academico',
            $clave === 'ADM' || str_contains($nombre, 'admin') => 'administrativo',
            $clave === 'POS' || str_contains($nombre, 'posdoc') => 'posdoctorado',
            $clave === 'SSO' || str_contains($nombre, 'social') => 'servicio_social',
            $clave === 'TSI' || str_contains($nombre, 'sindic') => 'trabajador_sindicalizado',
            default => 'personal_irya',
        };
    }

    protected function resolveTipoEstudiante(IdentityLink $identity): string
    {
        $estudiante = Estudiante::query()
            ->find($identity->identity_id);

        if (! $estudiante) {
            return 'estudiante';
        }

        $gradoClave = strtoupper((string) $estudiante->grado_actual_clave);
        $gradoNombre = strtolower((string) $estudiante->grado_actual_nombre);

        return match (true) {
            $gradoClave === 'DOC' || str_contains($gradoNombre, 'doctor') => 'estudiante_doctorado',
            $gradoClave === 'MAE' || str_contains($gradoNombre, 'maestr') => 'estudiante_maestria',
            default => 'estudiante',
        };
    }

    public function render(DirectorioQueryService $directorio)
    {
        $identidades = $directorio->paginate([
            'search' => $this->search,
            'tipo' => $this->tipo,
            'estado_perfil' => $this->estadoPerfil,
        ], $this->perPage);

        return view('livewire.directorio.index', [
            'identidades' => $identidades,
            'directorioService' => $directorio,
        ]);
    }
}
<?php

namespace App\Livewire\Sys\PerfilesPublicos;

use App\Models\IdentityLink;
use App\Models\PerfilPublico;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Perfiles\PerfilPublicoService;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $identityType = '';

    public string $visibility = '';

    public string $status = '';

    public int $perPage = 15;

    public bool $profileModal = false;

    public ?int $editingId = null;

    public ?string $titulo_es = null;

    public ?string $titulo_en = null;

    public ?string $nombre_publico = null;

    public ?string $apellido_publico = null;

    public ?string $area_es = null;

    public ?string $area_en = null;

    public ?string $oficina = null;

    public ?string $extension_red_unam = null;

    public ?string $telefono_morelia = null;

    public ?string $telefono_cdmx = null;

    public ?string $email_publico = null;

    public ?string $homepage_url = null;

    public ?string $observaciones = null;

    public bool $active = true;

    public bool $visible = false;

    public int $sort_order = 0;

    public bool $syncModal = false;

    public string $syncIdentityType = '';

    public bool $syncVisible = false;

    public bool $syncFillMissingData = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'identityType' => ['except' => ''],
        'visibility' => ['except' => ''],
        'status' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    protected function rules(): array
    {
        return [
            'titulo_es' => ['nullable', 'string', 'max:100'],
            'titulo_en' => ['nullable', 'string', 'max:100'],
            'nombre_publico' => ['nullable', 'string', 'max:150'],
            'apellido_publico' => ['nullable', 'string', 'max:150'],
            'area_es' => ['nullable', 'string', 'max:255'],
            'area_en' => ['nullable', 'string', 'max:255'],
            'oficina' => ['nullable', 'string', 'max:100'],
            'extension_red_unam' => ['nullable', 'string', 'max:50'],
            'telefono_morelia' => ['nullable', 'string', 'max:50'],
            'telefono_cdmx' => ['nullable', 'string', 'max:50'],
            'email_publico' => ['nullable', 'email', 'max:150'],
            'homepage_url' => ['nullable', 'url', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'active' => ['boolean'],
            'visible' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingIdentityType(): void
    {
        $this->resetPage();
    }

    public function updatingVisibility(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'identityType',
            'visibility',
            'status',
        ]);

        $this->resetPage();
    }

    public function edit(int $perfilId): void
    {
        $perfil = PerfilPublico::query()
            ->with('identityLink')
            ->findOrFail($perfilId);

        $this->editingId = $perfil->id;

        $this->titulo_es = $perfil->titulo_es;
        $this->titulo_en = $perfil->titulo_en;
        $this->nombre_publico = $perfil->nombre_publico;
        $this->apellido_publico = $perfil->apellido_publico;
        $this->area_es = $perfil->area_es;
        $this->area_en = $perfil->area_en;
        $this->oficina = $perfil->oficina;
        $this->extension_red_unam = $perfil->extension_red_unam;
        $this->telefono_morelia = $perfil->telefono_morelia;
        $this->telefono_cdmx = $perfil->telefono_cdmx;
        $this->email_publico = $perfil->email_publico;
        $this->homepage_url = $perfil->homepage_url;
        $this->observaciones = $perfil->observaciones;
        $this->active = (bool) $perfil->active;
        $this->visible = (bool) $perfil->visible;
        $this->sort_order = (int) $perfil->sort_order;

        $this->profileModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $perfil = PerfilPublico::query()->findOrFail($this->editingId);

        $perfil->update([
            'titulo_es' => $this->titulo_es,
            'titulo_en' => $this->titulo_en,
            'nombre_publico' => $this->nombre_publico,
            'apellido_publico' => $this->apellido_publico,
            'area_es' => $this->area_es,
            'area_en' => $this->area_en,
            'oficina' => $this->oficina,
            'extension_red_unam' => $this->extension_red_unam,
            'telefono_morelia' => $this->telefono_morelia,
            'telefono_cdmx' => $this->telefono_cdmx,
            'email_publico' => $this->email_publico,
            'homepage_url' => $this->homepage_url,
            'observaciones' => $this->observaciones,
            'active' => $this->active,
            'visible' => $this->visible,
            'sort_order' => $this->sort_order,
        ]);

        $this->profileModal = false;

        $this->dispatch('toast', type: 'success', message: 'Perfil público actualizado correctamente.');
    }

    public function toggleVisible(int $perfilId): void
    {
        $perfil = PerfilPublico::query()->findOrFail($perfilId);

        $perfil->update([
            'visible' => ! $perfil->visible,
        ]);

        $this->dispatch('toast', type: 'success', message: 'Visibilidad actualizada correctamente.');
    }

    public function toggleActive(int $perfilId): void
    {
        $perfil = PerfilPublico::query()->findOrFail($perfilId);

        $perfil->update([
            'active' => ! $perfil->active,
        ]);

        $this->dispatch('toast', type: 'success', message: 'Estado actualizado correctamente.');
    }

    public function closeModal(): void
    {
        $this->profileModal = false;
        $this->resetValidation();
    }

    public function getPerfilesProperty()
    {
        return PerfilPublico::query()
            ->with('identityLink')
            ->when($this->identityType !== '', function (Builder $query) {
                $query->whereHas('identityLink', function (Builder $query) {
                    $query->where('identity_type', $this->identityType);
                });
            })
            ->when($this->visibility !== '', function (Builder $query) {
                $query->where('visible', $this->visibility === 'visible');
            })
            ->when($this->status !== '', function (Builder $query) {
                $query->where('active', $this->status === 'active');
            })
            ->when($this->search !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $query) use ($search) {
                    $query->where('nombre_publico', 'like', "%{$search}%")
                        ->orWhere('apellido_publico', 'like', "%{$search}%")
                        ->orWhere('email_publico', 'like', "%{$search}%")
                        ->orWhere('area_es', 'like', "%{$search}%")
                        ->orWhereHas('identityLink', function (Builder $query) use ($search) {
                            $query->where('email', 'like', "%{$search}%")
                                ->orWhere('identity_type', 'like', "%{$search}%");
                        });
                });
            })
            ->ordenados()
            ->paginate($this->perPage);
    }

    public function openSyncModal(): void
    {
        $this->syncIdentityType = $this->identityType ?: '';
        $this->syncVisible = false;
        $this->syncFillMissingData = true;

        $this->syncModal = true;
    }

    public function closeSyncModal(): void
    {
        $this->syncModal = false;
    }

    public function syncPublicProfiles(PerfilPublicoService $perfilService): void
    {
        if ($this->syncIdentityType === '') {
            $identityTypes = [
                IdentityLink::TYPE_SIIAA,
                IdentityLink::TYPE_SIIAP_STUDENT,
            ];

            foreach ($identityTypes as $identityType) {
                if ($this->syncFillMissingData) {
                    $perfilService->firstOrCreateAndFillManyByIdentityType(
                        identityType: $identityType,
                        visible: $this->syncVisible
                    );
                } else {
                    $perfilService->firstOrCreateManyByIdentityType(
                        identityType: $identityType,
                        visible: $this->syncVisible
                    );
                }
            }

            $this->syncModal = false;

            $this->dispatch(
                'toast',
                type: 'success',
                message: 'Perfiles públicos sincronizados correctamente.'
            );

            return;
        }

        if ($this->syncFillMissingData) {
            $perfilService->firstOrCreateAndFillManyByIdentityType(
                identityType: $this->syncIdentityType,
                visible: $this->syncVisible
            );
        } else {
            $perfilService->firstOrCreateManyByIdentityType(
                identityType: $this->syncIdentityType,
                visible: $this->syncVisible
            );
        }

        $this->syncModal = false;

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Perfiles públicos sincronizados correctamente.'
        );
    }

    public function render()
    {
        return view('livewire.sys.perfiles-publicos.index', [
            'perfiles' => $this->perfiles,
            'identityTypes' => [
                IdentityLink::TYPE_SIIAA => 'Personal SIIAA',
                IdentityLink::TYPE_SIIAP_STUDENT => 'Estudiantes SIIAP',
            ],
        ]);
    }
}
<?php

namespace App\Livewire\Solicitudes;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use App\Models\Solicitudes\Solicitud;
use App\Services\Solicitudes\SolicitudServiceInterface;
use App\Support\Solicitudes\SolicitudCatalogos;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class SolicitudesEdit extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public Solicitud $solicitud;

    public int $paso = 1;

    public array $form = [
        'owner_id' => null,
        'tipo_solicitud_id' => null,
        'requiere_recursos' => false,
        'motivo_id' => null,
        'motivo_otro' => null,
        'fecha_inicio' => null,
        'fecha_fin' => null,
        'pais_id' => null,
        'nombre_evento' => null,
        'tipo_presentacion' => null,
        'institucion' => null,
        'anfitrion' => null,
        'lugar' => null,
        'tutor_id' => null,
        'informacion_adicional' => null,
        'requiere_seguro_unam' => false,
        'seguro_unam_beneficiario' => null,
    ];

    public array $visitanteForm = [
        'tipo_visitante_id' => null,
        'estudiante_asociado_id' => null,
        'nombre' => null,
        'apellidos' => null,
        'email' => null,
        'pais_id' => null,
        'institucion_id' => null,
        'institucion' => null,
        'lugar' => null,
        'fecha_inicio' => null,
        'fecha_fin' => null,
    ];

    public array $requerimientosSeleccionados = [], $recursosForm = [], $documentosUpload = [];

    public bool $can_manage_owner = false,  $can_modify_owner = false;

    /* catalogos */
    public Collection $c_tipos_solicitud, $c_motivos, $c_paises, $c_tutores, $c_tipos_visitante, $c_requerimientos_visitante, $c_owner_identities;
    public Collection $c_origenes_recurso, $c_divisas;

    public function mount(Solicitud $solicitud): void
    {
        $this->solicitud = $solicitud->load([
            'owner',
            'tipoSolicitud',
            'motivo',
            'estatus',
            'recursos',
            'documentos.uploadedBy',
            'visitante',
            'requerimientos',
        ]);

        $this->authorize('update', $this->solicitud);

        $this->can_manage_owner = $this->canManageOwner();
        $this->can_modify_owner = $this->canModifyOwner();

        $this->c_tipos_solicitud = $this->catalogoItems('SOLTIPOS');
        $this->c_motivos = $this->catalogoItems('SOLMOT');
        $this->c_paises = $this->catalogoItems('PAISES');
        $this->c_tutores = $this->tutorIdentities();
        $this->c_tipos_visitante = $this->catalogoItems('C_SOLTVIS');
        $this->c_requerimientos_visitante = $this->catalogoItems('VIS_REQ');
        $this->c_origenes_recurso = $this->catalogoItems('C_OREC');
        $this->c_divisas = $this->catalogoItems('DIVISAS');
        $this->c_owner_identities = $this->can_manage_owner ? $this->ownerIdentities() : collect();

        $this->form = [
            'owner_id' => $this->solicitud->owner_id,
            'tipo_solicitud_id' => $this->solicitud->tipo_solicitud_id,
            'requiere_recursos' => (bool) $this->solicitud->requiere_recursos,
            'motivo_id' => $this->solicitud->motivo_id,
            'motivo_otro' => $this->solicitud->motivo_otro,
            'fecha_inicio' => optional($this->solicitud->fecha_inicio)->format('Y-m-d'),
            'fecha_fin' => optional($this->solicitud->fecha_fin)->format('Y-m-d'),
            'pais_id' => $this->solicitud->pais_id,
            'nombre_evento' => $this->solicitud->nombre_evento,
            'tipo_presentacion' => $this->solicitud->tipo_presentacion,
            'institucion' => $this->solicitud->institucion,
            'anfitrion' => $this->solicitud->anfitrion,
            'lugar' => $this->solicitud->lugar,
            'tutor_id' => $this->solicitud->tutor_id,
            'informacion_adicional' => $this->solicitud->informacion_adicional,
            'requiere_seguro_unam' => (bool) $this->solicitud->requiere_seguro_unam,
            'seguro_unam_beneficiario' => $this->solicitud->seguro_unam_beneficiario,
        ];

        $this->visitanteForm = [
            'tipo_visitante_id' => $this->solicitud->visitante?->tipo_visitante_id,
            'estudiante_asociado_id' => $this->solicitud->visitante?->estudiante_asociado_id,
            'nombre' => $this->solicitud->visitante?->nombre,
            'apellidos' => $this->solicitud->visitante?->apellidos,
            'email' => $this->solicitud->visitante?->email,
            'pais_id' => $this->solicitud->visitante?->pais_id,
            'institucion_id' => $this->solicitud->visitante?->institucion_id,
            'institucion' => $this->solicitud->visitante?->institucion,
            'lugar' => $this->solicitud->visitante?->lugar,
            'fecha_inicio' => optional($this->solicitud->visitante?->fecha_inicio)->format('Y-m-d'),
            'fecha_fin' => optional($this->solicitud->visitante?->fecha_fin)->format('Y-m-d'),
        ];

        $this->requerimientosSeleccionados = $this->solicitud->requerimientos
            ->pluck('requerimiento_id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        $this->recursosForm = $this->solicitud->recursos
            ->map(fn($recurso) => [
                'origen_id' => $recurso->origen_id,
                'proyecto_id' => $recurso->proyecto_id,
                'proyecto_nombre' => $recurso->proyecto_nombre,
                'dias_n' => $recurso->dias_n,
                'dias_i' => $recurso->dias_i,
                'cuota' => $recurso->cuota,
                'cuota_divisa' => $recurso->cuota_divisa,
                'avion' => $recurso->avion,
                'avion_divisa' => $recurso->avion_divisa,
                'otro' => $recurso->otro,
                'otro_divisa' => $recurso->otro_divisa,
                'informacion_adicional' => $recurso->informacion_adicional,
            ])
            ->values()
            ->all();

        if ($this->solicitud->requiere_recursos && empty($this->recursosForm)) {
            $this->agregarRecurso();
        }
    }

    public function guardarPaso1(SolicitudServiceInterface $solicitudService): void
    {
        $this->authorize('update', $this->solicitud);

        $validated = $this->validate([
            'form.owner_id' => [
                $this->can_modify_owner ? 'required' : 'nullable',
                'nullable',
                'integer',
                'exists:identity_links,id',
            ],
            'form.tipo_solicitud_id' => ['required', 'integer', 'exists:catalogos_items,id'],
            'form.requiere_recursos' => ['boolean'],
            'form.motivo_id' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'form.motivo_otro' => ['nullable', 'string', 'max:255'],
            'form.fecha_inicio' => ['nullable', 'date'],
            'form.fecha_fin' => ['nullable', 'date', 'after_or_equal:form.fecha_inicio'],
            'form.pais_id' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'form.nombre_evento' => ['nullable', 'string', 'max:255'],
            'form.tipo_presentacion' => ['nullable', 'string', 'max:255'],
            'form.institucion' => ['nullable', 'string', 'max:255'],
            'form.anfitrion' => ['nullable', 'string', 'max:255'],
            'form.lugar' => ['nullable', 'string', 'max:255'],
            'form.tutor_id' => ['nullable', 'integer', 'exists:identity_links,id'],
            'form.informacion_adicional' => ['nullable', 'string', 'max:5000'],
            'form.requiere_seguro_unam' => ['boolean'],
            'form.seguro_unam_beneficiario' => ['nullable', 'string', 'max:255'],
        ]);

        $tipoSeleccionado = $this->c_tipos_solicitud
            ->firstWhere('id', (int) $validated['form']['tipo_solicitud_id']);

        if ($tipoSeleccionado) {
            $validated['form']['requiere_recursos'] = SolicitudCatalogos::requiereRecursosPorTipo($tipoSeleccionado->clave)
                || (
                    SolicitudCatalogos::recursosSeleccionablesPorTipo($tipoSeleccionado->clave)
                    && (bool) ($this->form['requiere_recursos'] ?? false)
                );
        }

        $identityId = $this->actorIdentityId(allowManageWithoutIdentity: true);

        if (! $this->can_modify_owner) {
            unset($validated['form']['owner_id']);
        }

        $this->solicitud = $solicitudService->actualizar($this->solicitud, $validated['form'], $identityId);

        $this->dispatch('toast', type: 'success', message: 'Datos generales guardados correctamente.');
    }

    public function esTipoVisitante(): bool
    {
        $tipoId = $this->form['tipo_solicitud_id'] ?? $this->solicitud->tipo_solicitud_id;

        if (! $tipoId) {
            return false;
        }

        return $this->c_tipos_solicitud
            ->firstWhere('id', (int) $tipoId)
            ?->clave === SolicitudCatalogos::TIPO_VISITANTE;
    }

    protected function divisaMxnId(): ?int
    {
        return $this->c_divisas
            ->firstWhere('clave', SolicitudCatalogos::DIVISA_MXN)
            ?->id;
    }

    public function guardarVisitante(SolicitudServiceInterface $solicitudService): void
    {
        $this->authorize('update', $this->solicitud);

        $validated = $this->validate([
            'visitanteForm.tipo_visitante_id' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'visitanteForm.estudiante_asociado_id' => ['nullable', 'integer', 'exists:estudiantes_asociados,id'],
            'visitanteForm.nombre' => ['nullable', 'string', 'max:255'],
            'visitanteForm.apellidos' => ['nullable', 'string', 'max:255'],
            'visitanteForm.email' => ['nullable', 'email', 'max:255'],
            'visitanteForm.pais_id' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'visitanteForm.institucion_id' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'visitanteForm.institucion' => ['nullable', 'string', 'max:255'],
            'visitanteForm.lugar' => ['nullable', 'string', 'max:255'],
            'visitanteForm.fecha_inicio' => ['nullable', 'date'],
            'visitanteForm.fecha_fin' => ['nullable', 'date', 'after_or_equal:visitanteForm.fecha_inicio'],
        ]);

        $identityId = $this->actorIdentityId(allowManageWithoutIdentity: true);

        $this->solicitud = $solicitudService
            ->guardarVisitante($this->solicitud, $validated['visitanteForm'], $identityId)
            ->load(['owner', 'tipoSolicitud', 'motivo', 'estatus', 'recursos', 'documentos.uploadedBy', 'visitante', 'requerimientos']);

        $this->dispatch('toast', type: 'success', message: 'Visitante guardado correctamente.');
    }

    public function guardarRequerimientos(SolicitudServiceInterface $solicitudService): void
    {
        $this->authorize('update', $this->solicitud);

        if (! $this->esTipoVisitante()) {
            $this->requerimientosSeleccionados = [];
        }

        $validated = $this->validate([
            'requerimientosSeleccionados' => ['array'],
            'requerimientosSeleccionados.*' => ['integer', 'exists:catalogos_items,id'],
        ]);

        $identityId = $this->actorIdentityId(allowManageWithoutIdentity: true);

        $this->solicitud = $solicitudService
            ->sincronizarRequerimientos(
                $this->solicitud,
                $validated['requerimientosSeleccionados'] ?? [],
                $identityId
            )
            ->load(['owner', 'tipoSolicitud', 'motivo', 'estatus', 'recursos', 'documentos.uploadedBy', 'visitante', 'requerimientos.requerimiento']);

        $this->dispatch('toast', type: 'success', message: 'Requerimientos guardados correctamente.');
    }

    public function enviarSolicitud(SolicitudServiceInterface $solicitudService)
    {
        $this->authorize('send', $this->solicitud);

        $identityId = $this->actorIdentityId(allowManageWithoutIdentity: true);

        $this->solicitud = $solicitudService->enviar($this->solicitud, $identityId);

        $this->dispatch('toast', type: 'success', message: 'Solicitud enviada correctamente.');

        return redirect()->route('solicitudes.show', $this->solicitud);
    }

    public function irAPaso(int $paso): void
    {
        if ($paso < 1 || $paso > 4) {
            return;
        }

        if ($paso === 2 && ! $this->solicitud->requiere_recursos) {
            return;
        }

        $this->paso = $paso;
    }

    public function pasoAnterior(): void
    {
        if ($this->paso <= 1) {
            return;
        }

        $this->paso--;

        if ($this->paso === 2 && ! $this->solicitud->requiere_recursos) {
            $this->paso = 1;
        }
    }

    public function pasoSiguiente(): void
    {
        if ($this->paso >= 4) {
            return;
        }

        $this->paso++;

        if ($this->paso === 2 && ! $this->solicitud->requiere_recursos) {
            $this->paso = 3;
        }
    }

    protected function actorIdentityId(bool $allowManageWithoutIdentity = false): ?int
    {
        $identityId = \currentIdentityId();

        if ($identityId) {
            return $identityId;
        }

        if ($allowManageWithoutIdentity && auth()->user()?->can('solicitudes.manage')) {
            return null;
        }

        abort(403, 'No se encontro una identidad institucional activa.');
    }

    protected function canManageOwner(): bool
    {
        return auth()->user()?->can('solicitudes.manage') ?? false;
    }

    protected function canModifyOwner(): bool
    {
        if (! $this->canManageOwner()) {
            return false;
        }

        return $this->solicitud->created_by === null
            || (int) $this->solicitud->created_by !== (int) $this->solicitud->owner_id;
    }

    protected function catalogoItems(string $catalogoClave)
    {
        return CatalogoItem::query()
            ->whereHas('catalogo', fn($query) => $query->where('clave', $catalogoClave))
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();
    }

    protected function ownerIdentities()
    {
        return IdentityLink::query()
            ->activas()
            ->orderBy('identity_type')
            ->orderBy('email')
            ->get();
    }

    protected function tutorIdentities()
    {
        return IdentityLink::query()
            ->activas()
            ->where('identity_type', 'persona')
            ->orderBy('email')
            ->get();
    }

    public function agregarRecurso(): void
    {
        $mxnId = $this->divisaMxnId();

        $this->recursosForm[] = [
            'origen_id' => null,
            'proyecto_id' => null,
            'proyecto_nombre' => null,
            'dias_n' => null,
            'dias_i' => null,
            'cuota' => null,
            'cuota_divisa' => $mxnId,
            'avion' => null,
            'avion_divisa' => $mxnId,
            'otro' => null,
            'otro_divisa' => $mxnId,
            'informacion_adicional' => null,
        ];
    }

    public function quitarRecurso(int $index): void
    {
        unset($this->recursosForm[$index]);

        $this->recursosForm = array_values($this->recursosForm);
    }

    public function guardarRecursos(SolicitudServiceInterface $solicitudService): void
    {
        $this->authorize('update', $this->solicitud);

        if (! $this->solicitud->requiere_recursos) {
            $this->recursosForm = [];
        }

        $validated = $this->validate([
            'recursosForm' => ['array'],
            'recursosForm.*.origen_id' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'recursosForm.*.proyecto_id' => ['nullable', 'integer'],
            'recursosForm.*.proyecto_nombre' => ['nullable', 'string', 'max:255'],
            'recursosForm.*.dias_n' => ['nullable', 'integer', 'min:0'],
            'recursosForm.*.dias_i' => ['nullable', 'integer', 'min:0'],
            'recursosForm.*.cuota' => ['nullable', 'numeric', 'min:0'],
            'recursosForm.*.cuota_divisa' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'recursosForm.*.avion' => ['nullable', 'numeric', 'min:0'],
            'recursosForm.*.avion_divisa' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'recursosForm.*.otro' => ['nullable', 'numeric', 'min:0'],
            'recursosForm.*.otro_divisa' => ['nullable', 'integer', 'exists:catalogos_items,id'],
            'recursosForm.*.informacion_adicional' => ['nullable', 'string', 'max:5000'],
        ]);

        $identityId = $this->actorIdentityId(allowManageWithoutIdentity: true);

        $recursos = collect($validated['recursosForm'] ?? [])
            ->filter(function (array $recurso) {
                return collect($recurso)
                    ->except(['cuota_divisa', 'avion_divisa', 'otro_divisa'])
                    ->filter(fn($value) => ! blank($value))
                    ->isNotEmpty();
            })
            ->values()
            ->all();

        $this->solicitud = $solicitudService
            ->sincronizarRecursos($this->solicitud, $recursos, $identityId)
            ->load(['owner', 'tipoSolicitud', 'motivo', 'estatus', 'recursos', 'documentos.uploadedBy', 'visitante', 'requerimientos.requerimiento']);

        $this->dispatch('toast', type: 'success', message: 'Recursos guardados correctamente.');
    }

    public function subirDocumentos(SolicitudServiceInterface $solicitudService): void
    {
        $this->authorize('update', $this->solicitud);

        $validated = $this->validate([
            'documentosUpload' => ['required', 'array', 'min:1'],
            'documentosUpload.*' => ['file', 'max:10240'],
        ]);

        $identityId = $this->actorIdentityId(allowManageWithoutIdentity: true);

        foreach ($validated['documentosUpload'] as $documento) {
            $solicitudService->adjuntarDocumento($this->solicitud, $documento, $identityId);
        }

        $this->reset('documentosUpload');

        $this->solicitud = $this->solicitud
            ->refresh()
            ->load([
                'owner',
                'tipoSolicitud',
                'motivo',
                'estatus',
                'recursos',
                'documentos.uploadedBy',
                'visitante',
                'requerimientos.requerimiento',
            ]);

        $this->dispatch('toast', type: 'success', message: 'Documento(s) adjuntado(s) correctamente.');
    }

    public function eliminarDocumento(int $documentoId, SolicitudServiceInterface $solicitudService): void
    {
        $this->authorize('update', $this->solicitud);

        $documento = $this->solicitud->documentos()
            ->whereKey($documentoId)
            ->firstOrFail();

        $identityId = $this->actorIdentityId(allowManageWithoutIdentity: true);

        $solicitudService->eliminarDocumento($documento, $identityId);

        $this->solicitud = $this->solicitud
            ->refresh()
            ->load([
                'owner',
                'tipoSolicitud',
                'motivo',
                'estatus',
                'recursos',
                'documentos.uploadedBy',
                'visitante',
                'requerimientos.requerimiento',
            ]);

        $this->dispatch('toast', type: 'success', message: 'Documento eliminado correctamente.');
    }

    public function render()
    {
        return view('livewire.solicitudes.solicitudes-edit');
    }
}

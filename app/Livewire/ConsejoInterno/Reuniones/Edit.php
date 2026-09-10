<?php

namespace App\Livewire\ConsejoInterno\Reuniones;

use App\Models\ConsejoInterno\CiReunion;
use App\Models\IdentityLink;
use App\Services\ConsejoInterno\CiReunionServiceInterface;
use App\Support\ConsejoInterno\ConsejoInternoCatalogos;
use App\Models\Solicitudes\Solicitud;
use App\Support\Solicitudes\SolicitudCatalogos;
use App\Models\ConsejoInterno\CiNotificacion;
use App\Services\ConsejoInterno\CiNotificacionServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public ?CiReunion $reunion = null;

    public string $titulo = '';
    public ?string $fecha = null;
    public string $tipo_reunion = ConsejoInternoCatalogos::TIPO_REUNION_DEFAULT;
    public string $modalidad = ConsejoInternoCatalogos::MODALIDAD_DEFAULT;
    public string $estatus = ConsejoInternoCatalogos::REUNION_EN_PROCESO;

    public string $participanteSearch = '';
    public string $nuevoParticipanteId = '';

    public string $solicitudSearch = '';
    public string $nuevaSolicitudId = '';
    public string $otroPuntoTitulo = '';
    public string $otroPuntoDescripcion = '';
    public ?TemporaryUploadedFile $nuevoDocumento = null;
    public array $resoluciones = [];

    public function mount(?CiReunion $reunion = null): void
    {
        $this->reunion = $reunion?->exists ? $reunion : null;

        if ($this->reunion) {
            $this->authorize('update', $this->reunion);

            $this->reunion = $this->reunion->load([
                'participantes.identidad',

                'puntosSolicitud.solicitud.owner',
                'puntosSolicitud.solicitud.tipoSolicitud',
                'puntosSolicitud.solicitud.estatus',
                'puntosSolicitud.evaluaciones.evaluacion',
                'puntosSolicitud.evaluaciones.identidad',
                'puntosSolicitud.resolvedBy',

                'puntosOtros.evaluaciones.evaluacion',
                'puntosOtros.evaluaciones.identidad',
                'puntosOtros.resolvedBy',

                'documentos.uploadedBy',

                'notificaciones.solicitud',
            ])->loadCount([
                'participantes',
                'puntosSolicitud',
                'puntosOtros',
                'documentos',
            ]);

            $this->titulo = (string) $this->reunion->titulo;
            $this->fecha = $this->reunion->fecha?->format('Y-m-d');
            $this->tipo_reunion = (string) $this->reunion->tipo_reunion;
            $this->modalidad = (string) $this->reunion->modalidad;
            $this->estatus = (string) $this->reunion->estatus;

            $this->cargarResoluciones();

            return;
        }

        $this->authorize('create', CiReunion::class);
    }

    private function usuarioPuedeOperarSinIdentidad(): bool
    {
        return auth()->user()?->hasAnyRole(['admin-sistema', 'super-admin']) ?? false;
    }

    public function guardar(CiReunionServiceInterface $reunionService): void
    {
        $data = $this->validate($this->rulesDatosBase());

        $identityId = currentIdentityId();

        if (blank($identityId) && ! auth()->user()?->hasAnyRole(['admin-sistema', 'super-admin'])) {
            abort(403, 'No se encontró una identidad institucional activa.');
        }

        if ($this->reunion) {
            $this->authorize('update', $this->reunion);

            $this->reunion = $reunionService->actualizar(
                $this->reunion,
                $data,
                $identityId
            );

            $this->cargarReunion();

            session()->flash('status', 'La reunión fue actualizada correctamente.');

            return;
        }

        $this->authorize('create', CiReunion::class);

        $this->reunion = $reunionService->crear($data, $identityId);

        session()->flash('status', 'La reunión fue creada correctamente.');

        $this->redirectRoute(
            'consejo-interno.reuniones.edit',
            $this->reunion,
            navigate: true
        );
    }

    private function cargarReunion(): void
    {
        if (! $this->reunion) {
            return;
        }

        $this->reunion = $this->reunion->fresh([
            'participantes.identidad',

            'puntosSolicitud.solicitud.owner',
            'puntosSolicitud.solicitud.tipoSolicitud',
            'puntosSolicitud.solicitud.estatus',
            'puntosSolicitud.evaluaciones.evaluacion',
            'puntosSolicitud.evaluaciones.identidad',
            'puntosSolicitud.resolvedBy',

            'puntosOtros.evaluaciones.evaluacion',
            'puntosOtros.evaluaciones.identidad',
            'puntosOtros.resolvedBy',

            'documentos.uploadedBy',

            'notificaciones.solicitud',
        ])->loadCount([
            'participantes',
            'puntosSolicitud',
            'puntosOtros',
            'documentos',
        ]);
    }

    private function rulesDatosBase(): array
    {
        return [
            'titulo' => [
                'required',
                'string',
                'max:255',
            ],
            'fecha' => [
                'required',
                'date',
            ],
            'tipo_reunion' => [
                'required',
                'string',
                'max:100',
            ],
            'modalidad' => [
                'required',
                'string',
                'max:100',
            ],
            'estatus' => [
                'required',
                'string',
                Rule::in(ConsejoInternoCatalogos::estadosReunion()),
            ],
        ];
    }

    public function agregarParticipante(CiReunionServiceInterface $reunionService): void
    {
        if (! $this->reunion) {
            return;
        }

        $this->authorize('update', $this->reunion);

        $data = $this->validate([
            'nuevoParticipanteId' => [
                'required',
                'integer',
                Rule::exists('identity_links', 'id')
                    ->where(fn($query) => $query->where('active', true)),
            ],
        ]);

        $reunionService->agregarParticipante(
            $this->reunion,
            (int) $data['nuevoParticipanteId']
        );

        $this->reset('nuevoParticipanteId', 'participanteSearch');

        $this->cargarReunion();

        session()->flash('status', 'Participante agregado correctamente.');
    }

    public function quitarParticipante(int $identityLinkId, CiReunionServiceInterface $reunionService): void
    {
        if (! $this->reunion) {
            return;
        }

        $this->authorize('update', $this->reunion);

        $reunionService->quitarParticipante($this->reunion, $identityLinkId);

        $this->cargarReunion();

        session()->flash('status', 'Participante retirado correctamente.');
    }



    public function agregarSolicitud(CiReunionServiceInterface $reunionService): void
    {
        if (! $this->reunion) {
            return;
        }

        $this->authorize('update', $this->reunion);

        $data = $this->validate([
            'nuevaSolicitudId' => [
                'required',
                'integer',
                Rule::exists('solicitudes', 'id'),
            ],
        ]);

        try {
            $reunionService->agregarSolicitud(
                $this->reunion,
                (int) $data['nuevaSolicitudId']
            );

            $this->reset('nuevaSolicitudId', 'solicitudSearch');

            $this->cargarReunion();

            session()->flash('status', 'Solicitud agregada correctamente.');
        } catch (\InvalidArgumentException $exception) {
            $this->addError('nuevaSolicitudId', $exception->getMessage());
        }
    }

    public function quitarSolicitud(int $solicitudId, CiReunionServiceInterface $reunionService): void
    {
        if (! $this->reunion) {
            return;
        }

        $this->authorize('update', $this->reunion);

        $reunionService->quitarSolicitud($this->reunion, $solicitudId);

        $this->cargarReunion();

        session()->flash('status', 'Solicitud retirada correctamente.');
    }

    public function agregarOtroPunto(CiReunionServiceInterface $reunionService): void
    {
        if (! $this->reunion) {
            return;
        }

        $this->authorize('update', $this->reunion);

        $data = $this->validate([
            'otroPuntoTitulo' => [
                'required',
                'string',
                'max:255',
            ],
            'otroPuntoDescripcion' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $reunionService->agregarOtroPunto($this->reunion, [
            'titulo'      => $data['otroPuntoTitulo'],
            'descripcion' => $data['otroPuntoDescripcion'],
        ]);

        $this->reset('otroPuntoTitulo', 'otroPuntoDescripcion');

        $this->cargarReunion();

        session()->flash('status', 'Punto agregado correctamente.');
    }

    public function quitarOtroPunto(int $puntoId, CiReunionServiceInterface $reunionService): void
    {
        if (! $this->reunion) {
            return;
        }

        $this->authorize('update', $this->reunion);

        $reunionService->quitarOtroPunto($this->reunion, $puntoId);

        $this->cargarReunion();

        session()->flash('status', 'Punto retirado correctamente.');
    }

    public function subirDocumento(CiReunionServiceInterface $reunionService): void
    {
        if (! $this->reunion) {
            return;
        }

        $this->authorize('update', $this->reunion);

        $this->validate([
            'nuevoDocumento' => [
                'required',
                'file',
                'max:10240',
            ],
        ]);

        $identityId = currentIdentityId();

        if (blank($identityId) && ! auth()->user()?->hasAnyRole(['admin-sistema', 'super-admin'])) {
            abort(403, 'No se encontró una identidad institucional activa.');
        }

        $reunionService->subirDocumento(
            $this->reunion,
            $this->nuevoDocumento,
            $identityId
        );

        $this->reset('nuevoDocumento');

        $this->cargarReunion();

        session()->flash('status', 'Documento cargado correctamente.');
    }

    public function eliminarDocumento(int $documentoId, CiReunionServiceInterface $reunionService): void
    {
        if (! $this->reunion) {
            return;
        }

        $this->authorize('update', $this->reunion);

        $reunionService->eliminarDocumento($this->reunion, $documentoId);

        $this->cargarReunion();

        session()->flash('status', 'Documento eliminado correctamente.');
    }

    public function resolverPunto(int $puntoId, CiReunionServiceInterface $reunionService): void
    {
        if (! $this->reunion) {
            return;
        }

        $punto = $this->reunion
            ->puntos()
            ->where('id', $puntoId)
            ->firstOrFail();

        $this->authorize('resolve', $punto);

        $data = $this->validate([
            "resoluciones.{$puntoId}" => [
                'required',
                'string',
                Rule::in(ConsejoInternoCatalogos::resolucionesFinales()),
            ],
        ]);

        $identityId = currentIdentityId();

        if (blank($identityId) && ! $this->usuarioPuedeOperarSinIdentidad()) {
            abort(403, 'No se encontró una identidad institucional activa.');
        }

        try {
            $reunionService->resolverPunto(
                $punto,
                $data['resoluciones'][$puntoId],
                $identityId
            );

            $this->cargarReunion();

            $this->cargarResoluciones();

            session()->flash(
                'status',
                'Resolución registrada correctamente. Si corresponde, la solicitud fue actualizada y su notificación quedó encolada.'
            );
        } catch (\InvalidArgumentException $exception) {
            $this->addError("resoluciones.{$puntoId}", $exception->getMessage());
        }
    }

    public function reenviarNotificacion(
        int $notificacionId,
        CiNotificacionServiceInterface $notificacionService
    ): void {
        if (! $this->reunion) {
            return;
        }

        $this->authorize('update', $this->reunion);

        $notificacion = CiNotificacion::query()
            ->where('reunion_id', $this->reunion->id)
            ->findOrFail($notificacionId);

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

        $notificacionService->reenviar(
            $notificacion,
            $identityId
        );

        $this->cargarReunion();

        session()->flash(
            'status',
            'La notificación fue encolada nuevamente.'
        );
    }

    private function cargarResoluciones(): void
    {
        if (! $this->reunion) {
            return;
        }

        $puntos = $this->reunion->puntosSolicitud
            ->merge($this->reunion->puntosOtros);

        foreach ($puntos as $punto) {
            if (filled($punto->resolucion)) {
                $this->resoluciones[$punto->id] = $punto->resolucion;
            }
        }
    }

    public function render()
    {
        $participantesActuales = $this->reunion
            ? $this->reunion->participantes->pluck('identity_link_id')->all()
            : [];

        $opcionesParticipantes = collect();

        $solicitudesActuales = $this->reunion
            ? $this->reunion->puntosSolicitud->pluck('solicitud_id')->filter()->all()
            : [];

        $opcionesSolicitudes = collect();

        if ($this->reunion) {
            $opcionesSolicitudes = Solicitud::query()
                ->with(['owner', 'tipoSolicitud', 'estatus'])
                ->whereNotIn('id', $solicitudesActuales)
                ->whereHas('estatus', function ($query) {
                    $query->where('clave', SolicitudCatalogos::ESTATUS_ENVIADA);
                })
                ->when(trim($this->solicitudSearch) !== '', function ($query) {
                    $search = trim($this->solicitudSearch);

                    $query->where(function ($query) use ($search) {
                        $query->where('folio', 'like', "%{$search}%")
                            ->orWhere('nombre_evento', 'like', "%{$search}%")
                            ->orWhere('institucion', 'like', "%{$search}%")
                            ->orWhere('id', $search);
                    });
                })
                ->orderByDesc('submitted_at')
                ->orderByDesc('id')
                ->limit(20)
                ->get();
        }

        return view('livewire.consejo-interno.reuniones.edit', [
            'estatusOptions' => ConsejoInternoCatalogos::estadosReunion(),
            'opcionesParticipantes' => $opcionesParticipantes,
            'opcionesSolicitudes' => $opcionesSolicitudes,
        ])->layout('layouts.app');
    }
}

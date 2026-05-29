<?php

namespace App\Services\Directorio;

use App\Models\IdentityLink;
use App\Models\Persona;
use App\Models\Siiap\Estudiante;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;



class DirectorioQueryService
{
    public function query(array $filters = []): Builder
    {
        $query = IdentityLink::query()
            ->with([
                'perfilPublico',
                'perfilAcademico.sni',
                'perfilAcademico.pride',
            ])
            ->where('identity_links.active', true);

        $this->applySearchFilter($query, $filters['search'] ?? null);
        $this->applyTipoFilter($query, $filters['tipo'] ?? null);
        $this->applyEstadoPerfilFilter($query, $filters['estado_perfil'] ?? null);

        return $query;
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyDirectoryOrder(
            $this->query($filters)
        )->paginate($perPage);
    }

    public function get(array $filters = []): Collection
    {
        return $this->applyDirectoryOrder(
            $this->query($filters)
        )
            ->get()
            ->map(fn(IdentityLink $identity) => $this->mapIdentity($identity));
    }

    public function getPublic(array $filters = []): Collection
    {
        $query = $this->query($filters)
            ->whereHas('perfilPublico', function (Builder $q) {
                $q->where('active', true)
                    ->where('visible', true);
            })
            ->where('active', true);

        return $this->applyDirectoryOrder($query)
            ->get()
            ->map(fn(IdentityLink $identity) => $this->mapPublicIdentity($identity));
    }

    protected function resolveDatosBase(IdentityLink $identity): array
    {
        if ($identity->identity_type === IdentityLink::TYPE_SIIAA) {
            $persona = Persona::query()
                ->select([
                    'id',
                    'nombre',
                    'apellidop',
                    'apellidom',
                    'email',
                ])
                ->find($identity->identity_id);

            return [
                'nombre' => $persona?->nombre,
                'apellido' => trim(collect([
                    $persona?->apellidop,
                    $persona?->apellidom,
                ])->filter()->implode(' ')) ?: null,
                'email' => $persona?->email ?: $identity->email,
            ];
        }

        if ($identity->identity_type === IdentityLink::TYPE_SIIAP_STUDENT) {
            $estudiante = Estudiante::query()
                ->select([
                    'id',
                    'nombre',
                    'apellidop',
                    'apellidom',
                    'email',
                ])
                ->find($identity->identity_id);

            return [
                'nombre' => $estudiante?->nombre,
                'apellido' => trim(collect([
                    $estudiante?->apellidop,
                    $estudiante?->apellidom,
                ])->filter()->implode(' ')) ?: null,
                'email' => $estudiante?->email ?: $identity->email,
            ];
        }

        return [
            'nombre' => null,
            'apellido' => null,
            'email' => $identity->email,
        ];
    }

    public function mapIdentity(IdentityLink $identity): array
    {
        $perfilPublico = $identity->perfilPublico;
        $perfilAcademico = $identity->perfilAcademico;
        $datosBase = $this->resolveDatosBase($identity);

        $directorioTipo = $perfilPublico?->directorio_tipo
            ?: $this->resolveDirectorioTipo($identity);

        return [
            'identity_id' => $identity->id,
            'identity_type' => $identity->identity_type,
            'identity_label' => $this->identityLabel($identity),

            'directorio_tipo' => $directorioTipo,
            'directorio_tipo_label' => $this->directorioTipoLabel($directorioTipo),
            'estado_institucional' => $this->resolveEstadoInstitucional($identity),
            'estudiante_info' => $this->resolveEstudianteInfo($identity),

            'email_base' => $identity->email,

            'titulo_es' => $perfilPublico?->titulo_es,
            'titulo_en' => $perfilPublico?->titulo_en,
            'nombre_publico' => $perfilPublico?->nombre_publico ?: $datosBase['nombre'],
            'apellido_publico' => $perfilPublico?->apellido_publico ?: $datosBase['apellido'],
            'nombre_completo' => $this->nombreCompleto(
                $perfilPublico,
                $datosBase
            ),

            'area_es' => $perfilPublico?->area_es,
            'area_en' => $perfilPublico?->area_en,
            'oficina' => $perfilPublico?->oficina,
            'extension_red_unam' => $perfilPublico?->extension_red_unam,
            'telefono_morelia' => $perfilPublico?->telefono_morelia,
            'telefono_cdmx' => $perfilPublico?->telefono_cdmx,
            'email_publico' => $perfilPublico?->email_publico ?: $datosBase['email'],
            'homepage_url' => $perfilPublico?->homepage_url,

            'orcid' => $perfilAcademico?->orcid,
            'scopus_id' => $perfilAcademico?->scopus_id,
            'ads_author_query' => $perfilAcademico?->ads_author_query,
            'ads_profile_url' => $perfilAcademico?->ads_profile_url,
            'ads_library_url' => $perfilAcademico?->ads_library_url,
            'research_area' => $perfilAcademico?->research_area,
            'academic_keywords' => $perfilAcademico?->academic_keywords,

            'perfil_publico_existe' => (bool) $perfilPublico,
            'perfil_academico_existe' => (bool) $perfilAcademico,
            'visible' => (bool) ($perfilPublico?->visible ?? false),
            'active' => (bool) ($perfilPublico?->active ?? false),
            'sort_order' => $perfilPublico?->sort_order,
        ];
    }

    public function mapPublicIdentity(IdentityLink $identity): array
    {
        $row = $this->mapIdentity($identity);

        return [
            'id' => $row['identity_id'] ?? null,
            'tipo' => $row['directorio_tipo'] ?? null,
            'tipo_label' => $row['directorio_tipo_label'] ?? null,

            'titulo_es' => $row['titulo_es'] ?? null,
            'titulo_en' => $row['titulo_en'] ?? null,
            'nombre' => $row['nombre_publico'] ?? null,
            'apellido' => $row['apellido_publico'] ?? null,
            'nombre_completo' => $row['nombre_completo'] ?? null,

            'area_es' => $row['area_es'] ?? null,
            'area_en' => $row['area_en'] ?? null,

            'oficina' => $row['oficina'] ?? null,
            'extension_red_unam' => $row['extension_red_unam'] ?? null,
            'telefono_morelia' => $row['telefono_morelia'] ?? null,
            'telefono_cdmx' => $row['telefono_cdmx'] ?? null,

            'email' => $row['email_publico'] ?? null,
            'homepage_url' => $row['homepage_url'] ?? null,

            'orcid' => $row['orcid'] ?? null,
            'ads_author_query' => $row['ads_author_query'] ?? null,
            'ads_profile_url' => $row['ads_profile_url'] ?? null,
            'ads_library_url' => $row['ads_library_url'] ?? null,
        ];
    }

    protected function applySearchFilter(Builder $query, ?string $search): void
    {
        if (! filled($search)) {
            return;
        }

        $search = trim($search);

        $query->where(function (Builder $q) use ($search) {
            $q->where('email', 'like', "%{$search}%")
                ->orWhereHas('perfilPublico', function (Builder $subquery) use ($search) {
                    $subquery
                        ->where('nombre_publico', 'like', "%{$search}%")
                        ->orWhere('apellido_publico', 'like', "%{$search}%")
                        ->orWhere('email_publico', 'like', "%{$search}%")
                        ->orWhere('area_es', 'like', "%{$search}%")
                        ->orWhere('area_en', 'like', "%{$search}%")
                        ->orWhere('oficina', 'like', "%{$search}%");
                })
                ->orWhereHas('perfilAcademico', function (Builder $subquery) use ($search) {
                    $subquery
                        ->where('orcid', 'like', "%{$search}%")
                        ->orWhere('ads_author_query', 'like', "%{$search}%")
                        ->orWhere('ads_profile_url', 'like', "%{$search}%")
                        ->orWhere('ads_library_url', 'like', "%{$search}%")
                        ->orWhere('research_area', 'like', "%{$search}%")
                        ->orWhere('academic_keywords', 'like', "%{$search}%");
                });
        });
    }

    protected function applyTipoFilter(Builder $query, ?string $tipo): void
    {
        if (! filled($tipo) || $tipo === 'todos') {
            return;
        }

        if ($tipo === 'siiaa') {
            $query->where('identity_type', IdentityLink::TYPE_SIIAA);
            return;
        }

        if ($tipo === 'siiap_student') {
            $query->where('identity_type', IdentityLink::TYPE_SIIAP_STUDENT);
            return;
        }

        $query->whereHas('perfilPublico', function (Builder $q) use ($tipo) {
            $q->where('directorio_tipo', $tipo);
        });
    }

    protected function applyEstadoPerfilFilter(Builder $query, ?string $estado): void
    {
        if (! filled($estado) || $estado === 'todos') {
            return;
        }

        match ($estado) {
            'con_perfil_publico' => $query->whereHas('perfilPublico'),
            'sin_perfil_publico' => $query->whereDoesntHave('perfilPublico'),
            'visible' => $query->whereHas('perfilPublico', function (Builder $q) {
                $q->where('visible', true)
                    ->where('active', true);
            }),
            'oculto' => $query->whereHas('perfilPublico', function (Builder $q) {
                $q->where(function (Builder $subquery) {
                    $subquery->where('visible', false)
                        ->orWhere('active', false);
                });
            }),
            default => null,
        };
    }

    protected function identityLabel(IdentityLink $identity): string
    {
        return match ($identity->identity_type) {
            IdentityLink::TYPE_SIIAA => 'Personal IRyA',
            IdentityLink::TYPE_SIIAP_STUDENT => 'Estudiante SIIAP',
            default => $identity->identity_type,
        };
    }

    protected function resolveDirectorioTipo(IdentityLink $identity): string
    {
        if ($identity->identity_type === IdentityLink::TYPE_SIIAA) {
            return $this->resolveTipoPersona($identity);
        }

        if ($identity->identity_type === IdentityLink::TYPE_SIIAP_STUDENT) {
            return $this->resolveTipoEstudiante($identity);
        }

        return 'otro';
    }

    protected function directorioTipoLabel(string $tipo): string
    {
        return match ($tipo) {
            'investigador' => 'Investigador',
            'tecnico_academico' => 'Técnico académico',
            'administrativo' => 'Administrativo',
            'posdoctorado' => 'Posdoctorado',
            'estudiante_maestria' => 'Estudiante de maestría',
            'estudiante_doctorado' => 'Estudiante de doctorado',
            'estudiante' => 'Estudiante',
            'servicio_social' => 'Servicio social',
            'trabajador_sindicalizado' => 'Trabajador sindicalizado',
            'personal_irya' => 'Personal IRyA',
            default => 'Otro',
        };
    }

    protected function resolveTipoPersona(IdentityLink $identity): string
    {
        $persona = Persona::query()
            ->with([
                'ingresoPrincipal.tipoPersonal',
            ])
            ->find($identity->identity_id);

        $clave = strtoupper((string) $persona?->ingresoPrincipal?->tipoPersonal?->clave);
        $nombre = strtolower((string) $persona?->ingresoPrincipal?->tipoPersonal?->nombre);

        return match (true) {
            $clave === 'INV' || str_contains($nombre, 'investig') => 'investigador',
            $clave === 'TAC' || str_contains($nombre, 'técnico') || str_contains($nombre, 'tecnico') => 'tecnico_academico',
            $clave === 'ADM' || str_contains($nombre, 'admin') => 'administrativo',
            $clave === 'POS' || str_contains($nombre, 'posdoc') => 'posdoctorado',
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

    protected function resolveEstudianteInfo(IdentityLink $identity): ?array
    {
        if ($identity->identity_type !== IdentityLink::TYPE_SIIAP_STUDENT) {
            return null;
        }

        $current = currentSemestre();
        $ultimosTres = ultimosTresSemestres($current);

        $estudiante = Estudiante::query()
            ->with([
                'inscripciones.semestre',
            ])
            ->find($identity->identity_id);

        if (! $estudiante) {
            return [
                'semestre_actual' => $current,
                'ultimos_tres_semestres' => $ultimosTres,
                'ultima_inscripcion' => null,
                'mensaje' => 'No se encontró el estudiante en SIIAP.',
            ];
        }

        $inscripciones = $estudiante->inscripciones
            ->filter(fn($inscripcion) => $inscripcion->semestre?->nombre)
            ->sortByDesc(fn($inscripcion) => $inscripcion->semestre?->nombre)
            ->values();

        $ultimaInscripcion = $inscripciones->first();

        return [
            'semestre_actual' => $current,
            'ultimos_tres_semestres' => $ultimosTres,
            'ultima_inscripcion' => $ultimaInscripcion?->semestre?->nombre,
            'mensaje' => $this->mensajeEstadoEstudiante($identity),
        ];
    }
    protected function nombreCompleto($perfilPublico, array $datosBase = []): ?string
    {
        $nombre = $perfilPublico?->nombre_publico ?: ($datosBase['nombre'] ?? null);
        $apellido = $perfilPublico?->apellido_publico ?: ($datosBase['apellido'] ?? null);

        $partes = collect([
            $perfilPublico?->titulo_es,
            $nombre,
            $apellido,
        ])
            ->filter()
            ->map(fn($value) => trim($value));

        return $partes->isNotEmpty()
            ? $partes->implode(' ')
            : null;
    }

    protected function resolveEstadoInstitucional(IdentityLink $identity): string
    {
        if ($identity->identity_type === IdentityLink::TYPE_SIIAP_STUDENT) {
            return $this->resolveEstadoEstudiante($identity);
        }

        return $identity->active ? 'activo' : 'inactivo';
    }

    protected function resolveEstadoEstudiante(IdentityLink $identity): string
    {
        $current = currentSemestre();
        $ultimosTres = ultimosTresSemestres($current);

        $estudiante = Estudiante::query()
            ->with([
                'inscripciones.semestre',
            ])
            ->find($identity->identity_id);

        if (! $estudiante) {
            return 'no_encontrado';
        }

        $semestresInscritos = $estudiante->inscripciones
            ->pluck('semestre.nombre')
            ->filter()
            ->unique()
            ->values();

        $tieneInscripcionActual = $semestresInscritos
            ->contains($current);

        if ($tieneInscripcionActual) {
            return 'vigente';
        }

        $tieneInscripcionEnUltimosTres = $semestresInscritos
            ->intersect($ultimosTres)
            ->isNotEmpty();

        if ($tieneInscripcionEnUltimosTres) {
            return 'gracia';
        }

        return 'no_vigente';
    }

    protected function mensajeEstadoEstudiante(IdentityLink $identity): string
    {
        $estado = $this->resolveEstadoEstudiante($identity);

        return match ($estado) {
            'vigente' => 'El estudiante tiene inscripción registrada en el semestre actual.',
            'gracia' => 'El estudiante no tiene inscripción en el semestre actual, pero permanece dentro del periodo institucional de tres semestres.',
            'no_vigente' => 'El estudiante no tiene inscripción dentro de los últimos tres semestres.',
            'no_encontrado' => 'No se encontró el estudiante asociado en SIIAP.',
            default => 'Estado del estudiante pendiente de revisión.',
        };
    }

    public function resolveTipoForIdentity(IdentityLink $identity): string
    {
        return $this->resolveDirectorioTipo($identity);
    }

    protected function applyDirectoryOrder(Builder $query): Builder
    {
        return $query
            ->leftJoin('perfiles_publicos as pp_order', 'pp_order.identity_link_id', '=', 'identity_links.id')
            ->select('identity_links.*')
            ->orderByRaw("
            CASE pp_order.directorio_tipo
                WHEN 'investigador' THEN 1
                WHEN 'tecnico_academico' THEN 2
                WHEN 'posdoctorado' THEN 3
                WHEN 'administrativo' THEN 4
                WHEN 'personal_confianza' THEN 5
                WHEN 'servicio_social' THEN 6
                WHEN 'estudiante_doctorado' THEN 7
                WHEN 'estudiante_maestria' THEN 8
                WHEN 'estudiante' THEN 9
                WHEN 'personal_irya' THEN 10
                ELSE 99
            END
        ")
            ->orderByRaw('COALESCE(pp_order.sort_order, 999999)')
            ->orderBy('pp_order.apellido_publico')
            ->orderBy('pp_order.nombre_publico')
            ->orderBy('identity_links.email');
    }
}

<?php

namespace App\Livewire\Personas;

use App\Models\CatalogoItem;
use App\Models\IdentityLink;
use App\Models\PerfilPublico;
use App\Models\Persona;
use App\Models\PersonaPerfilAcademico;
use App\Models\PersonaPosdocBeca;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    use AuthorizesRequests;

    public Persona $persona;

    public string $section = 'ingresos';

    /*
    |--------------------------------------------------------------------------
    | Datos generales
    |--------------------------------------------------------------------------
    */

    public string $nombre = '';
    public ?string $apellidop = null;
    public ?string $apellidom = null;
    public ?string $email = null;
    public ?string $curp = null;
    public ?string $rfc = null;
    public ?string $fecha_nacimiento = null;
    public ?int $sexo_id = null;
    public ?int $nacionalidad_id = null;
    public bool $activo = true;
    public ?string $observaciones = null;

    /*
    |--------------------------------------------------------------------------
    | Ingresos institucionales
    |--------------------------------------------------------------------------
    */

    public bool $ingresoFormVisible = false;
    public ?int $ingresoId = null;

    public ?int $tipo_personal_id = null;
    public ?string $numero_trabajador = null;
    public ?string $cuv = null;
    public ?int $contrato_id = null;
    public ?int $nombramiento_id = null;
    public ?int $escolaridad_id = null;
    public ?string $fecha_ingreso = null;
    public ?string $fecha_nombramiento = null;
    public ?string $fecha_definitividad = null;
    public ?string $fecha_baja = null;
    public bool $ingreso_principal = false;
    public bool $ingreso_activo = true;
    public ?string $ingreso_observaciones = null;

    public bool $deleteIngresoModal = false;
    public ?int $deleteIngresoId = null;

    public bool $showAllIngresos = false;

    /*
|--------------------------------------------------------------------------
| Perfil académico
|--------------------------------------------------------------------------
*/

    public ?string $orcid = null;
    public ?int $sni_id = null;
    public ?string $sni_vigencia = null;
    public ?int $pride_id = null;
    public ?string $pride_vigencia = null;
    public ?string $ads_author_query = null;
    public ?string $ads_profile_url = null;
    public ?string $ads_library_url = null;
    public ?string $scopus_id = null;
    public ?string $research_area = null;
    public ?string $academic_keywords = null;
    public ?string $perfil_academico_observaciones = null;

    /*
|--------------------------------------------------------------------------
| Perfil público
|--------------------------------------------------------------------------
*/

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
    public bool $perfil_publico_active = true;
    public bool $perfil_publico_visible = false;
    public ?int $sort_order = null;
    public ?string $perfil_publico_observaciones = null;

    /*
|--------------------------------------------------------------------------
| Becas posdoctorales
|--------------------------------------------------------------------------
*/

    public bool $posdocBecaFormVisible = false;
    public ?int $posdocBecaId = null;

    public ?int $beca_id = null;
    public ?string $beca_fecha_inicio = null;
    public ?string $beca_fecha_fin = null;
    public ?int $asesor_id = null;
    public bool $beca_principal = false;
    public bool $beca_activo = true;
    public ?string $beca_observaciones = null;

    public bool $deletePosdocBecaModal = false;
    public ?int $deletePosdocBecaId = null;

    public function mount(Persona $persona): void
    {
        $this->authorize('personas.update');

        /*
        * Se carga identityLink porque el perfil público se administra desde
        * la identidad institucional, no como una relación directa de Persona.
        */
        $this->persona = $persona->load([
            'identityLink.perfilPublico',
        ]);

        //$this->section = request('section', 'general');

        $ingreso_principal = $this->persona->ingresos()->where('activo', true)
            ->where('principal', true)->first();

        if ($ingreso_principal) {
            $this->editIngreso($ingreso_principal->id);
        }

        $this->fillForm();
    }

    public function setSection(string $section): void
    {
        if (! in_array($section, $this->availableSections(), true)) {
            return;
        }

        $this->section = $section;
    }

    /*
    |--------------------------------------------------------------------------
    | Datos generales
    |--------------------------------------------------------------------------
    */

    public function saveGeneral(): void
    {
        $this->authorize('personas.update');

        $data = $this->validate($this->rules(), [], $this->attributes());

        $this->persona->update($data);

        $this->persona->refresh();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Datos generales actualizados correctamente.'
        );
    }

    public function fillForm(): void
    {
        $this->nombre = $this->persona->nombre ?? '';
        $this->apellidop = $this->persona->apellidop;
        $this->apellidom = $this->persona->apellidom;
        $this->email = $this->persona->email;
        $this->curp = $this->persona->curp;
        $this->rfc = $this->persona->rfc;
        $this->fecha_nacimiento = optional($this->persona->fecha_nacimiento)->format('Y-m-d');
        $this->sexo_id = $this->persona->sexo_id;
        $this->nacionalidad_id = $this->persona->nacionalidad_id;
        $this->activo = (bool) $this->persona->activo;
        $this->observaciones = $this->persona->observaciones;

        $this->fillPerfilAcademicoForm();

        $this->fillPerfilPublicoForm();

        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:150'],
            'apellidop' => ['nullable', 'string', 'max:150'],
            'apellidom' => ['nullable', 'string', 'max:150'],

            'email' => [
                'nullable',
                'email',
                'max:190',
                Rule::unique('personas', 'email')->ignore($this->persona->id),
            ],

            'curp' => [
                'nullable',
                'string',
                'max:18',
                Rule::unique('personas', 'curp')->ignore($this->persona->id),
            ],

            'rfc' => [
                'nullable',
                'string',
                'max:13',
                Rule::unique('personas', 'rfc')->ignore($this->persona->id),
            ],

            'fecha_nacimiento' => ['nullable', 'date'],

            'sexo_id' => [
                'nullable',
                'integer',
                Rule::exists((new CatalogoItem())->getTable(), 'id'),
            ],

            'nacionalidad_id' => [
                'nullable',
                'integer',
                Rule::exists((new CatalogoItem())->getTable(), 'id'),
            ],

            'activo' => ['boolean'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'apellidop' => 'apellido paterno',
            'apellidom' => 'apellido materno',
            'email' => 'correo electrónico',
            'curp' => 'CURP',
            'rfc' => 'RFC',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'sexo_id' => 'sexo',
            'nacionalidad_id' => 'nacionalidad',
            'activo' => 'estado',
            'observaciones' => 'observaciones',
        ];
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'email.unique' => 'Ya existe una persona registrada con este correo electrónico.',
            'curp.unique' => 'Ya existe una persona registrada con esta CURP.',
            'rfc.unique' => 'Ya existe una persona registrada con este RFC.',
            'sexo_id.exists' => 'El sexo seleccionado no es válido.',
            'nacionalidad_id.exists' => 'La nacionalidad seleccionada no es válida.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Ingresos institucionales
    |--------------------------------------------------------------------------
    */

    public function createIngreso(): void
    {
        $this->authorize('personas.manage_ingresos');

        $this->resetIngresoForm();

        $this->ingreso_principal = ! $this->persona->ingresos()->exists();
        $this->ingreso_activo = true;
        $this->ingresoFormVisible = true;
        $this->section = 'ingresos';
    }

    public function editIngreso(int $id): void
    {
        $this->authorize('personas.manage_ingresos');

        $ingreso = $this->persona->ingresos()->findOrFail($id);

        $this->resetIngresoValidation();

        $this->ingresoId = $ingreso->id;
        $this->tipo_personal_id = $ingreso->tipo_personal_id;
        $this->numero_trabajador = $ingreso->numero_trabajador;
        $this->cuv = $ingreso->cuv;
        $this->contrato_id = $ingreso->contrato_id;
        $this->nombramiento_id = $ingreso->nombramiento_id;
        $this->escolaridad_id = $ingreso->escolaridad_id;
        $this->fecha_ingreso = optional($ingreso->fecha_ingreso)->format('Y-m-d');
        $this->fecha_nombramiento = optional($ingreso->fecha_nombramiento)->format('Y-m-d');
        $this->fecha_definitividad = optional($ingreso->fecha_definitividad)->format('Y-m-d');
        $this->fecha_baja = optional($ingreso->fecha_baja)->format('Y-m-d');
        $this->ingreso_principal = (bool) $ingreso->principal;
        $this->ingreso_activo = (bool) $ingreso->activo;
        $this->ingreso_observaciones = $ingreso->observaciones;

        $this->ingresoFormVisible = true;
        $this->section = 'ingresos';
    }

    public function saveIngreso(): void
    {
        $this->authorize('personas.manage_ingresos');

        $this->validate(
            $this->ingresoRules(),
            [],
            $this->ingresoAttributes()
        );

        $data = [
            'tipo_personal_id' => $this->tipo_personal_id,
            'numero_trabajador' => $this->numero_trabajador,
            'cuv' => $this->cuv,
            'contrato_id' => $this->contrato_id,
            'nombramiento_id' => $this->nombramiento_id,
            'escolaridad_id' => $this->escolaridad_id,
            'fecha_ingreso' => $this->fecha_ingreso,
            'fecha_nombramiento' => $this->fecha_nombramiento,
            'fecha_definitividad' => $this->fecha_definitividad,
            'fecha_baja' => $this->fecha_baja,
            'principal' => $this->ingreso_principal,
            'activo' => $this->ingreso_activo,
            'observaciones' => $this->ingreso_observaciones,
        ];

        if ($this->ingreso_principal) {
            $this->persona->ingresos()
                ->when($this->ingresoId, fn($query) => $query->where('id', '!=', $this->ingresoId))
                ->update(['principal' => false]);
        }

        $this->persona->ingresos()->updateOrCreate(
            ['id' => $this->ingresoId],
            $data
        );

        $this->persona->refresh();

        $this->resetIngresoForm();

        $this->mount($this->persona);

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Ingreso institucional guardado correctamente.'
        );
    }

    public function setIngresoPrincipal(int $id): void
    {
        $this->authorize('personas.manage_ingresos');

        $ingreso = $this->persona->ingresos()->findOrFail($id);

        $this->persona->ingresos()->update([
            'principal' => false,
        ]);

        $ingreso->update([
            'principal' => true,
            'activo' => true,
        ]);

        $this->persona->refresh();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Ingreso principal actualizado correctamente.'
        );
    }

    public function toggleIngresoActive(int $id): void
    {
        $this->authorize('personas.manage_ingresos');

        $ingreso = $this->persona->ingresos()->findOrFail($id);

        if ($ingreso->principal && $ingreso->activo) {
            $this->dispatch(
                'toast',
                type: 'warning',
                message: 'No se puede inactivar el ingreso principal. Primero selecciona otro ingreso principal.'
            );

            return;
        }

        $ingreso->update([
            'activo' => ! (bool) $ingreso->activo,
        ]);

        $this->persona->refresh();

        $this->dispatch(
            'toast',
            type: 'success',
            message: $ingreso->activo
                ? 'Ingreso institucional activado correctamente.'
                : 'Ingreso institucional inactivado correctamente.'
        );
    }

    public function confirmDeleteIngreso(int $id): void
    {
        $this->authorize('personas.manage_ingresos');

        $this->deleteIngresoId = $id;
        $this->deleteIngresoModal = true;
    }

    public function deleteIngresoConfirmed(): void
    {
        $this->authorize('personas.manage_ingresos');

        if (! $this->deleteIngresoId) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'No se encontró el ingreso institucional a eliminar.'
            );

            return;
        }

        $ingreso = $this->persona->ingresos()->findOrFail($this->deleteIngresoId);

        if ($ingreso->principal) {
            $this->dispatch(
                'toast',
                type: 'warning',
                message: 'No se puede eliminar el ingreso principal. Primero selecciona otro ingreso principal.'
            );

            $this->resetDeleteIngresoForm();

            return;
        }

        $ingreso->delete();

        $this->persona->refresh();

        $this->resetDeleteIngresoForm();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Ingreso institucional eliminado correctamente.'
        );
    }

    public function resetIngresoForm(): void
    {
        $this->reset([
            'ingresoFormVisible',
            'ingresoId',
            'tipo_personal_id',
            'numero_trabajador',
            'cuv',
            'contrato_id',
            'nombramiento_id',
            'escolaridad_id',
            'fecha_ingreso',
            'fecha_nombramiento',
            'fecha_definitividad',
            'fecha_baja',
            'ingreso_principal',
            'ingreso_activo',
            'ingreso_observaciones',
        ]);

        $this->ingreso_activo = true;

        $this->resetIngresoValidation();
    }

    public function resetDeleteIngresoForm(): void
    {
        $this->deleteIngresoId = null;
        $this->deleteIngresoModal = false;
    }

    protected function resetIngresoValidation(): void
    {
        $this->resetValidation([
            'tipo_personal_id',
            'numero_trabajador',
            'cuv',
            'contrato_id',
            'nombramiento_id',
            'escolaridad_id',
            'fecha_ingreso',
            'fecha_nombramiento',
            'fecha_definitividad',
            'fecha_baja',
            'ingreso_principal',
            'ingreso_activo',
            'ingreso_observaciones',
        ]);
    }

    protected function ingresoRules(): array
    {
        return [
            'tipo_personal_id' => [
                'required',
                'integer',
                Rule::exists((new CatalogoItem())->getTable(), 'id'),
            ],

            'numero_trabajador' => ['nullable', 'string', 'max:50'],
            'cuv' => ['nullable', 'string', 'max:50'],

            'contrato_id' => [
                'nullable',
                'integer',
                Rule::exists((new CatalogoItem())->getTable(), 'id'),
            ],

            'nombramiento_id' => [
                'nullable',
                'integer',
                Rule::exists((new CatalogoItem())->getTable(), 'id'),
            ],

            'escolaridad_id' => [
                'nullable',
                'integer',
                Rule::exists((new CatalogoItem())->getTable(), 'id'),
            ],

            'fecha_ingreso' => ['nullable', 'date'],
            'fecha_nombramiento' => ['nullable', 'date'],
            'fecha_definitividad' => ['nullable', 'date'],
            'fecha_baja' => ['nullable', 'date', 'after_or_equal:fecha_ingreso'],

            'ingreso_principal' => ['boolean'],
            'ingreso_activo' => ['boolean'],
            'ingreso_observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function ingresoAttributes(): array
    {
        return [
            'tipo_personal_id' => 'tipo de personal',
            'numero_trabajador' => 'número de trabajador',
            'cuv' => 'CUV',
            'contrato_id' => 'tipo de contrato',
            'nombramiento_id' => 'nombramiento',
            'escolaridad_id' => 'escolaridad',
            'fecha_ingreso' => 'fecha de ingreso',
            'fecha_nombramiento' => 'fecha de nombramiento',
            'fecha_definitividad' => 'fecha de definitividad',
            'fecha_baja' => 'fecha de baja',
            'ingreso_principal' => 'ingreso principal',
            'ingreso_activo' => 'estado del ingreso',
            'ingreso_observaciones' => 'observaciones del ingreso',
        ];
    }

    /*
|--------------------------------------------------------------------------
| Perfil académico
|--------------------------------------------------------------------------
*/

    protected function resolvedIdentityLink(): IdentityLink
    {
        $identityLink = $this->persona->identityLink;

        if (! $identityLink) {
            $identityLink = IdentityLink::create([
                'id' => IdentityLink::makeIdentityId(
                    IdentityLink::TYPE_SIIAA,
                    $this->persona->id
                ),
                'identity_type' => IdentityLink::TYPE_SIIAA,
                'identity_id' => $this->persona->id,
                'email' => $this->persona->email,
                'is_primary' => true,
                'active' => true,
                'matched_by' => IdentityLink::MATCHED_BY_MANUAL,
                'matched_at' => now(),
                'verified_at' => now(),
            ]);

            $this->persona->load('identityLink');
        }

        return $identityLink;
    }

    /**
     * Carga en el formulario la información académica existente.
     *
     * El perfil académico es un registro único por persona. Si todavía no existe,
     * los campos permanecen vacíos hasta el primer guardado.
     */
    public function fillPerfilAcademicoForm(): void
    {
        #$perfil = $this->persona->perfilAcademico;
        $perfil = $this->persona->identityLink?->perfilAcademico;

        $this->orcid = $perfil?->orcid;
        $this->sni_id = $perfil?->sni_id;
        $this->sni_vigencia = optional($perfil?->sni_vigencia)->format('Y-m-d');
        $this->pride_id = $perfil?->pride_id;
        $this->pride_vigencia = optional($perfil?->pride_vigencia)->format('Y-m-d');
        $this->ads_author_query = $perfil?->ads_author_query;
        $this->ads_profile_url = $perfil?->ads_profile_url;
        $this->ads_library_url = $perfil?->ads_library_url;
        $this->scopus_id = $perfil?->scopus_id;
        $this->research_area = $perfil?->research_area;
        $this->academic_keywords = $perfil?->academic_keywords;
        $this->perfil_academico_observaciones = $perfil?->observaciones;
    }

    /**
     * Crea o actualiza el perfil académico de la persona.
     */
    public function savePerfilAcademico(): void
    {
        $this->authorize('personas.update');

        $data = $this->validate(
            $this->perfilAcademicoRules(),
            [],
            $this->perfilAcademicoAttributes()
        );

        $identityLink = $this->resolvedIdentityLink();

        PersonaPerfilAcademico::updateOrCreate(
            ['identity_link_id' => $identityLink->id],
            [
                'orcid' => $data['orcid'],
                'sni_id' => $data['sni_id'],
                'sni_vigencia' => $data['sni_vigencia'],
                'pride_id' => $data['pride_id'],
                'pride_vigencia' => $data['pride_vigencia'],
                'ads_author_query' => $data['ads_author_query'],
                'ads_profile_url' => $data['ads_profile_url'],
                'ads_library_url' => $data['ads_library_url'],
                'scopus_id' => $data['scopus_id'],
                'research_area' => $data['research_area'],
                'academic_keywords' => $data['academic_keywords'],
                'observaciones' => $data['perfil_academico_observaciones'],
            ]
        );

        $this->persona->refresh();
        $this->persona->load('identityLink.perfilAcademico');

        $this->fillPerfilAcademicoForm();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Perfil académico actualizado correctamente.'
        );
    }

    /**
     * Reglas de validación del perfil académico.
     */
    protected function perfilAcademicoRules(): array
    {
        return [
            'orcid' => ['nullable', 'string', 'max:50'],
            'sni_id' => [
                'nullable',
                'integer',
                Rule::exists((new CatalogoItem())->getTable(), 'id'),
            ],
            'sni_vigencia' => ['nullable', 'date'],
            'pride_id' => [
                'nullable',
                'integer',
                Rule::exists((new CatalogoItem())->getTable(), 'id'),
            ],
            'pride_vigencia' => ['nullable', 'date'],
            'ads_author_query' => ['nullable', 'string', 'max:500'],
            'ads_profile_url' => ['nullable', 'url', 'max:500'],
            'ads_library_url' => ['nullable', 'url', 'max:500'],
            'scopus_id' => ['nullable', 'string', 'max:100'],
            'research_area' => ['nullable', 'string', 'max:1000'],
            'academic_keywords' => ['nullable', 'string', 'max:1000'],
            'perfil_academico_observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Nombres legibles para errores de validación.
     */
    protected function perfilAcademicoAttributes(): array
    {
        return [
            'orcid' => 'ORCID',
            'sni_id' => 'SNI',
            'sni_vigencia' => 'vigencia SNI',
            'pride_id' => 'PRIDE',
            'pride_vigencia' => 'vigencia PRIDE',
            'ads_author_query' => 'consulta ADS',
            'ads_profile_url' => 'URL de perfil ADS',
            'ads_library_url' => 'URL de biblioteca ADS',
            'scopus_id' => 'Scopus ID',
            'research_area' => 'área de investigación',
            'academic_keywords' => 'palabras clave académicas',
            'perfil_academico_observaciones' => 'observaciones del perfil académico',
        ];
    }

    /*
|--------------------------------------------------------------------------
| Perfil público
|--------------------------------------------------------------------------
*/

    /**
     * Carga la información pública asociada a la identidad institucional.
     *
     * El perfil público no depende directamente de Persona, sino de identityLink.
     * Esto permite reutilizar la misma estructura para personal SIIAA y estudiantes SIIAP.
     */
    public function fillPerfilPublicoForm(): void
    {
        $perfil = $this->persona->identityLink?->perfilPublico;

        $this->titulo_es = $perfil?->titulo_es;
        $this->titulo_en = $perfil?->titulo_en;
        $this->nombre_publico = $perfil?->nombre_publico;
        $this->apellido_publico = $perfil?->apellido_publico;
        $this->area_es = $perfil?->area_es;
        $this->area_en = $perfil?->area_en;
        $this->oficina = $perfil?->oficina;
        $this->extension_red_unam = $perfil?->extension_red_unam;
        $this->telefono_morelia = $perfil?->telefono_morelia;
        $this->telefono_cdmx = $perfil?->telefono_cdmx;
        $this->email_publico = $perfil?->email_publico;
        $this->homepage_url = $perfil?->homepage_url;
        $this->perfil_publico_active = (bool) ($perfil?->active ?? true);
        $this->perfil_publico_visible = (bool) ($perfil?->visible ?? false);
        $this->sort_order = $perfil?->sort_order;
        $this->perfil_publico_observaciones = $perfil?->observaciones;
    }

    /**
     * Crea o actualiza el perfil público de la identidad institucional.
     *
     * Requiere que la persona tenga un identityLink resuelto. Si no existe,
     * se bloquea el guardado para no crear perfiles públicos huérfanos.
     */
    public function savePerfilPublico(): void
    {
        $this->authorize('personas.manage_public_profile');

        $identityLink = $this->resolvedIdentityLink();

        $data = $this->validate(
            $this->perfilPublicoRules(),
            [],
            $this->perfilPublicoAttributes()
        );

        PerfilPublico::query()->updateOrCreate(
            ['identity_link_id' => $identityLink->id],
            [
                'titulo_es' => $data['titulo_es'],
                'titulo_en' => $data['titulo_en'],
                'nombre_publico' => $data['nombre_publico'],
                'apellido_publico' => $data['apellido_publico'],
                'area_es' => $data['area_es'],
                'area_en' => $data['area_en'],
                'oficina' => $data['oficina'],
                'extension_red_unam' => $data['extension_red_unam'],
                'telefono_morelia' => $data['telefono_morelia'],
                'telefono_cdmx' => $data['telefono_cdmx'],
                'email_publico' => $data['email_publico'],
                'homepage_url' => $data['homepage_url'],
                'active' => $data['perfil_publico_active'],
                'visible' => $data['perfil_publico_visible'],
                'sort_order' => $data['sort_order'],
                'observaciones' => $data['perfil_publico_observaciones'],
            ]
        );

        $this->persona->refresh();
        $this->persona->load('identityLink.perfilPublico');

        $this->fillPerfilPublicoForm();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Perfil público actualizado correctamente.'
        );
    }

    /**
     * Reglas de validación para el perfil visible en directorio institucional.
     */
    protected function perfilPublicoRules(): array
    {
        return [
            'titulo_es' => ['nullable', 'string', 'max:190'],
            'titulo_en' => ['nullable', 'string', 'max:190'],
            'nombre_publico' => ['nullable', 'string', 'max:190'],
            'apellido_publico' => ['nullable', 'string', 'max:190'],
            'area_es' => ['nullable', 'string', 'max:500'],
            'area_en' => ['nullable', 'string', 'max:500'],
            'oficina' => ['nullable', 'string', 'max:100'],
            'extension_red_unam' => ['nullable', 'string', 'max:50'],
            'telefono_morelia' => ['nullable', 'string', 'max:50'],
            'telefono_cdmx' => ['nullable', 'string', 'max:50'],
            'email_publico' => ['nullable', 'email', 'max:190'],
            'homepage_url' => ['nullable', 'url', 'max:500'],
            'perfil_publico_active' => ['boolean'],
            'perfil_publico_visible' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'perfil_publico_observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Etiquetas legibles para mensajes de validación.
     */
    protected function perfilPublicoAttributes(): array
    {
        return [
            'titulo_es' => 'título en español',
            'titulo_en' => 'título en inglés',
            'nombre_publico' => 'nombre público',
            'apellido_publico' => 'apellido público',
            'area_es' => 'área en español',
            'area_en' => 'área en inglés',
            'oficina' => 'oficina',
            'extension_red_unam' => 'extensión RedUNAM',
            'telefono_morelia' => 'teléfono Morelia',
            'telefono_cdmx' => 'teléfono CDMX',
            'email_publico' => 'correo público',
            'homepage_url' => 'sitio web personal',
            'perfil_publico_active' => 'perfil activo',
            'perfil_publico_visible' => 'perfil visible',
            'sort_order' => 'orden de aparición',
            'perfil_publico_observaciones' => 'observaciones del perfil público',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Utilidades
    |--------------------------------------------------------------------------
    */

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

    protected function catalogOptions(string $catalogoClave): array
    {
        return CatalogoItem::query()
            ->whereHas('catalogo', function ($query) use ($catalogoClave) {
                $query->where('clave', $catalogoClave);
            })
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->pluck('nombre', 'id')
            ->toArray();
    }

    /*
|--------------------------------------------------------------------------
| Becas posdoctorales
|--------------------------------------------------------------------------
*/

    /**
     * Abre el formulario para registrar una nueva beca posdoctoral.
     *
     * Esta sección solo debe operar cuando la persona tiene ingreso principal
     * de tipo posdoctoral, validado mediante Persona::esPosdoctorado().
     */
    public function createPosdocBeca(): void
    {
        $this->authorize('personas.manage_posdoc_becas');

        if (! $this->persona->esPosdoctorado()) {
            $this->dispatch(
                'toast',
                type: 'warning',
                message: 'Solo se pueden registrar becas en personas con tipo de personal posdoctoral.'
            );

            return;
        }

        $this->resetPosdocBecaForm();

        $this->beca_principal = ! $this->persona->posdocBecas()->exists();
        $this->beca_activo = true;
        $this->posdocBecaFormVisible = true;
        $this->section = 'posdoc_becas';
    }

    /**
     * Carga una beca posdoctoral existente para edición.
     */
    public function editPosdocBeca(int $id): void
    {
        $this->authorize('personas.manage_posdoc_becas');

        $beca = $this->persona->posdocBecas()->findOrFail($id);

        $this->resetPosdocBecaValidation();

        $this->posdocBecaId = $beca->id;
        $this->beca_id = $beca->beca_id;
        $this->beca_fecha_inicio = optional($beca->fecha_inicio)->format('Y-m-d');
        $this->beca_fecha_fin = optional($beca->fecha_fin)->format('Y-m-d');
        $this->asesor_id = $beca->asesor_id;
        $this->beca_principal = (bool) $beca->principal;
        $this->beca_activo = (bool) $beca->activo;
        $this->beca_observaciones = $beca->observaciones;

        $this->posdocBecaFormVisible = true;
        $this->section = 'posdoc_becas';
    }

    /**
     * Guarda o actualiza una beca posdoctoral.
     *
     * Regla de negocio:
     * - Una persona posdoctoral puede tener historial de becas.
     * - Solo una beca debe quedar marcada como principal.
     * - La beca principal se conserva activa.
     */
    public function savePosdocBeca(): void
    {
        $this->authorize('personas.manage_posdoc_becas');

        if (! $this->persona->esPosdoctorado()) {
            $this->dispatch(
                'toast',
                type: 'warning',
                message: 'No se puede guardar una beca porque la persona no está marcada como posdoctoral.'
            );

            return;
        }

        $this->validate(
            $this->posdocBecaRules(),
            [],
            $this->posdocBecaAttributes()
        );

        if ($this->beca_principal) {
            $this->persona->posdocBecas()
                ->when($this->posdocBecaId, fn($query) => $query->where('id', '!=', $this->posdocBecaId))
                ->update(['principal' => false]);

            $this->beca_activo = true;
        }

        $this->persona->posdocBecas()->updateOrCreate(
            ['id' => $this->posdocBecaId],
            [
                'beca_id' => $this->beca_id,
                'fecha_inicio' => $this->beca_fecha_inicio,
                'fecha_fin' => $this->beca_fecha_fin,
                'asesor_id' => $this->asesor_id,
                'principal' => $this->beca_principal,
                'activo' => $this->beca_activo,
                'observaciones' => $this->beca_observaciones,
            ]
        );

        $this->persona->refresh();

        $this->resetPosdocBecaForm();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Beca posdoctoral guardada correctamente.'
        );
    }

    /**
     * Marca una beca posdoctoral como principal.
     */
    public function setPosdocBecaPrincipal(int $id): void
    {
        $this->authorize('personas.manage_posdoc_becas');

        $beca = $this->persona->posdocBecas()->findOrFail($id);

        $this->persona->posdocBecas()->update(['principal' => false]);

        $beca->update([
            'principal' => true,
            'activo' => true,
        ]);

        $this->persona->refresh();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Beca posdoctoral principal actualizada correctamente.'
        );
    }

    /**
     * Activa o inactiva una beca posdoctoral.
     *
     * La beca principal no se inactiva directamente para evitar dejar el
     * historial sin una beca vigente de referencia.
     */
    public function togglePosdocBecaActive(int $id): void
    {
        $this->authorize('personas.manage_posdoc_becas');

        $beca = $this->persona->posdocBecas()->findOrFail($id);

        if ($beca->principal && $beca->activo) {
            $this->dispatch(
                'toast',
                type: 'warning',
                message: 'No se puede inactivar la beca principal. Primero selecciona otra beca principal.'
            );

            return;
        }

        $beca->update([
            'activo' => ! (bool) $beca->activo,
        ]);

        $this->persona->refresh();

        $this->dispatch(
            'toast',
            type: 'success',
            message: $beca->activo
                ? 'Beca posdoctoral activada correctamente.'
                : 'Beca posdoctoral inactivada correctamente.'
        );
    }

    /**
     * Abre el modal de confirmación para eliminar una beca posdoctoral.
     */
    public function confirmDeletePosdocBeca(int $id): void
    {
        $this->authorize('personas.manage_posdoc_becas');

        $this->deletePosdocBecaId = $id;
        $this->deletePosdocBecaModal = true;
    }

    /**
     * Elimina una beca posdoctoral no principal.
     */
    public function deletePosdocBecaConfirmed(): void
    {
        $this->authorize('personas.manage_posdoc_becas');

        if (! $this->deletePosdocBecaId) {
            $this->dispatch(
                'toast',
                type: 'error',
                message: 'No se encontró la beca posdoctoral a eliminar.'
            );

            return;
        }

        $beca = $this->persona->posdocBecas()->findOrFail($this->deletePosdocBecaId);

        if ($beca->principal) {
            $this->dispatch(
                'toast',
                type: 'warning',
                message: 'No se puede eliminar la beca principal. Primero selecciona otra beca principal.'
            );

            $this->resetDeletePosdocBecaForm();

            return;
        }

        $beca->delete();

        $this->persona->refresh();

        $this->resetDeletePosdocBecaForm();

        $this->dispatch(
            'toast',
            type: 'success',
            message: 'Beca posdoctoral eliminada correctamente.'
        );
    }

    /**
     * Limpia el formulario de becas posdoctorales.
     */
    public function resetPosdocBecaForm(): void
    {
        $this->reset([
            'posdocBecaFormVisible',
            'posdocBecaId',
            'beca_id',
            'beca_fecha_inicio',
            'beca_fecha_fin',
            'asesor_id',
            'beca_principal',
            'beca_activo',
            'beca_observaciones',
        ]);

        $this->beca_activo = true;

        $this->resetPosdocBecaValidation();
    }

    /**
     * Limpia el estado del modal de eliminación de becas.
     */
    public function resetDeletePosdocBecaForm(): void
    {
        $this->deletePosdocBecaId = null;
        $this->deletePosdocBecaModal = false;
    }

    /**
     * Limpia únicamente los errores de validación de becas posdoctorales.
     */
    protected function resetPosdocBecaValidation(): void
    {
        $this->resetValidation([
            'beca_id',
            'beca_fecha_inicio',
            'beca_fecha_fin',
            'asesor_id',
            'beca_principal',
            'beca_activo',
            'beca_observaciones',
        ]);
    }

    /**
     * Reglas de validación para becas posdoctorales.
     */
    protected function posdocBecaRules(): array
    {
        return [
            'beca_id' => [
                'required',
                'integer',
                Rule::exists((new CatalogoItem())->getTable(), 'id'),
            ],
            'beca_fecha_inicio' => ['nullable', 'date'],
            'beca_fecha_fin' => ['nullable', 'date', 'after_or_equal:beca_fecha_inicio'],
            'asesor_id' => [
                'nullable',
                'integer',
                Rule::exists((new Persona())->getTable(), 'id'),
            ],
            'beca_principal' => ['boolean'],
            'beca_activo' => ['boolean'],
            'beca_observaciones' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Etiquetas legibles para mensajes de validación.
     */
    protected function posdocBecaAttributes(): array
    {
        return [
            'beca_id' => 'beca posdoctoral',
            'beca_fecha_inicio' => 'fecha de inicio',
            'beca_fecha_fin' => 'fecha de fin',
            'asesor_id' => 'asesor',
            'beca_principal' => 'beca principal',
            'beca_activo' => 'estado de la beca',
            'beca_observaciones' => 'observaciones de la beca',
        ];
    }

    public function render()
    {
        return view('livewire.personas.edit', [
            /*
         * Catálogos para selects.
         *
         * Las claves reales se conservan como están registradas en BD.
         * El prefijo c_ solo identifica variables que contienen opciones
         * provenientes del módulo de catálogos.
         */
            'c_sexos' => $this->catalogOptions('SEXOS'),
            'c_nacionalidades' => $this->catalogOptions('PAISES'),
            'c_tiposPersonal' => $this->catalogOptions('TIPOS_PERSONAL'),
            'c_contratos' => $this->catalogOptions('T_CONTRATOS'),
            'c_nombramientos' => $this->catalogOptions('C_NOMBRAMIENTOS'),
            'c_escolaridades' => $this->catalogOptions('C_ESCOLARIDADES'),
            'c_posdocBecas' => $this->catalogOptions('POS_BECAS'),
            'c_sni' => $this->catalogOptions('C_SNI'),
            'c_pride' => $this->catalogOptions('C_PRIDE'),

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

            /*
            * Asesores disponibles para becas posdoctorales.
            *
            * Solo se consideran personas IRyA activas cuyo ingreso principal corresponde
            * al tipo de personal investigador. Esto evita asignar como asesor a personal
            * administrativo, técnico, posdoctoral u otros perfiles no aplicables.
            */
            'asesores' => Persona::query()
                ->where('activo', true)
                ->whereHas('ingresoPrincipal.tipoPersonal', function ($query) {
                    $query->where('clave', 'INV');
                })
                ->orderBy('apellidop')
                ->orderBy('apellidom')
                ->orderBy('nombre')
                ->get()
                ->mapWithKeys(fn(Persona $persona) => [
                    $persona->id => $persona->fullname,
                ])
                ->toArray(),

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
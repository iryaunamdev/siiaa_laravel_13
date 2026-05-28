<?php

namespace App\Models;

use App\Models\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerfilPublico extends Model
{
    use SoftDeletes;
    use TracksUserActions;

    protected $table = 'perfiles_publicos';

    protected $fillable = [
        'identity_link_id',
        'photo_path',
        'titulo_es',
        'titulo_en',
        'nombre_publico',
        'apellido_publico',
        'area_es',
        'area_en',
        'oficina',
        'extension_red_unam',
        'telefono_morelia',
        'telefono_cdmx',
        'email_publico',
        'homepage_url',
        'active',
        'visible',
        'sort_order',
        'observaciones',
    ];

    protected $casts = [
        'identity_link_id' => 'integer',
        'active' => 'boolean',
        'visible' => 'boolean',
        'sort_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function identityLink(): BelongsTo
    {
        return $this->belongsTo(IdentityLink::class, 'identity_link_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Datos resueltos
    |--------------------------------------------------------------------------
    */

    public function profileData(): ?array
    {
        return $this->identityLink
            ? profileData($this->identityLink->identity_type, $this->identityLink->id)
            : null;
    }

    public function resolvedName(): ?string
    {
        $publicName = trim(
            ($this->nombre_publico ?? '') . ' ' .
                ($this->apellido_publico ?? '')
        );

        if ($publicName !== '') {
            return $publicName;
        }

        return $this->identityLink?->fullname();
    }

    public function resolvedEmail(): ?string
    {
        return $this->email_publico
            ?: $this->identityLink?->emailResolved();
    }

    public function resolvedPhotoUrl(): ?string
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }

        return $this->identityLink?->photoUrl();
    }

    public function resolvedInitials(): string
    {
        return $this->identityLink?->initials() ?? 'NA';
    }

    public function resolvedTitulo(string $locale = 'es'): ?string
    {
        return $locale === 'en'
            ? ($this->titulo_en ?: $this->titulo_es)
            : ($this->titulo_es ?: $this->titulo_en);
    }

    public function resolvedArea(string $locale = 'es'): ?string
    {
        return $locale === 'en'
            ? ($this->area_en ?: $this->area_es)
            : ($this->area_es ?: $this->area_en);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visible', true);
    }

    public function scopePublicados(Builder $query): Builder
    {
        return $query
            ->active()
            ->visible();
    }

    public function scopeOrdenados(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function scopeDeTipoIdentidad(Builder $query, string $identityType): Builder
    {
        return $query->whereHas('identityLink', function (Builder $query) use ($identityType) {
            $query->where('identity_type', $identityType);
        });
    }

    public function scopeEstudiantesSiiap(Builder $query): Builder
    {
        return $this->scopeDeTipoIdentidad($query, IdentityLink::TYPE_SIIAP_STUDENT);
    }

    public function scopePersonasSiiaa(Builder $query): Builder
    {
        return $this->scopeDeTipoIdentidad($query, IdentityLink::TYPE_SIIAA);
    }
}
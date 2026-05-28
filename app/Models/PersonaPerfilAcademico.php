<?php

namespace App\Models;

use App\Models\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonaPerfilAcademico extends Model
{
    use SoftDeletes;
    use TracksUserActions;

    protected $table = 'persona_perfiles_academicos';

    protected $fillable = [
        'persona_id',
        'orcid',
        'sni_id',
        'sni_vigencia',
        'pride_id',
        'pride_vigencia',
        'ads_author_query',
        'ads_profile_url',
        'ads_library_url',
        'scopus_id',
        'research_area',
        'academic_keywords',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'pride_vigencia' => 'date',
        'sni_vigencia' => 'date',
    ];

    public function setOrcidAttribute($value): void
    {
        $this->attributes['orcid'] = $value
            ? trim($value)
            : null;
    }

    public function setScopusIdAttribute($value): void
    {
        $this->attributes['scopus_id'] = $value
            ? trim($value)
            : null;
    }

    public function setAdsProfileUrlAttribute($value): void
    {
        $this->attributes['ads_profile_url'] = $value
            ? trim($value)
            : null;
    }

    public function setAdsLibraryUrlAttribute($value): void
    {
        $this->attributes['ads_library_url'] = $value
            ? trim($value)
            : null;
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function sni()
    {
        return $this->belongsTo(CatalogoItem::class, 'sni_id');
    }

    public function pride()
    {
        return $this->belongsTo(CatalogoItem::class, 'pride_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeConOrcid($query)
    {
        return $query->whereNotNull('orcid')
            ->where('orcid', '!=', '');
    }

    public function scopeConAds($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('ads_author_query')
                ->orWhereNotNull('ads_profile_url')
                ->orWhereNotNull('ads_library_url');
        });
    }
}

<?php

namespace App\Models;

use App\Models\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Persona extends Model
{
    use SoftDeletes;
    use TracksUserActions;

    protected $fillable = [
        'nombre',
        'apellidop',
        'apellidom',
        'email',
        'curp',
        'rfc',
        'fecha_nacimiento',
        'sexo_id',
        'nacionalidad_id',
        'activo',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = $value
            ? mb_strtolower(trim($value))
            : null;
    }

    public function setCurpAttribute($value): void
    {
        $this->attributes['curp'] = $value
            ? mb_strtoupper(trim($value))
            : null;
    }

    public function setRfcAttribute($value): void
    {
        $this->attributes['rfc'] = $value
            ? mb_strtoupper(trim($value))
            : null;
    }

    public function getFullnameAttribute(): string
    {
        return trim(
            collect([
                $this->nombre,
                $this->apellidop,
                $this->apellidom,
            ])->filter()->implode(' ')
        );
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->fullname;
    }

    /**
     * Iniciales para avatares o identificadores visuales.
     *
     * Prioriza nombre y apellido paterno. Si alguno falta, toma las iniciales
     * disponibles sin romper la vista.
     */
    public function getInitialsAttribute(): string
    {
        $parts = collect([
            $this->nombre,
            $this->apellidop,
        ])
            ->filter()
            ->map(fn($value) => Str::of(trim((string) $value))->substr(0, 1));

        return Str::of($parts->implode(''))
            ->upper()
            ->ascii()
            ->toString();
    }

    public function ingresos()
    {
        return $this->hasMany(PersonaIngreso::class);
    }

    public function ingresoPrincipal()
    {
        return $this->hasOne(PersonaIngreso::class)
            ->where('principal', true);
    }

    public function ingresosActivos()
    {
        return $this->hasMany(PersonaIngreso::class)
            ->where('activo', true);
    }

    public function perfilAcademico()
    {
        return $this->hasOneThrough(
            PersonaPerfilAcademico::class,
            IdentityLink::class,
            'identity_id',
            'identity_link_id',
            'id',
            'id'
        )->where('identity_links.identity_type', IdentityLink::TYPE_SIIAA);
    }

    public function identityLink()
    {
        return $this->hasOne(IdentityLink::class, 'identity_id')
            ->where('identity_type', IdentityLink::TYPE_SIIAA);
    }

    public function esPosdoctorado(): bool
    {
        return $this->ingresoPrincipal?->tipoPersonal?->clave === 'POS';
    }

    public function posdocBecas()
    {
        return $this->hasMany(PersonaPosdocBeca::class);
    }

    public function posdocBecaPrincipal()
    {
        return $this->hasOne(PersonaPosdocBeca::class)
            ->where('principal', true);
    }

    public function posdocBecasActivas()
    {
        return $this->hasMany(PersonaPosdocBeca::class)
            ->where('activo', true);
    }

    public function asesoriasPosdoc()
    {
        return $this->hasMany(PersonaPosdocBeca::class, 'asesor_id');
    }

    public function sexo()
    {
        return $this->belongsTo(CatalogoItem::class, 'sexo_id');
    }

    public function nacionalidad()
    {
        return $this->belongsTo(CatalogoItem::class, 'nacionalidad_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeBuscar($query, ?string $search)
    {
        if (! filled($search)) {
            return $query;
        }

        $search = trim($search);

        return $query->where(function ($q) use ($search) {
            $q->where('nombre', 'like', "%{$search}%")
                ->orWhere('apellidop', 'like', "%{$search}%")
                ->orWhere('apellidom', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('curp', 'like', "%{$search}%")
                ->orWhere('rfc', 'like', "%{$search}%");
        });
    }
}
<?php

namespace App\Models;

use App\Models\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonaIngreso extends Model
{
    use SoftDeletes;
    use TracksUserActions;

    protected $fillable = [
        'persona_id',
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
        'activo',
        'principal',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_nombramiento' => 'date',
        'fecha_definitividad' => 'date',
        'fecha_baja' => 'date',
        'activo' => 'boolean',
        'principal' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (PersonaIngreso $ingreso) {
            if ($ingreso->principal) {
                static::where('persona_id', $ingreso->persona_id)
                    ->where('id', '!=', $ingreso->id)
                    ->where('principal', true)
                    ->update(['principal' => false]);
            }
        });
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function tipoPersonal()
    {
        return $this->belongsTo(CatalogoItem::class, 'tipo_personal_id');
    }

    public function contrato()
    {
        return $this->belongsTo(CatalogoItem::class, 'contrato_id');
    }

    public function nombramiento()
    {
        return $this->belongsTo(CatalogoItem::class, 'nombramiento_id');
    }

    /*public function categoria()
    {
        return $this->belongsTo(CatalogoItem::class, 'categoria_id');
    } --- IGNORE ---*/

    public function escolaridad()
    {
        return $this->belongsTo(CatalogoItem::class, 'escolaridad_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePrincipales($query)
    {
        return $query->where('principal', true);
    }

    public function scopeDeTipo($query, $tipoPersonalId)
    {
        if (! filled($tipoPersonalId)) {
            return $query;
        }

        return $query->where('tipo_personal_id', $tipoPersonalId);
    }
}
<?php

namespace App\Models;

use App\Models\Traits\TracksUserActions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonaPosdocBeca extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TracksUserActions;

    protected $table = 'persona_posdoc_becas';

    protected $fillable = [
        'persona_id',
        'beca_id',
        'fecha_inicio',
        'fecha_fin',
        'asesor_id',
        'principal',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'principal' => 'boolean',
        'activo' => 'boolean',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function beca()
    {
        return $this->belongsTo(CatalogoItem::class, 'beca_id');
    }

    public function asesor()
    {
        return $this->belongsTo(Persona::class, 'asesor_id');
    }
}

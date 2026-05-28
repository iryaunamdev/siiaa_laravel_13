<?php

namespace App\Models\Siiap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstudianteTutor extends Model
{
    protected $connection = 'siiap';

    protected $table = 'estudiantes_tutores';

    protected $casts = [
        'principal' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(EstudianteInscripcion::class, 'inscripcion_id');
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePrincipal($query)
    {
        return $query->where('principal', true);
    }

    public function scopeSecundarios($query)
    {
        return $query->where('principal', false);
    }
}
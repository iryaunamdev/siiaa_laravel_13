<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccessLog extends Model
{
    protected $fillable = [
        'user_id',
        'event',
        'auth_type',
        'ip_address',
        'user_agent',
        'session_id',
        'context',
    ];

    /**
     * Se castea context como arreglo para facilitar auditoría,
     * depuración y futura extensión de metadatos de acceso.
     */
    protected function casts(): array
    {
        return [
            'context' => 'array',
        ];
    }

    /**
     * Usuario asociado al evento de acceso.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
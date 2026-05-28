<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdentityAccessLog extends Model
{
    public const ACCESS_NORMAL = 'normal';
    public const ACCESS_IMPERSONATION = 'impersonation';
    public const ACCESS_MANUAL_TEST = 'manual_test';

    protected $fillable = [
        'user_id',
        'identity_link_id',
        'access_type',
        'impersonated_by',
        'reason',
        'ip_address',
        'user_agent',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public static function validAccessTypes(): array
    {
        return [
            self::ACCESS_NORMAL,
            self::ACCESS_IMPERSONATION,
            self::ACCESS_MANUAL_TEST,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function identityLink()
    {
        return $this->belongsTo(IdentityLink::class, 'identity_link_id', 'id');
    }

    public function impersonatedBy()
    {
        return $this->belongsTo(User::class, 'impersonated_by');
    }

    public function scopeNormal($query)
    {
        return $query->where('access_type', self::ACCESS_NORMAL);
    }

    public function scopeImpersonation($query)
    {
        return $query->where('access_type', self::ACCESS_IMPERSONATION);
    }

    public function scopeManualTest($query)
    {
        return $query->where('access_type', self::ACCESS_MANUAL_TEST);
    }

    public function scopeActivos($query)
    {
        return $query->whereNull('ended_at');
    }

    public function scopeFinalizados($query)
    {
        return $query->whereNotNull('ended_at');
    }
}
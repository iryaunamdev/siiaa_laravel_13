<?php

namespace App\Models;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdentityLink extends Model
{
    use SoftDeletes;

    public const TYPE_SIIAA = 'siiaa';
    public const TYPE_SIIAP_STUDENT = 'siiap_student';

    public const MATCHED_BY_EMAIL = 'email';
    public const MATCHED_BY_MANUAL = 'manual';
    public const MATCHED_BY_IMPORT = 'import';
    public const MATCHED_BY_LDAP = 'ldap';
    public const MATCHED_BY_SIIAP = 'siiap';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'identity_type',
        'identity_id',
        'email',
        'is_primary',
        'active',
        'matched_by',
        'matched_at',
        'verified_at',
        'observaciones',
    ];

    protected $casts = [
        'identity_id' => 'integer',
        'is_primary' => 'boolean',
        'active' => 'boolean',
        'matched_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public static function makeIdentityId(string $identityType, int $identityId): int
    {
        if ($identityId > 9999999) {
            throw new InvalidArgumentException(
                'El ID original excede el límite permitido para generar identidad.'
            );
        }

        return match ($identityType) {
            self::TYPE_SIIAA => 10000000 + $identityId,
            self::TYPE_SIIAP_STUDENT => 20000000 + $identityId,
            default => throw new InvalidArgumentException(
                "Tipo de identidad no válido: {$identityType}"
            ),
        };
    }

    public static function validTypes(): array
    {
        return [
            self::TYPE_SIIAA,
            self::TYPE_SIIAP_STUDENT,
        ];
    }

    public static function validMatchedBy(): array
    {
        return [
            self::MATCHED_BY_EMAIL,
            self::MATCHED_BY_MANUAL,
            self::MATCHED_BY_IMPORT,
            self::MATCHED_BY_LDAP,
            self::MATCHED_BY_SIIAP,
        ];
    }

    public function setEmailAttribute($value): void
    {
        $this->attributes['email'] = $value
            ? mb_strtolower(trim($value))
            : null;
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'identity_id')
            ->where('identity_type', self::TYPE_SIIAA);
    }

    public function perfilPublico()
    {
        return $this->hasOne(PerfilPublico::class, 'identity_link_id', 'id');
    }

    public function scopeActivas($query)
    {
        return $query->where('active', true);
    }

    public function scopeTipo($query, ?string $type)
    {
        if (! filled($type)) {
            return $query;
        }

        return $query->where('identity_type', $type);
    }

    public function scopePorEmail($query, ?string $email)
    {
        if (! filled($email)) {
            return $query;
        }

        return $query->where('email', mb_strtolower(trim($email)));
    }

    public function profile(): ?array
    {
        return profileData($this->identity_type, $this->id);
    }

    public function fullname(): ?string
    {
        return profileFullname($this->identity_type, $this->id);
    }

    public function emailResolved(): ?string
    {
        return profileEmail($this->identity_type, $this->id) ?? $this->email;
    }

    public function photoUrl(): ?string
    {
        return profilePhotoUrl($this->identity_type, $this->id);
    }

    public function initials(): string
    {
        return profileInitials($this->identity_type, $this->id);
    }
}

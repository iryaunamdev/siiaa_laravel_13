<?php

namespace App\Models;

use App\Models\UserAccessLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Services\System\SettingService;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'auth_type',
        'ldap_uid',
        'ldap_dn',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function initials(): string
    {
        $name = trim($this->name);

        if (empty($name)) {
            return '';
        }

        // Normalizar acentos (Á → A, ñ → n, etc.)
        $name = $this->normalizeString($name);

        // Caso: "Apellido, Nombre"
        if (str_contains($name, ',')) {
            [$last, $first] = array_map('trim', explode(',', $name, 2));

            $firstName = explode(' ', $first)[0] ?? '';
            $lastName = explode(' ', $last)[0] ?? '';

            return strtoupper(
                mb_substr($firstName, 0, 1) .
                    mb_substr($lastName, 0, 1)
            );
        }

        // Caso: "Nombre Apellido"
        $parts = preg_split('/\s+/', $name);

        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? '';

        return strtoupper(
            mb_substr($firstName, 0, 1) .
                mb_substr($lastName, 0, 1)
        );
    }

    protected function normalizeString(string $value): string
    {
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return $normalized ?: $value;
    }

    public function requiresTwoFactor(): bool
    {
        $settings = app(SettingService::class);

        return (bool) $settings->get('auth.2fa.enabled', false);
    }

    /**
     * Bitácora de accesos del usuario.
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(UserAccessLog::class);
    }
}

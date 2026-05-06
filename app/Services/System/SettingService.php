<?php

namespace App\Services\System;

use App\Models\Setting;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = Setting::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        if (! $setting) {
            return $default;
        }

        return $this->castValue($setting->value, $setting->type);
    }

    public function set(
        string $key,
        mixed $value,
        string $group = 'general',
        string $type = 'string',
        ?string $label = null,
        ?string $description = null,
        bool $isPublic = false,
        bool $isEncrypted = false,
        bool $isActive = true,
    ): Setting {
        return Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $this->prepareValue($value, $type),
                'group' => $group,
                'type' => $type,
                'label' => $label,
                'description' => $description,
                'is_public' => $isPublic,
                'is_encrypted' => $isEncrypted,
                'is_active' => $isActive,
            ]
        );
    }

    protected function castValue(?string $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => is_null($value) ? null : (int) $value,
            'float' => is_null($value) ? null : (float) $value,
            'json' => is_null($value) ? null : json_decode($value, true),
            default => $value,
        };
    }

    protected function prepareValue(mixed $value, string $type): ?string
    {
        if (is_null($value)) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? 'true' : 'false',
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            default => (string) $value,
        };
    }
}

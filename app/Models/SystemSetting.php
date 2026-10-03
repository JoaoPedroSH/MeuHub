<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $value = static::where('key', $key)->value('value');
        return $value === null ? $default : match ($value) { '1', 'true' => true, '0', 'false' => false, default => $value };
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]);
    }

    public static function getSecret(string $key, mixed $default = null): mixed
    {
        $value = static::where('key', $key)->value('value');
        if ($value === null) return $default;
        try { return Crypt::decryptString($value); } catch (\Throwable) { return $default; }
    }

    public static function setSecret(string $key, ?string $value): void
    {
        if (blank($value)) return;
        static::updateOrCreate(['key' => $key], ['value' => Crypt::encryptString($value)]);
    }

    public static function getList(string $key, array $default = []): array
    {
        $value = static::getValue($key);
        if ($value === null || $value === '') return $default;
        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : $default;
    }

    public static function setList(string $key, array $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => json_encode(array_values($value), JSON_UNESCAPED_SLASHES)]);
    }
}

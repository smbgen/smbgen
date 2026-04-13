<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'key' => 'string',
        'value' => 'string',
        'type' => 'string',
    ];

    /**
     * Get a setting value by key with proper type casting
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        // Cast based on type
        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        // Convert boolean to string for storage
        if ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        // Convert array/object to JSON
        if ($type === 'json' && (is_array($value) || is_object($value))) {
            $value = json_encode($value);
        }

        // Ensure all values are explicitly cast to string to prevent PostgreSQL type issues
        $valueString = (string) $value;
        $typeString = (string) $type;

        // Use explicit update/insert instead of updateOrCreate to avoid PostgreSQL binding issues
        $existing = static::where('key', $key)->first();

        if ($existing) {
            $existing->update([
                'value' => $valueString,
                'type' => $typeString,
            ]);
        } else {
            static::create([
                'key' => $key,
                'value' => $valueString,
                'type' => $typeString,
            ]);
        }
    }

    /**
     * Get all settings as key-value array with proper type casting
     */
    public static function getAll(): array
    {
        $settings = static::all();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = static::get($setting->key);
        }

        return $result;
    }
}

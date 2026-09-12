<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    const CACHE_KEY = 'settings.all';

    /**
     * Load all settings into cache (forever) and return as key→value map.
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a setting value by key, with optional default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        if (!array_key_exists($key, $settings)) {
            return $default;
        }

        $raw = $settings[$key];

        // Detect boolean stored as '1'/'0'
        if ($raw === '1' || $raw === '0') {
            // Check DB for type
            $type = $this->getType($key);
            if ($type === Setting::TYPE_BOOLEAN) {
                return (bool) $raw;
            }
        }

        if ($raw === null) {
            return $default;
        }

        return $raw;
    }

    /**
     * Get a boolean setting.
     */
    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->get($key);
        if ($value === null) return $default;
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get an integer setting.
     */
    public function int(string $key, int $default = 0): int
    {
        $value = $this->get($key);
        return $value !== null ? (int) $value : $default;
    }

    /**
     * Set a setting value and invalidate cache.
     */
    public function set(string $key, mixed $value): void
    {
        // Store booleans as '1'/'0' strings
        if (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'label' => $key]
        );

        // Invalidate the entire settings cache
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Set multiple settings at once and invalidate cache once.
     */
    public function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? '1' : '0';
            }
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'label' => $key]
            );
        }

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get all settings for a specific group (returns full model data).
     */
    public function group(string $group): \Illuminate\Database\Eloquent\Collection
    {
        return Setting::where('group', $group)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get the type for a key from DB (uncached, used sparingly).
     */
    private function getType(string $key): ?string
    {
        return Setting::where('key', $key)->value('type');
    }
}

<?php

use App\Services\SettingsService;

if (! function_exists('setting')) {
    /**
     * Get or set a setting value.
     *
     * Usage:
     *   setting('site_name')            → get value
     *   setting('site_name', 'default') → get with default
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}

if (! function_exists('setting_bool')) {
    /**
     * Get a boolean setting value.
     *
     * Usage:
     *   setting_bool('feature_dictionary')      → true/false
     *   setting_bool('maintenance_mode', false) → false if not set
     */
    function setting_bool(string $key, bool $default = false): bool
    {
        return app(SettingsService::class)->bool($key, $default);
    }
}

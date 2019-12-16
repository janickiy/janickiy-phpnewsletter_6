<?php

namespace App\Helpers;

use App\Models\Settings;

class SettingsHelpers
{
    public static function getSetting($key = '')
    {
        $setting = Settings::where('name',strtoupper($key))->first();
        if ($setting) {
            return $setting->value;
        } else {
            return '';
        }
    }
}
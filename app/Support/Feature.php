<?php

namespace App\Support;

class Feature
{
    public static function enabled(string $flag): bool
    {
        // Prefer new central config `feature`, fallback to legacy `features`
        $featureConfig = config('feature');
        if (is_array($featureConfig) && array_key_exists($flag, $featureConfig)) {
            return (bool) $featureConfig[$flag];
        }

        $legacyConfig = config('features');
        if (is_array($legacyConfig) && array_key_exists($flag, $legacyConfig)) {
            return (bool) $legacyConfig[$flag];
        }

        return false;
    }
}

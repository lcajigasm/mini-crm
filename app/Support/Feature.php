<?php

namespace App\Support;

class Feature
{
    public static function enabled(string $flag): bool
    {
        $config = config('features');
        return (bool) ($config[$flag] ?? false);
    }
}

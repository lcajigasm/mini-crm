<?php

namespace App\Support;

class TemplateRenderer
{
    /**
     * Render content by replacing {{var|fallback}} placeholders with values.
     * Variables not provided will use the fallback, or empty if missing.
     */
    public static function render(string $content, array $vars = []): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_\.]+)(?:\|([^}]+))?\s*\}\}/', function ($matches) use ($vars) {
            $key = $matches[1] ?? '';
            $fallback = $matches[2] ?? '';
            $value = self::arrayGetDot($vars, $key);
            if ($value === null || $value === '') {
                return (string) $fallback;
            }
            return (string) $value;
        }, $content);
    }

    private static function arrayGetDot(array $array, string $key): mixed
    {
        if ($key === '') {
            return null;
        }
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        $segments = explode('.', $key);
        $value = $array;
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return null;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}



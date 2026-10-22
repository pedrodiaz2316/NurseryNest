<?php

declare(strict_types=1);

namespace NurseryNest\Core;

/**
 * Loads KEY=VALUE pairs from a .env file into a read-only config map.
 */
final class Env
{
    /** @var array<string, string> */
    private static array $values = [];

    public static function load(string $path): void
    {
        if (!is_file($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            self::$values[trim($key)] = trim(trim($value), "\"'");
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        $fromServer = getenv($key);
        if ($fromServer !== false) {
            return $fromServer;
        }

        return self::$values[$key] ?? $default;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = self::get($key, $default ? 'true' : 'false');

        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }
}

<?php

declare(strict_types=1);

namespace NurseryNest\Core;

/**
 * One cookie per area so client, nursery and admin logins never share a session (AUTH-8, ADM-5).
 */
final class Session
{
    public const AREA_CLIENT  = 'client';
    public const AREA_NURSERY = 'nursery';
    public const AREA_ADMIN   = 'admin';

    public static function areaForPath(string $path): string
    {
        if (str_starts_with($path, '/admin')) {
            return self::AREA_ADMIN;
        }
        if (str_starts_with($path, '/dashboard') || str_starts_with($path, '/nursery')) {
            return self::AREA_NURSERY;
        }

        return self::AREA_CLIENT;
    }

    public static function start(string $area): void
    {
        $secure = str_starts_with(Env::get('APP_URL'), 'https://');

        session_name('nn_' . $area);
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();

        self::expireIfIdle();
    }

    /** AUTH-3: drop the session after a period of inactivity. */
    private static function expireIfIdle(): void
    {
        $idleSeconds = (int) Env::get('SESSION_IDLE_MINUTES', '30') * 60;
        $last = $_SESSION['_last_activity'] ?? null;

        if ($last !== null && time() - $last > $idleSeconds) {
            $_SESSION = [];
            session_regenerate_id(true);
            self::flash('info', 'Your session expired. Please log in again.');
        }

        $_SESSION['_last_activity'] = time();
    }

    /** Call right after a successful login to prevent session fixation. */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /** @return list<array{type: string, message: string}> */
    public static function takeFlashes(): array
    {
        $flashes = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);

        return $flashes;
    }
}

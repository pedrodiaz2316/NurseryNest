<?php

declare(strict_types=1);

namespace NurseryNest\Core;

/**
 * Per-session CSRF token on every state-changing request (NFR-1).
 */
final class Csrf
{
    public static function token(): string
    {
        $token = Session::get('_csrf');
        if (!is_string($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set('_csrf', $token);
        }

        return $token;
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . View::e(self::token()) . '">';
    }

    public static function verify(): void
    {
        $sent = $_POST['_csrf'] ?? '';
        if (!is_string($sent) || !hash_equals(self::token(), $sent)) {
            http_response_code(419);
            View::render('errors/419', ['title' => 'Form expired'], 'public');
            exit;
        }
    }
}

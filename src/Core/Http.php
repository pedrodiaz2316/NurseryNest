<?php

declare(strict_types=1);

namespace NurseryNest\Core;

final class Http
{
    public static function redirect(string $to): never
    {
        header('Location: ' . $to, true, 303);
        exit;
    }

    public static function notFound(): never
    {
        http_response_code(404);
        View::render('errors/404', ['title' => 'Page not found'], 'public');
        exit;
    }

    public static function forbidden(): never
    {
        http_response_code(403);
        View::render('errors/403', ['title' => 'Not allowed'], 'public');
        exit;
    }

    public static function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    public static function query(string $key, string $default = ''): string
    {
        $value = $_GET[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }
}

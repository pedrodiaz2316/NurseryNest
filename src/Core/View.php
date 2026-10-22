<?php

declare(strict_types=1);

namespace NurseryNest\Core;

/**
 * Renders a PHP template inside a layout. Templates escape every value with e() (NFR-1).
 */
final class View
{
    private static string $templateDir = __DIR__ . '/../../templates';

    /** @param array<string, mixed> $data */
    public static function render(string $template, array $data = [], string $layout = 'public'): void
    {
        $content = self::capture($template, $data);
        echo self::capture('layouts/' . $layout, $data + ['content' => $content]);
    }

    /** @param array<string, mixed> $data */
    public static function capture(string $template, array $data = []): string
    {
        $file = self::$templateDir . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("Template not found: {$template}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $file;

        return (string) ob_get_clean();
    }

    public static function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function money(float|string $amount): string
    {
        return '$' . number_format((float) $amount, 2);
    }
}

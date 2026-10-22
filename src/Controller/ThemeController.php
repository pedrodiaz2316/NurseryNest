<?php

declare(strict_types=1);

namespace NurseryNest\Controller;

use NurseryNest\Core\Http;
use NurseryNest\Repository\NurseryRepository;

/**
 * Serves a nursery's theme colours as CSS custom properties (BRAND-2, BRAND-3).
 * One shared stylesheet plus this small file styles every nursery.
 */
final class ThemeController
{
    private const FALLBACK_PRIMARY = '#2f6b3b';
    private const FALLBACK_SECONDARY = '#e4f0df';

    public function __construct(private readonly NurseryRepository $nurseries)
    {
    }

    public function css(string $slug): void
    {
        $nursery = $this->nurseries->findBySlug($slug) ?? Http::notFound();

        header('Content-Type: text/css; charset=utf-8');
        header('Cache-Control: public, max-age=300');

        printf(
            ":root{--brand-primary:%s;--brand-secondary:%s;}\n",
            self::color($nursery['primary_color'] ?? null, self::FALLBACK_PRIMARY),
            self::color($nursery['secondary_color'] ?? null, self::FALLBACK_SECONDARY),
        );
    }

    /** Only #rrggbb values reach the stylesheet. */
    public static function color(?string $value, string $fallback): string
    {
        return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? strtolower($value) : $fallback;
    }
}

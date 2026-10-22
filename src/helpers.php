<?php

declare(strict_types=1);

use NurseryNest\Core\Csrf;
use NurseryNest\Core\View;

/** Escape for HTML output (NFR-1). */
function e(mixed $value): string
{
    return View::e($value);
}

function money(float|string $amount): string
{
    return View::money($amount);
}

function csrf_field(): string
{
    return Csrf::field();
}

/** aria-current for nav links. */
function current_attr(string $href, bool $prefix = false): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $active = $prefix ? str_starts_with($path, $href) : $path === $href;

    return $active ? ' aria-current="page"' : '';
}

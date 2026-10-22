<?php

declare(strict_types=1);

namespace NurseryNest\Controller;

use NurseryNest\Core\Http;
use NurseryNest\Core\View;
use NurseryNest\Repository\NurseryRepository;

/**
 * A nursery's public page with its profile and inventory, in its own theme (WEB-6, BRAND-1).
 */
final class StorefrontController
{
    public function __construct(private readonly NurseryRepository $nurseries)
    {
    }

    public function show(string $slug): void
    {
        $nursery = $this->nurseries->findBySlug($slug) ?? Http::notFound();
        $search = Http::query('q');
        $category = Http::query('category');

        View::render('storefront/show', [
            'title'      => $nursery['name'],
            'nursery'    => $nursery,
            'items'      => $this->nurseries->publicItems((int) $nursery['id'], $search, $category),
            'categories' => $this->nurseries->publicCategories((int) $nursery['id']),
            'search'     => $search,
            'category'   => $category,
        ], 'storefront');
    }

    public function item(string $slug, string $id): void
    {
        $nursery = $this->nurseries->findBySlug($slug) ?? Http::notFound();
        $item = $this->nurseries->publicItem((int) $nursery['id'], (int) $id) ?? Http::notFound();

        View::render('storefront/item', [
            'title'   => $item['name'] . ' | ' . $nursery['name'],
            'nursery' => $nursery,
            'item'    => $item,
        ], 'storefront');
    }
}

<?php

declare(strict_types=1);

use NurseryNest\Controller\ClientController;
use NurseryNest\Controller\DashboardController;
use NurseryNest\Controller\InventoryController;
use NurseryNest\Controller\NurseryAuthController;
use NurseryNest\Controller\PublicController;
use NurseryNest\Controller\StorefrontController;
use NurseryNest\Controller\ThemeController;
use NurseryNest\Core\Database;
use NurseryNest\Core\Router;
use NurseryNest\Repository\NurseryRepository;

return static function (Router $r): void {
    $nurseries = static fn () => new NurseryRepository(Database::connection());

    // Public marketing site (WEB-1 to WEB-5)
    $r->get('/', fn () => (new PublicController($nurseries()))->home());
    $r->get('/about', fn () => (new PublicController($nurseries()))->about());
    $r->get('/specialty', fn () => (new PublicController($nurseries()))->specialty());
    $r->get('/features', fn () => (new PublicController($nurseries()))->features());
    $r->get('/nurseries', fn () => (new PublicController($nurseries()))->nurseries());
    $r->get('/contact', fn () => (new PublicController($nurseries()))->contact());

    // Nursery storefronts (WEB-6) and their theme colours (BRAND-3)
    $r->get('/theme/{slug}.css', fn (string $slug) => (new ThemeController($nurseries()))->css($slug));
    $r->get('/n/{slug}', fn (string $slug) => (new StorefrontController($nurseries()))->show($slug));
    $r->get('/n/{slug}/item/{id}', fn (string $slug, string $id) => (new StorefrontController($nurseries()))->item($slug, $id));

    // Client accounts (CLI-1 to CLI-6)
    $r->get('/login', fn () => (new ClientController($nurseries()))->showLogin());
    $r->post('/login', fn () => (new ClientController($nurseries()))->login());
    $r->get('/signup', fn () => (new ClientController($nurseries()))->showSignup());
    $r->post('/signup', fn () => (new ClientController($nurseries()))->signup());
    $r->post('/logout', fn () => (new ClientController($nurseries()))->logout());
    $r->get('/account', fn () => (new ClientController($nurseries()))->account());
    $r->get('/browse', fn () => (new ClientController($nurseries()))->browse());

    // Nursery login and dashboard
    $r->get('/nursery/login', fn () => (new NurseryAuthController())->showLogin());
    $r->post('/nursery/login', fn () => (new NurseryAuthController())->login());
    $r->post('/nursery/logout', fn () => (new NurseryAuthController())->logout());

    $r->get('/dashboard', fn () => (new DashboardController())->home());
    $r->get('/dashboard/inventory', fn () => (new InventoryController())->index());
    $r->get('/dashboard/inventory/new', fn () => (new InventoryController())->create());
    $r->post('/dashboard/inventory', fn () => (new InventoryController())->store());
    $r->get('/dashboard/inventory/{id}/edit', fn (string $id) => (new InventoryController())->edit($id));
    $r->post('/dashboard/inventory/{id}', fn (string $id) => (new InventoryController())->update($id));
    $r->post('/dashboard/inventory/{id}/price', fn (string $id) => (new InventoryController())->updatePrice($id));
    $r->post('/dashboard/inventory/{id}/remove', fn (string $id) => (new InventoryController())->destroy($id));
};

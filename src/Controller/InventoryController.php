<?php

declare(strict_types=1);

namespace NurseryNest\Controller;

use NurseryNest\Auth\NurseryAuth;
use NurseryNest\Auth\Policy;
use NurseryNest\Core\Csrf;
use NurseryNest\Core\Database;
use NurseryNest\Core\Http;
use NurseryNest\Core\Session;
use NurseryNest\Core\View;
use NurseryNest\Repository\InventoryRepository;

/**
 * Inventory management in the nursery dashboard (INV-1 to INV-6, AUTH-9).
 */
final class InventoryController
{
    public function index(): void
    {
        $user = NurseryAuth::requirePermission(Policy::VIEW_INVENTORY);
        $repo = $this->repo($user);
        $search = Http::query('q');
        $category = Http::query('category');

        View::render('dashboard/inventory/index', [
            'title'      => 'Inventory',
            'items'      => $repo->all($search, $category),
            'categories' => $repo->categories(),
            'search'     => $search,
            'category'   => $category,
        ], 'dashboard');
    }

    public function create(): void
    {
        NurseryAuth::requirePermission(Policy::MANAGE_INVENTORY);

        View::render('dashboard/inventory/form', [
            'title'      => 'Add item',
            'item'       => null,
            'old'        => [],
            'attributes' => '',
        ], 'dashboard');
    }

    public function store(): void
    {
        $user = NurseryAuth::requirePermission(Policy::MANAGE_INVENTORY);
        Csrf::verify();

        [$data, $errors] = ItemInput::fromPost();
        $canPrice = Policy::can($user['role'], Policy::CHANGE_PRICES);
        $price = $canPrice ? ItemInput::price(Http::input('price'), $errors) : 0.0;

        if ($errors !== []) {
            View::render('dashboard/inventory/form', [
                'title'      => 'Add item',
                'item'       => null,
                'old'        => $_POST,
                'attributes' => Http::input('attributes'),
                'errors'     => $errors,
            ], 'dashboard');
            return;
        }

        $repo = $this->repo($user);
        $id = $repo->create($data, $price);
        $repo->replaceAttributes($id, ItemInput::attributes(Http::input('attributes')));

        Session::flash('success', $canPrice
            ? 'Item added.'
            : 'Item added. It stays hidden from your public page until a Nursery Manager sets its price.');
        Http::redirect('/dashboard/inventory');
    }

    public function edit(string $id): void
    {
        $user = NurseryAuth::requirePermission(Policy::MANAGE_INVENTORY);
        $repo = $this->repo($user);
        $item = $repo->find((int) $id) ?? Http::notFound();

        View::render('dashboard/inventory/form', [
            'title'      => 'Edit ' . $item['name'],
            'item'       => $item,
            'old'        => $item,
            'attributes' => ItemInput::attributesToText($repo->attributes((int) $id)),
        ], 'dashboard');
    }

    public function update(string $id): void
    {
        $user = NurseryAuth::requirePermission(Policy::MANAGE_INVENTORY);
        Csrf::verify();
        $repo = $this->repo($user);
        $item = $repo->find((int) $id) ?? Http::notFound();

        [$data, $errors] = ItemInput::fromPost();
        if ($errors !== []) {
            View::render('dashboard/inventory/form', [
                'title'      => 'Edit ' . $item['name'],
                'item'       => $item,
                'old'        => $_POST + ['price' => $item['price']],
                'attributes' => Http::input('attributes'),
                'errors'     => $errors,
            ], 'dashboard');
            return;
        }

        $repo->update((int) $id, $data);
        $repo->replaceAttributes((int) $id, ItemInput::attributes(Http::input('attributes')));

        Session::flash('success', 'Item updated.');
        Http::redirect('/dashboard/inventory');
    }

    /** Price changes are their own action so the permission check can tell them apart (AUTH-9). */
    public function updatePrice(string $id): void
    {
        $user = NurseryAuth::requirePermission(Policy::CHANGE_PRICES);
        Csrf::verify();
        $repo = $this->repo($user);
        $item = $repo->find((int) $id) ?? Http::notFound();

        $errors = [];
        $price = ItemInput::price(Http::input('price'), $errors);
        if ($errors !== []) {
            Session::flash('error', $errors['price']);
        } else {
            $repo->updatePrice((int) $id, $price);
            Session::flash('success', 'Price for ' . $item['name'] . ' set to $' . number_format($price, 2) . '.');
        }

        Http::redirect('/dashboard/inventory/' . (int) $id . '/edit');
    }

    public function destroy(string $id): void
    {
        $user = NurseryAuth::requirePermission(Policy::MANAGE_INVENTORY);
        Csrf::verify();

        if ($this->repo($user)->remove((int) $id)) {
            Session::flash('success', 'Item removed.');
        }
        Http::redirect('/dashboard/inventory');
    }

    /** @param array{nursery_id: int} $user */
    private function repo(array $user): InventoryRepository
    {
        return new InventoryRepository(Database::connection(), $user['nursery_id']);
    }
}

<?php

declare(strict_types=1);

namespace NurseryNest\Controller;

use NurseryNest\Auth\NurseryAuth;
use NurseryNest\Auth\Policy;
use NurseryNest\Core\Database;
use NurseryNest\Core\View;
use NurseryNest\Repository\InventoryRepository;

final class DashboardController
{
    public function home(): void
    {
        $user = NurseryAuth::requirePermission(Policy::VIEW_INVENTORY);
        $inventory = new InventoryRepository(Database::connection(), $user['nursery_id']);

        View::render('dashboard/home', [
            'title'   => 'Overview',
            'summary' => $inventory->summary(),
        ], 'dashboard');
    }
}

<?php

declare(strict_types=1);

namespace NurseryNest\Tests;

use NurseryNest\Auth\Policy;
use PHPUnit\Framework\TestCase;

final class PolicyTest extends TestCase
{
    public function testOnlyManagersChangePrices(): void
    {
        $this->assertTrue(Policy::can(Policy::ROLE_MANAGER, Policy::CHANGE_PRICES));
        $this->assertFalse(Policy::can(Policy::ROLE_OFFICE, Policy::CHANGE_PRICES));
        $this->assertFalse(Policy::can(Policy::ROLE_INVENTORY, Policy::CHANGE_PRICES));
        $this->assertFalse(Policy::can(Policy::ROLE_SALES, Policy::CHANGE_PRICES));
    }

    public function testInventoryManagementRoles(): void
    {
        $this->assertTrue(Policy::can(Policy::ROLE_INVENTORY, Policy::MANAGE_INVENTORY));
        $this->assertFalse(Policy::can(Policy::ROLE_SALES, Policy::MANAGE_INVENTORY));
        $this->assertFalse(Policy::can(Policy::ROLE_OFFICE, Policy::MANAGE_INVENTORY));
    }

    public function testEveryRoleCanViewInventory(): void
    {
        foreach (array_keys(Policy::ROLE_LABELS) as $role) {
            $this->assertTrue(Policy::can($role, Policy::VIEW_INVENTORY), $role);
        }
    }

    public function testUnknownActionOrRoleIsDenied(): void
    {
        $this->assertFalse(Policy::can(Policy::ROLE_MANAGER, 'launch_rockets'));
        $this->assertFalse(Policy::can('platform_admin', Policy::VIEW_INVENTORY));
    }

    public function testOnlyManagerCanAssignManagerRole(): void
    {
        $this->assertTrue(Policy::canAssignRole(Policy::ROLE_MANAGER, Policy::ROLE_MANAGER));
        $this->assertFalse(Policy::canAssignRole(Policy::ROLE_OFFICE, Policy::ROLE_MANAGER));
        $this->assertTrue(Policy::canAssignRole(Policy::ROLE_OFFICE, Policy::ROLE_SALES));
        $this->assertFalse(Policy::canAssignRole(Policy::ROLE_SALES, Policy::ROLE_SALES));
    }
}

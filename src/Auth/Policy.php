<?php

declare(strict_types=1);

namespace NurseryNest\Auth;

/**
 * The AUTH-9 permission matrix, coded in one place. Still a draft in the requirements:
 * changing who can do what is an edit to MATRIX only.
 */
final class Policy
{
    public const VIEW_INVENTORY    = 'view_inventory';
    public const MANAGE_INVENTORY  = 'manage_inventory';
    public const CHANGE_PRICES     = 'change_prices';
    public const VIEW_ORDERS       = 'view_orders';
    public const UPDATE_ORDERS     = 'update_order_status';
    public const CANCEL_ORDERS     = 'cancel_orders';
    public const ISSUE_REFUNDS     = 'issue_refunds';
    public const MANAGE_CLIENTS    = 'manage_clients';
    public const MANAGE_STAFF      = 'manage_staff';
    public const MANAGE_USERS      = 'manage_users';
    public const EDIT_SETTINGS     = 'edit_settings';

    public const ROLE_MANAGER     = 'nursery_manager';
    public const ROLE_OFFICE      = 'office_admin';
    public const ROLE_INVENTORY   = 'inventory_coordinator';
    public const ROLE_SALES       = 'sales_rep';

    private const MATRIX = [
        self::VIEW_INVENTORY   => [self::ROLE_MANAGER, self::ROLE_OFFICE, self::ROLE_INVENTORY, self::ROLE_SALES],
        self::MANAGE_INVENTORY => [self::ROLE_MANAGER, self::ROLE_INVENTORY],
        self::CHANGE_PRICES    => [self::ROLE_MANAGER],
        self::VIEW_ORDERS      => [self::ROLE_MANAGER, self::ROLE_OFFICE, self::ROLE_INVENTORY, self::ROLE_SALES],
        self::UPDATE_ORDERS    => [self::ROLE_MANAGER, self::ROLE_INVENTORY, self::ROLE_SALES],
        self::CANCEL_ORDERS    => [self::ROLE_MANAGER, self::ROLE_SALES],
        self::ISSUE_REFUNDS    => [self::ROLE_MANAGER],
        self::MANAGE_CLIENTS   => [self::ROLE_MANAGER, self::ROLE_OFFICE, self::ROLE_SALES],
        self::MANAGE_STAFF     => [self::ROLE_MANAGER, self::ROLE_OFFICE],
        self::MANAGE_USERS     => [self::ROLE_MANAGER, self::ROLE_OFFICE],
        self::EDIT_SETTINGS    => [self::ROLE_MANAGER, self::ROLE_OFFICE],
    ];

    public const ROLE_LABELS = [
        self::ROLE_MANAGER   => 'Nursery Manager',
        self::ROLE_OFFICE    => 'Office Administrator',
        self::ROLE_INVENTORY => 'Inventory Coordinator',
        self::ROLE_SALES     => 'Sales and Service Representative',
    ];

    public static function can(string $role, string $action): bool
    {
        return in_array($role, self::MATRIX[$action] ?? [], true);
    }

    /** AUTH-6: only a Nursery Manager can create or remove another Nursery Manager. */
    public static function canAssignRole(string $actorRole, string $targetRole): bool
    {
        if (!self::can($actorRole, self::MANAGE_USERS)) {
            return false;
        }

        return $targetRole !== self::ROLE_MANAGER || $actorRole === self::ROLE_MANAGER;
    }
}

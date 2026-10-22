<?php

declare(strict_types=1);

namespace NurseryNest\Auth;

use NurseryNest\Core\Database;
use NurseryNest\Core\Http;
use NurseryNest\Core\Session;

/**
 * Nursery user login. The nursery_id used for every tenant query comes from here, never from the request.
 */
final class NurseryAuth
{
    /** @return array{id: int, nursery_id: int, email: string, role: string}|null */
    public static function attempt(string $email, string $password): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT u.id, u.nursery_id, u.email, u.password_hash, u.role
             FROM users u
             JOIN nurseries n ON n.id = u.nursery_id
             WHERE u.email = ? AND u.is_active = 1 AND n.is_active = 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }

        self::rehashIfNeeded((int) $user['id'], $password, $user['password_hash']);

        Database::connection()
            ->prepare('UPDATE users SET last_login = UTC_TIMESTAMP() WHERE id = ?')
            ->execute([$user['id']]);

        $sessionUser = [
            'id'         => (int) $user['id'],
            'nursery_id' => (int) $user['nursery_id'],
            'email'      => $user['email'],
            'role'       => $user['role'],
        ];

        Session::regenerate();
        Session::set('nursery_user', $sessionUser);

        return $sessionUser;
    }

    private static function rehashIfNeeded(int $userId, string $password, string $hash): void
    {
        if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            Database::connection()
                ->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($password, PASSWORD_DEFAULT), $userId]);
        }
    }

    /** @return array{id: int, nursery_id: int, email: string, role: string}|null */
    public static function user(): ?array
    {
        $user = Session::get('nursery_user');

        return is_array($user) ? $user : null;
    }

    /** @return array{id: int, nursery_id: int, email: string, role: string} */
    public static function require(): array
    {
        $user = self::user();
        if ($user === null) {
            Session::flash('info', 'Please log in to your nursery account.');
            Http::redirect('/nursery/login');
        }

        return $user;
    }

    /** @return array{id: int, nursery_id: int, email: string, role: string} */
    public static function requirePermission(string $action): array
    {
        $user = self::require();
        if (!Policy::can($user['role'], $action)) {
            Http::forbidden();
        }

        return $user;
    }

    public static function logout(): void
    {
        Session::destroy();
    }
}

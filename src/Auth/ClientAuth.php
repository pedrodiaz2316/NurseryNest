<?php

declare(strict_types=1);

namespace NurseryNest\Auth;

use NurseryNest\Core\Database;
use NurseryNest\Core\Http;
use NurseryNest\Core\Session;
use PDOException;

/**
 * Platform-wide client accounts (CLI-1, CLI-2). Separate from nursery logins (AUTH-8).
 */
final class ClientAuth
{
    public const MIN_PASSWORD_LENGTH = 10;

    /** @return array{id: int, name: string, email: string}|null */
    public static function attempt(string $email, string $password): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT id, name, email, password_hash FROM clients WHERE email = ? AND is_active = 1'
        );
        $stmt->execute([$email]);
        $client = $stmt->fetch();

        if (!$client || !password_verify($password, $client['password_hash'])) {
            return null;
        }

        if (password_needs_rehash($client['password_hash'], PASSWORD_DEFAULT)) {
            Database::connection()
                ->prepare('UPDATE clients SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($password, PASSWORD_DEFAULT), $client['id']]);
        }

        Database::connection()
            ->prepare('UPDATE clients SET last_login = UTC_TIMESTAMP() WHERE id = ?')
            ->execute([$client['id']]);

        return self::loginAs((int) $client['id'], $client['name'], $client['email']);
    }

    /**
     * @return array{id: int, name: string, email: string}|string The new client, or an error message.
     */
    public static function register(string $name, string $email, string $phone, string $password): array|string
    {
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Please enter your name and a valid email address.';
        }
        if (mb_strlen($password) < self::MIN_PASSWORD_LENGTH) {
            return 'Passwords must be at least ' . self::MIN_PASSWORD_LENGTH . ' characters.';
        }

        try {
            Database::connection()
                ->prepare('INSERT INTO clients (name, email, phone, password_hash) VALUES (?, ?, ?, ?)')
                ->execute([$name, $email, $phone !== '' ? $phone : null, password_hash($password, PASSWORD_DEFAULT)]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) {
                return 'An account with that email already exists. Try logging in.';
            }
            throw $e;
        }

        return self::loginAs((int) Database::connection()->lastInsertId(), $name, $email);
    }

    /** @return array{id: int, name: string, email: string} */
    private static function loginAs(int $id, string $name, string $email): array
    {
        $client = ['id' => $id, 'name' => $name, 'email' => $email];
        Session::regenerate();
        Session::set('client', $client);

        return $client;
    }

    /** @return array{id: int, name: string, email: string}|null */
    public static function user(): ?array
    {
        $client = Session::get('client');

        return is_array($client) ? $client : null;
    }

    /** @return array{id: int, name: string, email: string} */
    public static function require(string $returnTo = '/account'): array
    {
        $client = self::user();
        if ($client === null) {
            Session::set('return_to', $returnTo);
            Session::flash('info', 'Please log in or create a free account to continue.');
            Http::redirect('/login');
        }

        return $client;
    }

    public static function logout(): void
    {
        Session::destroy();
    }
}

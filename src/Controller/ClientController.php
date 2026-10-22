<?php

declare(strict_types=1);

namespace NurseryNest\Controller;

use NurseryNest\Auth\ClientAuth;
use NurseryNest\Core\Csrf;
use NurseryNest\Core\Database;
use NurseryNest\Core\Http;
use NurseryNest\Core\Session;
use NurseryNest\Core\View;
use NurseryNest\Repository\NurseryRepository;

/**
 * Client sign-up, login, account and the all-nursery browse view (CLI-1 to CLI-6, WEB-7).
 */
final class ClientController
{
    public function __construct(private readonly NurseryRepository $nurseries)
    {
    }

    public function showLogin(): void
    {
        if (ClientAuth::user() !== null) {
            Http::redirect('/account');
        }
        View::render('client/login', ['title' => 'Log in', 'email' => '']);
    }

    public function login(): void
    {
        Csrf::verify();
        $email = Http::input('email');

        if (ClientAuth::attempt($email, Http::input('password')) === null) {
            View::render('client/login', [
                'title' => 'Log in',
                'email' => $email,
                'error' => 'That email and password don\'t match an account.',
            ]);
            return;
        }

        Http::redirect($this->takeReturnTo());
    }

    public function showSignup(): void
    {
        View::render('client/signup', ['title' => 'Create your account', 'old' => []]);
    }

    public function signup(): void
    {
        Csrf::verify();
        $old = ['name' => Http::input('name'), 'email' => Http::input('email'), 'phone' => Http::input('phone')];

        if (Http::input('password') !== Http::input('password_confirm')) {
            View::render('client/signup', ['title' => 'Create your account', 'old' => $old, 'error' => 'The passwords don\'t match.']);
            return;
        }

        $result = ClientAuth::register($old['name'], $old['email'], $old['phone'], Http::input('password'));
        if (is_string($result)) {
            View::render('client/signup', ['title' => 'Create your account', 'old' => $old, 'error' => $result]);
            return;
        }

        Session::flash('success', 'Welcome to NurseryNest, ' . $result['name'] . '!');
        Http::redirect($this->takeReturnTo());
    }

    public function logout(): void
    {
        Csrf::verify();
        ClientAuth::logout();
        Http::redirect('/');
    }

    public function account(): void
    {
        $client = ClientAuth::require();
        $db = Database::connection();

        $profile = $db->prepare('SELECT name, email, phone, created_at FROM clients WHERE id = ?');
        $profile->execute([$client['id']]);

        $addresses = $db->prepare('SELECT label, address, city, state, zip FROM client_addresses WHERE client_id = ? ORDER BY id');
        $addresses->execute([$client['id']]);

        $orders = $db->prepare(
            'SELECT o.id, o.status, o.total, o.created_at, n.name AS nursery_name
             FROM orders o JOIN nurseries n ON n.id = o.nursery_id
             WHERE o.client_id = ? ORDER BY o.created_at DESC'
        );
        $orders->execute([$client['id']]);

        View::render('client/account', [
            'title'     => 'My account',
            'profile'   => $profile->fetch(),
            'addresses' => $addresses->fetchAll(),
            'orders'    => $orders->fetchAll(),
        ]);
    }

    public function browse(): void
    {
        ClientAuth::require('/browse');
        $search = Http::query('q');
        $category = Http::query('category');

        View::render('client/browse', [
            'title'      => 'Browse all nurseries',
            'items'      => $this->nurseries->searchAllNurseries($search, $category),
            'categories' => $this->nurseries->allCategories(),
            'search'     => $search,
            'category'   => $category,
        ]);
    }

    private function takeReturnTo(): string
    {
        $to = Session::get('return_to', '/account');
        Session::forget('return_to');

        // Only allow local paths.
        return is_string($to) && str_starts_with($to, '/') && !str_starts_with($to, '//') ? $to : '/account';
    }
}

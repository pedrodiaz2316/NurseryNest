<?php

declare(strict_types=1);

namespace NurseryNest\Controller;

use NurseryNest\Auth\NurseryAuth;
use NurseryNest\Core\Csrf;
use NurseryNest\Core\Http;
use NurseryNest\Core\View;

final class NurseryAuthController
{
    public function showLogin(): void
    {
        if (NurseryAuth::user() !== null) {
            Http::redirect('/dashboard');
        }
        View::render('nursery/login', ['title' => 'Nursery login', 'email' => '']);
    }

    public function login(): void
    {
        Csrf::verify();
        $email = Http::input('email');

        if (NurseryAuth::attempt($email, Http::input('password')) === null) {
            View::render('nursery/login', [
                'title' => 'Nursery login',
                'email' => $email,
                'error' => 'That email and password don\'t match an active nursery account.',
            ]);
            return;
        }

        Http::redirect('/dashboard');
    }

    public function logout(): void
    {
        Csrf::verify();
        NurseryAuth::logout();
        Http::redirect('/nursery/login');
    }
}

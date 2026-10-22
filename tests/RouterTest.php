<?php

declare(strict_types=1);

namespace NurseryNest\Tests;

use NurseryNest\Core\Router;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{
    public function testMatchesStaticAndParameterRoutes(): void
    {
        $router = new Router();
        $router->get('/', fn () => null);
        $router->get('/n/{slug}/item/{id}', fn () => null);
        $router->get('/theme/{slug}.css', fn () => null);

        $this->assertSame([], $router->match('GET', '/')['params']);
        $this->assertSame(['slug' => 'gulf-coast', 'id' => '7'], $router->match('GET', '/n/gulf-coast/item/7')['params']);
        $this->assertSame(['slug' => 'sunshine-palms'], $router->match('GET', '/theme/sunshine-palms.css')['params']);
    }

    public function testMethodAndTrailingSlash(): void
    {
        $router = new Router();
        $router->post('/login', fn () => null);

        $this->assertNull($router->match('GET', '/login'));
        $this->assertNotNull($router->match('POST', '/login/'));
    }

    public function testParametersDoNotCrossSlashes(): void
    {
        $router = new Router();
        $router->get('/n/{slug}', fn () => null);

        $this->assertNull($router->match('GET', '/n/a/b'));
    }
}

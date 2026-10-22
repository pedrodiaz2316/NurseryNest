<?php

declare(strict_types=1);

namespace NurseryNest\Core;

/**
 * Minimal method + path router. Path parameters are written as {name}.
 */
final class Router
{
    /** @var list<array{method: string, regex: string, params: list<string>, handler: callable}> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, callable $handler): void
    {
        preg_match_all('/\{(\w+)\}/', $path, $matches);
        $regex = '#^' . preg_replace('/\\\{(\w+)\\\}/', '([^/]+)', preg_quote($path, '#')) . '$#';

        $this->routes[] = [
            'method'  => $method,
            'regex'   => $regex,
            'params'  => $matches[1],
            'handler' => $handler,
        ];
    }

    /**
     * @return array{handler: callable, params: array<string, string>}|null
     */
    public function match(string $method, string $path): ?array
    {
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method || !preg_match($route['regex'], $path, $values)) {
                continue;
            }
            array_shift($values);

            return [
                'handler' => $route['handler'],
                'params'  => array_combine($route['params'], array_map('rawurldecode', $values)),
            ];
        }

        return null;
    }

    public function dispatch(string $method, string $path): void
    {
        $match = $this->match($method, $path);

        if ($match === null) {
            Http::notFound();
        }

        ($match['handler'])(...$match['params']);
    }
}

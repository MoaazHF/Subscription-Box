<?php

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $uri, array $action): void
    {
        $this->routes['GET'][$this->normalize($uri)] = $action;
    }

    public function post(string $uri, array $action): void
    {
        $this->routes['POST'][$this->normalize($uri)] = $action;
    }

    public function dispatch(string $uri, string $method): void
    {
        $route = $this->routes[$method][$this->normalize($uri)] ?? null;

        if ($route === null) {
            http_response_code(404);
            echo '404 | Page not found';
            return;
        }

        [$controllerName, $controllerMethod] = $route;
        $controller = new $controllerName();
        $controller->$controllerMethod();
    }

    private function normalize(string $uri): string
    {
        $clean = '/' . trim($uri, '/');
        return $clean === '/' ? '/' : rtrim($clean, '/');
    }
}


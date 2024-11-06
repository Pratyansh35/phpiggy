<?php

declare(strict_types=1);

namespace Framework;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private array $error_handler = [];

    public function add(string $method, string $path, array $controller): void
    {
        $path = $this->normalizePath($path);
        $regex_path = preg_replace('#{[^/]+}#', '([^/]+)', $path);

        $this->routes[] = [
            'path' => $path,
            'method' => strtoupper($method),
            'controller' => $controller,
            'middlewares' => [],
            'regex_path' => $regex_path,
        ];
    }

    public function dispatch(string $path, string $method, Container $container = null): void
    {
        $path = $this->normalizePath($path);
        $method = strtoupper($_POST['_METHOD'] ?? $method);

        foreach ($this->routes as $route) {
            if (!preg_match("#^{$route['regex_path']}$#", $path, $param_values) || $route['method'] !== $method) {
                continue;
            }

            array_shift($param_values);

            preg_match_all('#{([^/]+)}#', $route['path'], $param_keys);

            $param_keys = $param_keys[1];

            $params = array_combine($param_keys, $param_values);

            [$class, $function] = $route['controller'];

            $controller_instance = $container ? $container->resolve($class) : new $class;

            $action = fn () => $controller_instance->{$function}($params);

            $all_middleware = [
                ...$route['middlewares'],
                ...$this->middlewares,
            ];

            foreach ($all_middleware as $middleware) {
                $middleware_instance = $container ? $container->resolve($middleware) : new $middleware;
                $action = fn () => $middleware_instance->process($action);
            }

            $action();

            return;
        }

        $this->dispatchNotFound($container);
    }

    public function addMiddleware(string $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function addRouteMiddleware(string $middleware): void
    {
        $last_route_key = array_key_last($this->routes);
        $this->routes[$last_route_key]['middlewares'][] = $middleware;
    }

    public function setErrorHandler(array $controller): void
    {
        $this->error_handler = $controller;
    }

    private function dispatchNotFound(?Container $container): void
    {
        [$class, $function] = $this->error_handler;

        $controller_instance = $container ? $container->resolve($class) : new $class;
        $action = fn () => $controller_instance->$function();

        foreach ($this->middlewares as $middleware) {
            $middleware_instance = $container ? $container->resolve($middleware) : new $middleware;
            $action = fn () => $middleware_instance->process($action);
        }

        $action();
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path, '/');
        $path = "/{$path}/";

        return preg_replace('#[/]{2,}#', '/', $path);
    }
}

<?php

declare(strict_types = 1);

namespace Framework;

class App
{
    private readonly Router $router;
    private readonly Container $container;

    public function __construct(string $container_definitions_path = null)
    {
        $this->router = new Router();
        $this->container = new Container();

        if ($container_definitions_path) {
            $container_definitions_path = include $container_definitions_path;
            $this->container->addDefinitions($container_definitions_path);
        }
    }

    public function run(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $this->router->dispatch($path, $method, $this->container);
    }

    public function addMiddleware(string $middleware): void
    {
        $this->router->addMiddleware($middleware);
    }

    public function get(string $path, array $controller): self
    {
        $this->router->add('GET', $path, $controller);

        return $this;
    }

    public function post(string $path, array $controller): self
    {
        $this->router->add('POST', $path, $controller);

        return $this;
    }

    public function delete(string $path, array $controller): self
    {
        $this->router->add('DELETE', $path, $controller);

        return $this;
    }

    public function middleware(string $middleware): void
    {
        $this->router->addRouteMiddleware($middleware);
    }

    public function setErrorHandler(array $controller): void
    {
        $this->router->setErrorHandler($controller);
    }
}

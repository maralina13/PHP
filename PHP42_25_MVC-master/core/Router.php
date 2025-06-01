<?php

declare(strict_types=1);

namespace app\core;

use app\exceptions\RouteException;
use app\controllers\HomeController;
use app\controllers\AboutController;
use app\controllers\PresentationController;

class Router
{
    private Request $request;
    private Response $response;
    private array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->registerRoutes();

        error_log("Registered routes: " . print_r($this->routes, true));
    }

    private function registerRoutes(): void
    {
        $this->setGetRoute('/', [HomeController::class, 'getView']);
        $this->setGetRoute('/home', [HomeController::class, 'getView']);

        $this->setGetRoute('/about', [AboutController::class, 'getView']);

        $this->setGetRoute('/registration', [PresentationController::class, 'getView']);
        $this->setPostRoute('/handle', [PresentationController::class, 'handleView']);
    }

    public function setGetRoute(string $path, string|array $callback): void
    {
        $this->routes[MethodEnum::GET->value][$path] = $callback;
    }

    public function setPostRoute(string $path, string|array $callback): void
    {
        $this->routes[MethodEnum::POST->value][$path] = $callback;
    }

    public function resolve(): void
    {
        $path = rtrim($this->request->getUri(), '/'); // Удаляем trailing slash
        if ($path === '') $path = '/';
        $method = $this->request->getMethod();

        if ($method === MethodEnum::GET && preg_match("/(png|jpe?g|css|js)/", $path)) {
            $this->renderStatic(ltrim($path, "/"));
            return;
        }

        if (!isset($this->routes[$method->value][$path])) {
            $this->renderStatic("404.html");
            $this->response->setStatusCode(HttpStatusCodeEnum::HTTP_NOT_FOUND);
            return;
        }

        $callback = $this->routes[$method->value][$path];

        if (is_string($callback)) {
            $this->renderView($callback);
            return;
        }

        if (is_array($callback)) {
            call_user_func($callback, $this->request);
            return;
        }

        throw new RouteException("Invalid route callback");
    }

    public function renderView(string $name): void {
        include PROJECT_ROOT."views/$name.php";
    }

    public function renderStatic(string $name): void {
        include PROJECT_ROOT."web/$name";
    }
}
<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
  private array $routes = [];

  public function get(string $path, array $handler): void
  {
    $this->addRoute('GET', $path, $handler);
  }

  public function post(string $path, array $handler): void
  {
    $this->addRoute('POST', $path, $handler);
  }

  private function addRoute(string $method, string $path, array $handler): void
  {
    $this->routes[$method][$path] = $handler;
  }

  public function dispatch(string $method, string $uri): void
  {
    $parsedUri = parse_url($uri, PHP_URL_PATH);
    $parsedUri = rtrim($parsedUri, '/') ?: '/';

    if (isset($this->routes[$method][$parsedUri])) {
      [$controllerClass, $action] = $this->routes[$method][$parsedUri];

      if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        if (method_exists($controller, $action)) {
          $controller->$action();
          return;
        }
      }
    }

    http_response_code(404);
    echo "<h1>404 - Página Não Encontrada</h1>";
  }
}
<?php

namespace App\Core;

class Router
{
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function handleRequest(string $uri): void
    {
        // Очищаем URI
        $uri = '/' . trim(parse_url($uri, PHP_URL_PATH), '/');


        // Проверяем, существует ли маршрут
        if (array_key_exists($uri, $this->routes)) {
            [$controllerClass, $method] = $this->routes[$uri];

            // Проверка существования класса и метода
            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo "Controller class not found: $controllerClass";
                return;
            }

            if (!method_exists($controllerClass, $method)) {
                http_response_code(500);
                echo "Method $method not found in controller $controllerClass";
                return;
            }

            // Создаём объект контроллера и вызываем метод
            $controller = new $controllerClass();
            $controller->$method();
            return;
        }

        // Если маршрут не найден, возвращаем 404
        http_response_code(404);
        echo "Page not found.";
    }





}

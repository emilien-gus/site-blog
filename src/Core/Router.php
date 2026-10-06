<?php

namespace App\Core;

/**
 * Простой роутер.
 * Разбирает URL запроса и вызывает нужный контроллер с параметрами.
 */
class Router
{
    /**
     * Массив зарегистрированных маршрутов.
     * Каждый маршрут — это ['method' => 'GET', 'pattern' => '...', 'handler' => [Controller, method]]
     */
    private array $routes = [];

    /**
     * Зарегистрировать GET-маршрут.
     *
     * @param string $pattern  URI-паттерн, например '/category/{id}'
     * @param string $controller  Полное имя класса контроллера
     * @param string $method  Метод контроллера
     */
    public function get(string $pattern, string $controller, string $method): void
    {
        $this->routes[] = [
            'method' => 'GET',
            'pattern' => $pattern,
            'controller' => $controller,
            'action' => $method,
        ];
    }

    /**
     * Обработать текущий HTTP-запрос.
     * Ищет подходящий маршрут, извлекает параметры из URL и вызывает контроллер.
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();

        foreach ($this->routes as $route) {
            // Проверяем HTTP-метод
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Преобразуем паттерн маршрута в регулярное выражение
            // '/category/{id}' → '#^/category/(?P<id>[^/]+)$#'
            $regex = $this->patternToRegex($route['pattern']);

            if (preg_match($regex, $uri, $matches)) {
                // Извлекаем только именованные параметры (id, slug и т.д.)
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Создаём контроллер и вызываем метод
                $controller = new $route['controller']();
                call_user_func_array([$controller, $route['action']], $params);
                return;
            }
        }

        // Ни один маршрут не подошёл — 404
        $this->notFound();
    }

    /**
     * Извлечь чистый URI из запроса (без query string).
     */
    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Убираем query string (?sort=views&page=2)
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Убираем trailing slash (кроме корня)
        $uri = rtrim($uri, '/') ?: '/';

        return $uri;
    }

    /**
     * Преобразовать паттерн маршрута в регулярное выражение.
     * {id} → (?P<id>[^/]+)
     */
    private function patternToRegex(string $pattern): string
    {
        // Экранируем спецсимволы, кроме {}
        $regex = preg_replace_callback('/\{(\w+)\}/', function ($matches) {
            return '(?P<' . $matches[1] . '>[^/]+)';
        }, $pattern);

        return '#^' . $regex . '$#';
    }

    /**
     * Отобразить страницу 404.
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo '404 — Страница не найдена';
    }
}

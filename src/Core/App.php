<?php

namespace App\Core;

use App\Controllers\HomeController;
use App\Controllers\CategoryController;
use App\Controllers\ArticleController;

/**
 * Главный класс приложения.
 * Загружает конфигурацию, регистрирует маршруты и запускает обработку запроса.
 */
class App
{
    private Router $router;

    public function __construct()
    {
        $this->loadEnv();
        $this->compileAssets();
        $this->router = new Router();
        $this->registerRoutes();
    }

    /**
     * Автоматическая проверка и компиляция SCSS в CSS (при наличии изменений).
     */
    private function compileAssets(): void
    {
        (new CssCompiler())->compileIfChanged();
    }

    /**
     * Загрузить переменные окружения из файла .env в $_ENV.
     * Простая реализация без внешних библиотек (vlucas/dotenv).
     */
    private function loadEnv(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';

        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Пропускаем комментарии
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Разбиваем строку на ключ=значение
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Устанавливаем в $_ENV и putenv для совместимости
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }

    /**
     * Зарегистрировать все маршруты приложения.
     */
    private function registerRoutes(): void
    {
        // Главная страница
        $this->router->get('/', HomeController::class, 'index');

        // Страница категории
        $this->router->get('/category/{id}', CategoryController::class, 'show');

        // Страница статьи
        $this->router->get('/article/{id}', ArticleController::class, 'show');
    }

    /**
     * Запустить приложение — передать управление роутеру.
     */
    public function run(): void
    {
        $this->router->dispatch();
    }
}

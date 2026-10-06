<?php

/**
 * Единая точка входа (Front Controller).
 * Все HTTP-запросы направляются сюда через Nginx (try_files → index.php).
 */

// Подключаем автозагрузку Composer (PSR-4)
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

try {
    // Создаём и запускаем приложение
    $app = new App\Core\App();
    $app->run();
} catch (Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage() . "\n" . $e->getTraceAsString());
    echo '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>500 Ошибка сервера</title><style>body{font-family:sans-serif;padding:40px;text-align:center;color:#333}h1{color:#e11d48}</style></head><body><h1>500 — Внутренняя ошибка сервера</h1><p>Произошла непредвиденная ошибка. Пожалуйста, попробуйте позже.</p></body></html>';
}
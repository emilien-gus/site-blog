<?php

/**
 * Единая точка входа (Front Controller).
 * Все HTTP-запросы направляются сюда через Nginx (try_files → index.php).
 */

// Подключаем автозагрузку Composer (PSR-4)
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Создаём и запускаем приложение
$app = new App\Core\App();
$app->run();
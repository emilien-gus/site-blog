<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\CssCompiler;

echo "========================================\n";
echo "Компиляция SCSS в CSS (ScssPhp)...\n";
echo "========================================\n";

$compiler = new CssCompiler();
if ($compiler->compile(true)) {
    echo "Успешно! Скомпилировано в src/public/css/style.css\n";
    exit(0);
} else {
    echo "Ошибка компиляции SCSS.\n";
    exit(1);
}

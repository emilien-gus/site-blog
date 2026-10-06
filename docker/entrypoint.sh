#!/bin/sh
set -e

echo "==> [Docker Entrypoint] 1. Сборка SCSS стилей в CSS..."
php bin/compile-css.php || true

echo "==> [Docker Entrypoint] 2. Ожидание готовности базы данных MySQL..."
MAX_RETRIES=30
COUNT=0
until php -r "
    \$host = getenv('DB_HOST') ?: 'mysql';
    \$port = getenv('DB_PORT') ?: '3306';
    \$db   = getenv('DB_DATABASE') ?: 'blog_db';
    \$user = getenv('DB_USERNAME') ?: 'blog_user';
    \$pass = getenv('DB_PASSWORD') ?: 'secret';
    try {
        new PDO(\"mysql:host=\$host;port=\$port;dbname=\$db;charset=utf8mb4\", \$user, \$pass);
        exit(0);
    } catch (Throwable \$e) {
        exit(1);
    }
" 2>/dev/null; do
    COUNT=$((COUNT + 1))
    if [ $COUNT -ge $MAX_RETRIES ]; then
        echo "==> [Docker Entrypoint] Предупреждение: превышено время ожидания MySQL."
        break
    fi
    echo "==> MySQL ещё запускается, повтор через 2 сек ($COUNT/$MAX_RETRIES)..."
    sleep 2
done

echo "==> [Docker Entrypoint] 3. Проверка и автоматическое наполнение БД (сидер)..."
php -r "
    require 'vendor/autoload.php';
    new App\Core\App();
    try {
        \$pdo = App\Core\Database::getInstance()->getConnection();
        \$count = \$pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
        if ((int)\$count === 0) {
            echo 'База данных пуста. Запускаем сидер...' . PHP_EOL;
            (new App\Seeds\DatabaseSeeder())->run();
        } else {
            echo 'В базе данных уже есть записи (' . \$count . ' категорий). Автосидирование пропущено.' . PHP_EOL;
        }
    } catch (Throwable \$e) {
        echo 'Сидер: ' . \$e->getMessage() . PHP_EOL;
    }
" || true

echo "==> [Docker Entrypoint] 4. Запуск основного процесса: $@"
exec "$@"

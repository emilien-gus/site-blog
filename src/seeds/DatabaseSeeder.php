<?php

namespace App\Seeds;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\App;
use App\Core\Database;
use PDO;

/**
 * Сидер для первоначального наполнения базы данных реалистичными данными.
 * Запуск: php src/seeds/DatabaseSeeder.php
 */
class DatabaseSeeder
{
    private PDO $pdo;

    public function __construct()
    {
        // Инициализируем приложение для загрузки .env
        new App();
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Основной метод запуска сидера.
     */
    public function run(): void
    {
        echo "========================================\n";
        echo " Запуск сидера базы данных DevBlog...\n";
        echo "========================================\n\n";

        $this->cleanDatabase();
        $categories = $this->seedCategories();
        $this->seedArticles($categories);

        echo "\n========================================\n";
        echo " ✅ Сидирование базы данных успешно завершено!\n";
        echo "========================================\n";
    }

    /**
     * Очистка существующих таблиц с отключением внешних ключей.
     */
    private function cleanDatabase(): void
    {
        echo "1. Очистка существующих данных... ";
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->pdo->exec('TRUNCATE TABLE article_category');
        $this->pdo->exec('TRUNCATE TABLE articles');
        $this->pdo->exec('TRUNCATE TABLE categories');
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        echo "Готово.\n";
    }

    /**
     * Добавление тестовых категорий.
     *
     * @return array<int, int> Массив ID созданных категорий
     */
    private function seedCategories(): array
    {
        echo "2. Наполнение категорий... ";

        $categories = [
            1 => [
                'name' => 'PHP и Архитектура',
                'description' => 'Современный PHP 8.2+, паттерны проектирования, чистая архитектура и best practices.',
            ],
            2 => [
                'name' => 'Базы данных и MySQL',
                'description' => 'Проектирование реляционных схем, индексы, оптимизация сложных SQL-запросов и транзакции.',
            ],
            3 => [
                'name' => 'DevOps и Docker',
                'description' => 'Контейнеризация, оркестрация, настройка Nginx, CI/CD пайплайны и мониторинг серверов.',
            ],
            4 => [
                'name' => 'Frontend и Стили',
                'description' => 'Современный CSS и SCSS, семантическая верстка, UI/UX подходы и адаптивный дизайн.',
            ],
            5 => [
                'name' => 'Безопасность и Инструменты',
                'description' => 'Composer, Git, защита от SQL-инъекций и XSS, статический анализ кода и аутентификация.',
            ],
            6 => [
                'name' => 'Карьера и Soft Skills',
                'description' => 'Развитие разработчика, проведение код-ревью, командные процессы и тайм-менеджмент.',
            ],
        ];

        $stmt = $this->pdo->prepare('INSERT INTO categories (id, name, description) VALUES (:id, :name, :description)');

        foreach ($categories as $id => $cat) {
            $stmt->execute([
                'id' => $id,
                'name' => $cat['name'],
                'description' => $cat['description'],
            ]);
        }

        echo "Добавлено " . count($categories) . " категорий.\n";
        return array_keys($categories);
    }

    /**
     * Добавление статей с распределением по категориям, датам и просмотрам.
     *
     * @param array<int> $categoryIds
     */
    private function seedArticles(array $categoryIds): void
    {
        echo "3. Наполнение статей и связей с категориями...\n";

        $articles = [
            [
                'title' => 'Что нового в PHP 8.2: Readonly-классы, DNF типы и устаревшие функции',
                'description' => 'Подробный разбор ключевых нововведений PHP 8.2: как новые возможности языка повышают безопасность типов и чистоту архитектуры.',
                'content' => "Релиз PHP 8.2 принёс ряд важных возможностей для разработчиков:\n\n1. Readonly-классы: теперь можно объявить весь класс как readonly class User, и все его свойства автоматически станут неизменяемыми.\n\n2. Disjunctive Normal Form (DNF) типы: объединение пересечений и объединений типов, например (HasTitle&HasId)|null, что открывает гибкие возможности для типизации.\n\n3. Выделенные типы null, false и true как самостоятельные возвращаемые значения.\n\n4. Предотвращение утечки конфиденциальных параметров в стек-трейсах с помощью атрибута #[\\SensitiveParameter].\n\nВнедрение этих практик позволяет делать код более надёжным и предотвращать ошибки на этапе компиляции и статического анализа.",
                'image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format&fit=crop&q=80',
                'views' => 482,
                'days_ago' => 45,
                'categories' => [1, 5],
            ],
            [
                'title' => 'Оптимизация MySQL: Руководство по композитным индексам и EXPLAIN',
                'description' => 'Как читать план выполнения запросов EXPLAIN, проектировать покрывающие индексы и избегать блокировок таблиц.',
                'content' => "Правильное индексирование базы данных — залог высокой скорости работы любого веб-приложения.\n\nЧто нужно помнить при работе с индексами MySQL:\n\n- Правило левого префикса: композитный индекс (category_id, created_at) будет эффективно использоваться для фильтрации по category_id и последующей сортировки по created_at.\n- Покрывающие индексы (Covering Indexes): если все поля запроса присутствуют в индексе, MySQL не обращается к таблице данных, выполняя запрос из памяти.\n- Использование EXPLAIN FORMAT=TREE в MySQL 8.0 позволяет наглядно увидеть стоимость каждого шага выполнения.\n\nВсегда проверяйте запросы со сложными сортировками и объединениями перед деплоем в продакшн.",
                'image' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=800&auto=format&fit=crop&q=80',
                'views' => 891,
                'days_ago' => 60,
                'categories' => [2],
            ],
            [
                'title' => 'Развёртывание PHP-приложений в Docker: Nginx, PHP-FPM и оптимизация образов',
                'description' => 'Пошаговый обзор контейнеризации: многоэтапная сборка (multi-stage build), правильное разделение прав доступа и кэширование слоёв.',
                'content' => "Использование Docker для PHP позволяет получить одинаковое окружение на машинах разработчиков и на продакшн-сервере.\n\nКлючевые принципы эффективного Docker-окружения:\n- Разделение веб-сервера (Nginx) и обработчика PHP (php-fpm) по отдельным контейнерам.\n- Использование .dockerignore для исключения vendor, логов и локальных конфигураций из контекста сборки.\n- Закрепление версий базовых образов (например, php:8.2-fpm-alpine).\n- Монтирование конфигурационных файлов Nginx через volumes для удобства настройки без пересборки.",
                'image' => 'https://images.unsplash.com/photo-1607799279861-4dd421887fb3?w=800&auto=format&fit=crop&q=80',
                'views' => 1240,
                'days_ago' => 70,
                'categories' => [3, 1],
            ],
            [
                'title' => 'Шаблонизатор Smarty в 2026 году: Архитектура, кэширование и наследование шаблонов',
                'description' => 'Почему изоляция представления от бизнес-логики важна, и как возможности Smarty {extends} и {block} упрощают масштабирование UI.',
                'content' => "Шаблонизатор Smarty позволяет полностью разделить ответственности контроллера и вёрстки.\n\nОсновные преимущества подхода:\n- Наследование шаблонов: базовый layout.tpl определяет структуру страницы, а дочерние шаблоны переопределяют только именованные блоки {block name=\"content\"}.\n- Подключение переиспользуемых компонентов с помощью {include file=\"partials/article_card.tpl\"}.\n- Компиляция шаблонов в нативный PHP с кэшированием в var/smarty/compile, что гарантирует высокую производительность.",
                'image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80',
                'views' => 310,
                'days_ago' => 20,
                'categories' => [1, 4],
            ],
            [
                'title' => 'Паттерны проектирования на практике: Singleton, Factory и Repository в чистом PHP',
                'description' => 'Как создавать масштабируемые архитектурные решения без фреймворков: разбор реальных примеров Database Singleton и моделей.',
                'content' => "При разработке приложений на чистом PHP важно сохранять чистоту архитектуры.\n\nВ этом проекте мы применили паттерн Singleton для класса Database, гарантируя создание ровно одного подключения PDO на протяжении всего HTTP-запроса.\n\nМодели инкапсулируют логику взаимодействия с базой данных, предотвращая дублирование SQL-запросов и защищая от SQL-инъекций благодаря prepared statements.",
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format&fit=crop&q=80',
                'views' => 670,
                'days_ago' => 35,
                'categories' => [1],
            ],
            [
                'title' => 'Безопасность PHP-приложений: Защита от SQL Injection, XSS и CSRF атак',
                'description' => 'Чеклист по кибербезопасности веб-ресурсов: подготовленные запросы PDO, экранирование вывода и безопасные HTTP-заголовки.',
                'content' => "Безопасность должна быть заложена в архитектуру с первой строчки кода.\n\nГлавные правила:\n1. Никогда не склеивайте пользовательские переменные в строки SQL-запросов. Всегда используйте параметризованные запросы PDO::prepare().\n2. Экранируйте любой вывод данных в HTML через htmlspecialchars() или встроенные фильтры Smarty |escape.\n3. Устанавливайте строгие флаги cookie: HttpOnly, Secure, SameSite=Lax.\n4. Валидируйте входные данные по белому списку допустимых значений (например, разрешённые параметры сортировки).",
                'image' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=800&auto=format&fit=crop&q=80',
                'views' => 1050,
                'days_ago' => 80,
                'categories' => [5, 1],
            ],
            [
                'title' => 'Современный SCSS: Организация дизайн-токенов, переменных и архитектура 7-1',
                'description' => 'Структурирование стилей в проекте: разбиение на компоненты, использование CSS Custom Properties и компиляция через ScssPhp.',
                'content' => "SCSS даёт разработчикам мощные возможности для создания модульных стилей.\n\nВ нашем проекте компиляция стилей происходит на стороне PHP с помощью библиотеки ScssPhp, что избавляет от необходимости поднимать Node.js окружение.\n\nМы разделяем переменные (_variables.scss), базовую раскладку (_layout.scss), карточки (_cards.scss) и пагинацию (_pagination.scss) в отдельные партиалы, собирая их в едином main.scss.",
                'image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80',
                'views' => 245,
                'days_ago' => 15,
                'categories' => [4],
            ],
            [
                'title' => 'Проектирование связи Many-to-Many в реляционных базах данных',
                'description' => 'Реализация промежуточных связующих таблиц, внешние ключи с каскадным удалением и защита от дублирования связей.',
                'content' => "Связь многие-ко-многим часто встречается в блогах, каталогах и интернет-магазинах (статьи и категории, товары и теги).\n\nПравильное проектирование таблицы article_category:\n- Составной первичный ключ PRIMARY KEY (article_id, category_id) исключает дублирование записей.\n- Внешние ключи с ON DELETE CASCADE автоматически удаляют записи связей при удалении статьи или категории.\n- Использование пакетных запросов с WHERE article_id IN (...) устраняет проблему N+1 запросов при выводе списков.",
                'image' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=800&auto=format&fit=crop&q=80',
                'views' => 520,
                'days_ago' => 40,
                'categories' => [2, 1],
            ],
            [
                'title' => 'Автоматизация сборки и зависимостей с Composer: PSR-4 и оптимизация автозагрузки',
                'description' => 'Как устроен PSR-4 Autoloading, настройка composer.json и команда composer dump-autoload --optimize для продакшна.',
                'content' => "Composer является стандартом де-факто для управления зависимостями в PHP-экосистеме.\n\nВ файле composer.json мы настроили сопоставление пространства имён App\\ с каталогом src/.\n\nПри добавлении новых классов в директории Models, Controllers или Core они мгновенно становятся доступны приложению благодаря динамическому поиску PSR-4.",
                'image' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format&fit=crop&q=80',
                'views' => 415,
                'days_ago' => 28,
                'categories' => [5, 1],
            ],
            [
                'title' => 'Конфигурация Nginx для высоконагруженных веб-серверов: Gzip, кэш и FastCGI',
                'description' => 'Тонкая настройка Nginx: директивы fastcgi_buffer_size, gzip_comp_level и обработка статических файлов напрямую.',
                'content' => "Nginx работает как обратный прокси-сервер, принимающий HTTP-соединения и передающий динамические PHP-запросы пулу FastCGI.\n\nДля максимальной скорости:\n- Отдавайте CSS, JS и изображения напрямую через директивы location ~* \\.(css|js|jpg|png)$, минуя PHP.\n- Включайте Gzip-сжатие текстовых ответов.\n- Настраивайте front controller (try_files \$uri \$uri/ /index.php?\$query_string;) для чистых и понятных URL.",
                'image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80',
                'views' => 980,
                'days_ago' => 65,
                'categories' => [3],
            ],
            [
                'title' => 'Реализация алгоритма пагинации и сортировки данных на чистом PHP',
                'description' => 'Математика пагинации: вычисление offset и limit, расчёт общего числа страниц и сохранение GET-параметров в ссылках.',
                'content' => "Пагинация позволяет избежать загрузки десятков тысяч строк из базы данных в память PHP.\n\nФормула проста: \$offset = (\$page - 1) * \$perPage. Количество страниц: ceil(\$total / \$perPage).\n\nВажно передавать текущие параметры сортировки в ссылки пагинации, чтобы при переходе на следующую страницу сохранялся выбранный порядок элементов.",
                'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&auto=format&fit=crop&q=80',
                'views' => 610,
                'days_ago' => 22,
                'categories' => [1, 2],
            ],
            [
                'title' => 'Анатомия чистого MVC: Роутер, Контроллеры и Представления без лишних зависимостей',
                'description' => 'Как построить архитектуру веб-проекта с нуля: разбор взаимодействия Router, Request dispatching и View engine.',
                'content' => "Паттерн MVC разделяет приложение на три независимых слоя:\n- Модель отвечает за бизнес-логику и работу с БД.\n- Контроллер принимает запрос пользователя, запрашивает данные из модели и передаёт их в представление.\n- Представление (View) отвечает исключительно за HTML-разметку и презентацию.\n\nТакая структура обеспечивает высокую читаемость и лёгкость поддержки проекта.",
                'image' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&auto=format&fit=crop&q=80',
                'views' => 740,
                'days_ago' => 50,
                'categories' => [1],
            ],
            [
                'title' => 'Транзакции в MySQL и уровни изоляции: ACID на практических примерах',
                'description' => 'Защита от несогласованности данных: использование START TRANSACTION, COMMIT, ROLLBACK и выбор между READ COMMITTED и REPEATABLE READ.',
                'content' => "Транзакции гарантируют, что набор связанных операций будет выполнен целиком либо не выполнен вовсе.\n\nПринципы ACID:\n- Atomicity (Атомарность)\n- Consistency (Согласованность)\n- Isolation (Изолированность)\n- Durability (Долговечность)\n\nВ PHP с PDO транзакции запускаются методом \$pdo->beginTransaction() и завершаются \$pdo->commit() внутри блока try-catch.",
                'image' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=800&auto=format&fit=crop&q=80',
                'views' => 530,
                'days_ago' => 38,
                'categories' => [2],
            ],
            [
                'title' => 'Docker Compose для разработки: Healthcheck, монтирование томов и работа в сети',
                'description' => 'Настройка локального стека разработчика с помощью docker-compose.yaml: общие bridge-сети и автоматическая инициализация БД.',
                'content' => "Docker Compose упрощает запуск многоконтейнерных сервисов одной командой docker compose up -d.\n\nВ нашем проекте три сервиса:\n- nginx: веб-сервер, порт 8080\n- php: PHP 8.2-FPM с установленными модулями pdo_mysql\n- mysql: база данных с монтированием docker-entrypoint-initdb.d/init.sql для автоматического создания схемы.\n\nВсе контейнеры объединены в изолированную bridge-сеть app-network.",
                'image' => 'https://images.unsplash.com/photo-1607799279861-4dd421887fb3?w=800&auto=format&fit=crop&q=80',
                'views' => 890,
                'days_ago' => 55,
                'categories' => [3],
            ],
            [
                'title' => 'Адаптивная верстка с CSS Grid и Flexbox: Современные техники без фреймворков',
                'description' => 'Создание отзывчивых интерфейсов: grid-template-columns с minmax(), flexbox выравнивание и мобильные брейкпоинты.',
                'content' => "Современный CSS позволяет строить адаптивные макеты любой сложности без тяжелых библиотек.\n\nИспользование CSS Grid в нашем блоге обеспечивает автоматическое перестроение сетки карточек:\n- 3 колонки на больших экранах\n- 2 колонки на экранах планшетов\n- 1 колонка на мобильных устройствах\n\nКарточки сохраняют одинаковую высоту благодаря Flexbox и свойству display: flex.",
                'image' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80',
                'views' => 380,
                'days_ago' => 12,
                'categories' => [4],
            ],
            [
                'title' => 'Проведение эффективного Code Review: Советы для разработчиков и тимлидов',
                'description' => 'Культура рецензирования кода: как находить баги, не ущемляя коллег, и выстраивать продуктивный процесс разработки.',
                'content' => "Code Review — это не проверка на ошибки, а инструмент обмена знаниями и повышения качества кодовой базы.\n\nКлючевые советы:\n- Автоматизируйте рутину: линтеры и статический анализ должны отрабатывать до ревью человека.\n- Оценивайте архитектуру, безопасность и читаемость, а не личные вкусовые предпочтения.\n- Оставляйте аргументированные комментарии и хвалите за удачные решения.",
                'image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&auto=format&fit=crop&q=80',
                'views' => 760,
                'days_ago' => 48,
                'categories' => [6],
            ],
            [
                'title' => 'Секреты Git: Интерактивный rebase, чистая история коммитов и Conventional Commits',
                'description' => 'Как вести структурированную историю проекта: соглашение Conventional Commits, squash коммитов и работа с ветками.',
                'content' => "Понятная история коммитов в Git значительно упрощает аудит изменений и поиск регрессий.\n\nИспользование формата Conventional Commits (feat, fix, refactor, docs) позволяет быстро ориентироваться в истории изменений и автоматически генерировать changelog проекта.\n\nВ данном проекте каждый шаг разработки оформляется отдельным осмысленным коммитом.",
                'image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format&fit=crop&q=80',
                'views' => 630,
                'days_ago' => 30,
                'categories' => [5, 6],
            ],
            [
                'title' => 'Основы реляционной нормализации данных: 1NF, 2NF, 3NF простыми словами',
                'description' => 'Теория баз данных на живых примерах: устранение аномалий вставки, обновления и удаления при проектировании таблиц.',
                'content' => "Нормализация — метод проектирования базы данных, позволяющий устранить избыточность информации.\n\n- Первая нормальная форма (1NF): атомарность значений атрибутов.\n- Вторая нормальная форма (2NF): каждый неключевой столбец полностью зависит от первичного ключа.\n- Третья нормальная форма (3NF): отсутствие транзитивных зависимостей между неключевыми полями.\n\nСоблюдение этих правил обеспечивает целостность структуры проекта.",
                'image' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=800&auto=format&fit=crop&q=80',
                'views' => 495,
                'days_ago' => 25,
                'categories' => [2],
            ],
        ];

        $stmtArticle = $this->pdo->prepare('
            INSERT INTO articles (title, description, content, image, views, created_at)
            VALUES (:title, :description, :content, :image, :views, :created_at)
        ');

        $stmtLink = $this->pdo->prepare('
            INSERT INTO article_category (article_id, category_id)
            VALUES (:article_id, :category_id)
        ');

        $totalLinks = 0;

        foreach ($articles as $index => $item) {
            // Рассчитываем дату публикации в прошлом
            $createdAt = date('Y-m-d H:i:s', strtotime("-{$item['days_ago']} days -{$index} hours"));

            $stmtArticle->execute([
                'title' => $item['title'],
                'description' => $item['description'],
                'content' => $item['content'],
                'image' => $item['image'],
                'views' => $item['views'],
                'created_at' => $createdAt,
            ]);

            $articleId = (int) $this->pdo->lastInsertId();

            foreach ($item['categories'] as $catId) {
                if (in_array($catId, $categoryIds, true)) {
                    $stmtLink->execute([
                        'article_id' => $articleId,
                        'category_id' => $catId,
                    ]);
                    $totalLinks++;
                }
            }
        }

        echo "Добавлено " . count($articles) . " статей и {$totalLinks} связей в таблицу article_category.\n";
    }
}

// Запуск при прямом вызове из CLI
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $seeder = new DatabaseSeeder();
    $seeder->run();
}

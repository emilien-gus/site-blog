<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Класс для работы с базой данных.
 * Реализует паттерн Singleton — гарантирует одно подключение на весь запрос.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    /**
     * Приватный конструктор — подключение к БД через PDO.
     * Вызывается только внутри getInstance().
     */
    private function __construct()
    {
        $host = $_ENV['DB_HOST'] ?? 'mysql';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $database = $_ENV['DB_DATABASE'] ?? 'blog_db';
        $username = $_ENV['DB_USERNAME'] ?? 'blog_user';
        $password = $_ENV['DB_PASSWORD'] ?? 'secret';

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                // Выбрасывать исключения при ошибках SQL
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Возвращать результаты как ассоциативные массивы
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Использовать нативные prepared statements (защита от SQL-инъекций)
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Ошибка подключения к БД: " . $e->getMessage());
        }
    }

    /**
     * Получить единственный экземпляр Database.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Получить объект PDO для выполнения запросов.
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Запрещаем клонирование и десериализацию (Singleton)
    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception("Нельзя десериализовать Singleton");
    }
}

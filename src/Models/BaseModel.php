<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Базовый класс для всех моделей.
 * Предоставляет доступ к объекту PDO.
 */
abstract class BaseModel
{
    protected PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance()->getConnection();
    }
}

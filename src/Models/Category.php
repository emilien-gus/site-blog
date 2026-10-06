<?php

namespace App\Models;

use PDO;

/**
 * Модель для работы с категориями блога.
 */
class Category extends BaseModel
{
    /**
     * Получить все категории, отсортированные по алфавиту.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categories ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    /**
     * Получить категорию по её ID.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Получить категории, в которых есть хотя бы одна статья.
     * Используется для главной страницы (согласно ТЗ).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCategoriesWithArticles(): array
    {
        $sql = '
            SELECT DISTINCT c.*
            FROM categories c
            INNER JOIN article_category ac ON c.id = ac.category_id
            ORDER BY c.name ASC
        ';

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Получить все категории, к которым привязана указанная статья.
     *
     * @param int $articleId
     * @return array<int, array<string, mixed>>
     */
    public function getByArticleId(int $articleId): array
    {
        $sql = '
            SELECT c.*
            FROM categories c
            INNER JOIN article_category ac ON c.id = ac.category_id
            WHERE ac.article_id = :article_id
            ORDER BY c.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['article_id' => $articleId]);

        return $stmt->fetchAll();
    }
}

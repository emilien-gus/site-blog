<?php

namespace App\Models;

use PDO;

/**
 * Модель для работы со статьями блога.
 */
class Article extends BaseModel
{
    /**
     * Разрешённые варианты сортировки статей.
     */
    private const SORT_MAP = [
        'date_desc'  => 'a.created_at DESC',
        'date_asc'   => 'a.created_at ASC',
        'views_desc' => 'a.views DESC, a.created_at DESC',
        'views_asc'  => 'a.views ASC, a.created_at DESC',
    ];

    /**
     * Получить одну статью по её ID.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM articles WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $article = $stmt->fetch();

        return $article ?: null;
    }

    /**
     * Увеличить счётчик просмотров статьи на 1.
     *
     * @param int $id
     * @return void
     */
    public function incrementViews(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE articles SET views = views + 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Получить последние N статей заданной категории.
     * Используется на главной странице для вывода превью по категориям.
     *
     * @param int $categoryId
     * @param int $limit
     * @return array<int, array<string, mixed>>
     */
    public function getLatestByCategory(int $categoryId, int $limit = 3): array
    {
        $sql = '
            SELECT a.*
            FROM articles a
            INNER JOIN article_category ac ON a.id = ac.article_id
            WHERE ac.category_id = :category_id
            ORDER BY a.created_at DESC
            LIMIT :limit
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Получить статьи категории с поддержкой сортировки и пагинации.
     *
     * @param int $categoryId
     * @param string $sort ('date_desc' | 'date_asc' | 'views_desc' | 'views_asc')
     * @param int $page Номер страницы (начиная с 1)
     * @param int $perPage Количество статей на странице
     * @return array<int, array<string, mixed>>
     */
    public function getByCategoryId(int $categoryId, string $sort = 'date_desc', int $page = 1, int $perPage = 6): array
    {
        $orderBy = self::SORT_MAP[$sort] ?? self::SORT_MAP['date_desc'];
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT a.*
            FROM articles a
            INNER JOIN article_category ac ON a.id = ac.article_id
            WHERE ac.category_id = :category_id
            ORDER BY {$orderBy}
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Получить общее количество статей в категории (для расчёта пагинации).
     *
     * @param int $categoryId
     * @return int
     */
    public function countByCategory(int $categoryId): int
    {
        $sql = '
            SELECT COUNT(a.id)
            FROM articles a
            INNER JOIN article_category ac ON a.id = ac.article_id
            WHERE ac.category_id = :category_id
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * Получить похожие статьи (до N штук из тех же категорий, исключая текущую статью).
     *
     * @param int $articleId
     * @param int $limit
     * @return array<int, array<string, mixed>>
     */
    public function getSimilar(int $articleId, int $limit = 3): array
    {
        $sql = '
            SELECT DISTINCT a.*
            FROM articles a
            INNER JOIN article_category ac ON a.id = ac.article_id
            WHERE ac.category_id IN (
                SELECT category_id FROM article_category WHERE article_id = :article_id_sub
            )
            AND a.id != :article_id_self
            ORDER BY a.created_at DESC
            LIMIT :limit
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':article_id_sub', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':article_id_self', $articleId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Получить категории конкретной статьи.
     *
     * @param int $articleId
     * @return array<int, array<string, mixed>>
     */
    public function getCategories(int $articleId): array
    {
        $sql = '
            SELECT c.*
            FROM categories c
            INNER JOIN article_category ac ON c.id = ac.category_id
            WHERE ac.article_id = :article_id
            ORDER BY c.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':article_id', $articleId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Пакетная привязка категорий к массиву статей (для избежания проблемы N+1).
     * Добавляет ключ ['categories'] к каждой статье.
     *
     * @param array<int, array<string, mixed>> $articles
     * @return void
     */
    public function attachCategories(array &$articles): void
    {
        if (empty($articles)) {
            return;
        }

        $articleIds = array_column($articles, 'id');
        $placeholders = implode(',', array_fill(0, count($articleIds), '?'));

        $sql = "
            SELECT ac.article_id, c.*
            FROM categories c
            INNER JOIN article_category ac ON c.id = ac.category_id
            WHERE ac.article_id IN ({$placeholders})
            ORDER BY c.name ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($articleIds);
        $rows = $stmt->fetchAll();

        $grouped = [];
        foreach ($rows as $row) {
            $articleId = $row['article_id'];
            unset($row['article_id']);
            $grouped[$articleId][] = $row;
        }

        foreach ($articles as &$article) {
            $article['categories'] = $grouped[$article['id']] ?? [];
        }
        unset($article);
    }
}

<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;
use App\Models\Category;

/**
 * Контроллер страницы категории.
 */
class CategoryController
{
    private Category $categoryModel;
    private Article $articleModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->articleModel = new Article();
    }

    /**
     * Страница категории:
     * - Название и описание категории.
     * - Список статей: изображение, название, описание, дата добавления, количество просмотров.
     * - Сортировка статей: по дате добавления (новые/старые), по просмотрам.
     * - Пагинация.
     *
     * @param string $id ID категории из URL
     */
    public function show(string $id): void
    {
        $categoryId = (int) $id;

        // Получаем информацию о категории
        $category = $this->categoryModel->getById($categoryId);
        if (!$category) {
            http_response_code(404);
            $view = new View();
            $view->assign('title', 'Категория не найдена — Блог');
            $view->assign('message', 'Запрашиваемая категория не найдена или была удалена.');
            $view->render('404.tpl');
            return;
        }

        // Обработка параметра сортировки
        $allowedSorts = ['date_desc', 'date_asc', 'views_desc', 'views_asc'];
        $sort = $_GET['sort'] ?? 'date_desc';
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'date_desc';
        }

        // Параметры пагинации
        $perPage = 6;
        $totalArticles = $this->articleModel->countByCategory($categoryId);
        $totalPages = (int) ceil($totalArticles / $perPage);

        $page = (int) ($_GET['page'] ?? 1);
        if ($page < 1) {
            $page = 1;
        }

        // Получаем статьи текущей страницы с выбранной сортировкой
        $articles = $this->articleModel->getByCategoryId($categoryId, $sort, $page, $perPage);
        $this->articleModel->attachCategories($articles);

        // Передаём данные в шаблон
        $view = new View();
        $view->assign('title', $category['name'] . ' — Блог');
        $view->assign('category', $category);
        $view->assign('articles', $articles);
        $view->assign('totalArticles', $totalArticles);
        $view->assign('currentSort', $sort);
        $view->assign('currentPage', $page);
        $view->assign('totalPages', $totalPages);

        $view->render('category.tpl');
    }
}

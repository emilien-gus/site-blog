<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;
use App\Models\Category;

/**
 * Контроллер главной страницы.
 */
class HomeController
{
    private Category $categoryModel;
    private Article $articleModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->articleModel = new Article();
    }

    /**
     * Главная страница:
     * - Вывод категорий, в которых есть хотя бы одна статья.
     * - У каждой категории выводятся последние 3 статьи.
     * - Ссылка «Все статьи категории» на страницу категории.
     */
    public function index(): void
    {
        $categories = $this->categoryModel->getCategoriesWithArticles();

        foreach ($categories as &$category) {
            $category['articles'] = $this->articleModel->getLatestByCategory((int) $category['id'], 3);
            $this->articleModel->attachCategories($category['articles']);
        }
        unset($category);

        $view = new View();
        $view->assign('title', 'Главная — Блог');
        $view->assign('categories', $categories);
        $view->render('home.tpl');
    }
}

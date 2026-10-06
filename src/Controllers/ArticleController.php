<?php

namespace App\Controllers;

use App\Core\View;
use App\Models\Article;
use App\Models\Category;

/**
 * Контроллер страницы статьи.
 */
class ArticleController
{
    private Article $articleModel;
    private Category $categoryModel;

    public function __construct()
    {
        $this->articleModel = new Article();
        $this->categoryModel = new Category();
    }

    /**
     * Страница статьи:
     * - Полная информация: изображение, название, дата добавления, просмотры, текст, категории.
     * - Блок «Похожие статьи»: до 3 статей из тех же категорий.
     * - При каждом просмотре счётчик увеличивается на 1.
     *
     * @param string $id ID статьи из URL
     */
    public function show(string $id): void
    {
        $articleId = (int) $id;

        $article = $this->articleModel->getById($articleId);
        if (!$article) {
            http_response_code(404);
            $view = new View();
            $view->assign('title', 'Статья не найдена — Блог');
            $view->assign('message', 'Запрашиваемая статья не найдена или была удалена.');
            $view->render('404.tpl');
            return;
        }

        // Увеличиваем счётчик просмотров
        $this->articleModel->incrementViews($articleId);
        $article['views'] = ((int) $article['views']) + 1;

        // Получаем категории текущей статьи
        $article['categories'] = $this->categoryModel->getByArticleId($articleId);

        // Получаем похожие статьи (из тех же категорий, кроме текущей)
        $similarArticles = $this->articleModel->getSimilar($articleId, 3);
        $this->articleModel->attachCategories($similarArticles);

        $view = new View();
        $view->assign('title', $article['title'] . ' — Блог');
        $view->assign('article', $article);
        $view->assign('similarArticles', $similarArticles);

        $view->render('article.tpl');
    }
}

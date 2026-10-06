<?php

namespace App\Controllers;

use App\Core\View;

/**
 * Контроллер страницы статьи.
 */
class ArticleController
{
    /**
     * Страница статьи — полная информация + похожие статьи.
     */
    public function show(string $id): void
    {
        $view = new View();
        $view->assign('title', 'Статья — Блог');
        $view->assign('articleId', (int) $id);
        $view->render('article.tpl');
    }
}

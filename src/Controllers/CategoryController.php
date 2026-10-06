<?php

namespace App\Controllers;

use App\Core\View;

/**
 * Контроллер страницы категории.
 */
class CategoryController
{
    /**
     * Страница категории — список статей с сортировкой и пагинацией.
     */
    public function show(string $id): void
    {
        $view = new View();
        $view->assign('title', 'Категория — Блог');
        $view->assign('categoryId', (int) $id);
        $view->render('category.tpl');
    }
}

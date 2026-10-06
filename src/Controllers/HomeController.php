<?php

namespace App\Controllers;

use App\Core\View;

/**
 * Контроллер главной страницы.
 */
class HomeController
{
    /**
     * Главная страница — категории с последними статьями.
     */
    public function index(): void
    {
        $view = new View();
        $view->assign('title', 'Главная — Блог');
        $view->render('home.tpl');
    }
}

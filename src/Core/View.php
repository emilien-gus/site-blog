<?php

namespace App\Core;

use Smarty\Smarty;

/**
 * Обёртка над шаблонизатором Smarty.
 * Настраивает пути к шаблонам, компиляции и кэшу.
 */
class View
{
    private Smarty $smarty;

    public function __construct()
    {
        $this->smarty = new Smarty();

        // Базовый путь проекта (корень site-blog/)
        $basePath = dirname(__DIR__, 2);

        // Директория с шаблонами .tpl
        $this->smarty->setTemplateDir($basePath . '/src/templates');

        // Директория для скомпилированных шаблонов (Smarty компилирует .tpl в PHP)
        $this->smarty->setCompileDir($basePath . '/var/smarty/compile');

        // Директория для кэша
        $this->smarty->setCacheDir($basePath . '/var/smarty/cache');
    }

    /**
     * Передать переменную в шаблон.
     *
     * @param string $name  Имя переменной в шаблоне (доступна как {$name})
     * @param mixed $value  Значение
     */
    public function assign(string $name, mixed $value): self
    {
        $this->smarty->assign($name, $value);
        return $this;
    }

    /**
     * Отрисовать шаблон и вывести результат.
     *
     * @param string $template  Имя файла шаблона (например, 'home.tpl')
     */
    public function render(string $template): void
    {
        $this->smarty->display($template);
    }

    /**
     * Получить объект Smarty напрямую (если нужны расширенные возможности).
     */
    public function getSmarty(): Smarty
    {
        return $this->smarty;
    }
}

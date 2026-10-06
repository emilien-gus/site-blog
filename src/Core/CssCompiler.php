<?php

namespace App\Core;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

/**
 * Сервис компиляции SCSS в CSS с помощью библиотеки ScssPhp.
 */
class CssCompiler
{
    private string $scssDir;
    private string $entryFile;
    private string $outputFile;

    public function __construct()
    {
        $baseDir = dirname(__DIR__, 2);
        $this->scssDir = $baseDir . '/src/scss';
        $this->entryFile = $this->scssDir . '/main.scss';
        $this->outputFile = $baseDir . '/src/public/css/style.css';
    }

    /**
     * Скомпилировать SCSS в CSS.
     *
     * @param bool $minify Минифицировать ли результат
     * @return bool
     */
    public function compile(bool $minify = true): bool
    {
        if (!file_exists($this->entryFile)) {
            return false;
        }

        $compiler = new Compiler();
        $compiler->setOutputStyle($minify ? OutputStyle::COMPRESSED : OutputStyle::EXPANDED);
        $compiler->setImportPaths([$this->scssDir]);

        $result = $compiler->compileFile($this->entryFile);
        $css = $result->getCss();

        $outputDir = dirname($this->outputFile);
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        return file_put_contents($this->outputFile, $css) !== false;
    }

    /**
     * Автоматическая перекомпиляция, если исходные .scss файлы были изменены.
     */
    public function compileIfChanged(): void
    {
        if (!file_exists($this->outputFile)) {
            $this->compile();
            return;
        }

        $cssMtime = filemtime($this->outputFile);
        $scssFiles = glob($this->scssDir . '/*.scss');

        if ($scssFiles) {
            foreach ($scssFiles as $file) {
                if (filemtime($file) > $cssMtime) {
                    $this->compile();
                    return;
                }
            }
        }
    }
}

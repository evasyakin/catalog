<?php
namespace Core\Router;

use Core\App;

if (!defined('VIEWS_DIR')) {
    define('VIEWS_DIR', 'views/');
}

class Controller
{
    /** @var string директория отображений */
    protected $viewsDir = VIEWS_DIR;

    public function setViewsDir(string $viewsDir)
    {
        $this->viewsDir = $viewsDir;
        return $this;
    }

    protected function preparePath(string $filename): string
    {
        return App::resolvePath($this->viewsDir . $filename);
    }

    public function view(string $filename, array $props = null)
    {
        if (!$this->canView($filename)) return;
        $filename = $this->preparePath($filename);
        if ($props) extract($props);
        include $filename;
    }

    public function canView(string $filename): bool
    {
        return is_file($this->preparePath($filename));
    }

    public function throwIfNotCanView(string $filename, array $props = null)
    {
        if (!$this->canView($filename)) {
            $filename = $this->preparePath($filename);
            throw new \RuntimeException("View file \"$filename\" does not exists");
        }
        return $this->view($filename, $props);
    }
}

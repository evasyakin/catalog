<?php
/**
 * Simple autoloader.
 */
namespace core;

include_once __DIR__ . '/PhpHelp.php';

use Core\PhpHelp;

class Loader
{
    /** @var string базовая директория автозагрузки */
    protected $baseDir;
    /** @var array директории поиска фалов для автозагрузки */
    protected $dirs = [];

    public function __construct(string $baseDir = null)
    {
        $this->baseDir = $baseDir ?? (defined('APP_DIR') ? APP_DIR : static::getRunDir());
    }

    /**
     * Получение директории запуска.
     * @return string директория запуска
     */
    public static function getRunDir(): string
    {
        static $dir = null;
        if (null === $dir) {
            $filename = $_SERVER['SCRIPT_FILENAME'];
            if (empty($filename)) {
                $filename = 'cli' === PHP_SAPI ? $_SERVER['PHP_SELF']
                    : ($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
            }
            $dir = PhpHelp::pathReal(dirname($filename));
        }
        return $dir;
    }

    /**
     * Установка директории автозагрузки.
     * @param strin директория
     * @return self
     */
    public function dir(string $dir)
    {
        $this->dirs[] = $dir;
        return $this;
    }

    /**
     * Автозагрузка.
     * @param string имя класса
     */
    public function autoload(string $className)
    {
        foreach ($this->dirs as $dir) {
            $filename = APP_DIR . $dir . str_replace('\\', '/', $className) . '.php';
            // debug($filename, 'Loader');
            if (is_file($filename)) {
                return include $filename;
            }
        }
    }

    /**
     * Запуск автозагрузки.
     * @return self
     */
    public function run()
    {
        spl_autoload_register([$this, 'autoload']);
        return $this;
    }
}

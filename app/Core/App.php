<?php
/**
 * Класс приложения.
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Core;

use Core\Db\Database;
use Core\Http\Request;
use Core\Loader;

if (!defined('DB_CONFIG_PATH')) {
    define('DB_CONFIG_PATH', null);
}

class App
{
    /** @var базовая директория приложения */
    protected static $dir;
    /** @var базовый url приложения */
    protected static $url;
    /** @var Request */
    protected static $request;
    /** @var Database */
    protected static $db;

    /**
     * Установка базовой директории приложения.
     * @param string директория
     */
    public static function setDir(string $dir)
    {
        static::$dir = PhpHelp::pathReal($dir);
        static::$url = null;
    }

    /**
     * Получение базовой директории приложения.
     * @return string
     */
    public static function dir(): string
    {
        if (!static::$dir) {
            static::setDir(defined('APP_DIR') ? APP_DIR : Loader::getRunDir());
        }
        return static::$dir;
    }

    /**
     * Реализация пути относительно приложения.
     * @param string путь
     * @return string исходный путь или путь относительно приложения
     */
    public static function resolvePath(string $path)
    {
        // $path = PhpHelp::pathReal($path);
        $filename = App::dir() . $path;
        return is_readable($filename) ? $filename : $path;
    }

    /**
     * Получение базового url приложения.
     * @return string
     */
    public static function url(): string
    {
        if (!static::$url) {
            static::$url = urldecode(static::calcPath());
        }
        return static::$url;
    }

    /**
     * Вычисление пути uri приложения относительно директории запуска и document_root.
     * @param string|null директория запуска
     * @param string|null директория корня сервера
     * @return string
     */
    protected static function calcPath(string $runDir = null, string $documentRoot = null): string
    {
        if (!$runDir) $runDir = Loader::getRunDir();
        if (!$documentRoot) $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $runDir = str_replace('\\', '/', $runDir);
        return substr(str_replace($documentRoot, '', $runDir), 0, -1);
    }

    /**
     * Получение запроса.
     * @return Request
     */
    public static function request(): Request
    {
        if (!static::$request) {
            static::$request = Request::createFromGlobals();
        }
        return static::$request;
    }

    /**
     * Получение соединения с БД.
     * @return Database
     */
    public static function db(): Database
    {
        if (!static::$db) {
            $config = include DB_CONFIG_PATH ?? static::resolvePath('config/db.php');
            static::$db = new Database($config);
        }
        return static::$db;
    }
}

<?php
namespace Core;

class PhpHelp
{
    /**
     * Преобразование пути в совместимый с ОС путь.
     * @param string путь
     * @return string путь совместимый с ОС
     */
    public static function path(string $path): string {
        $path = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
        if (is_dir($path) && DIRECTORY_SEPARATOR !== $path[mb_strlen($path) - 1]) {
            $path .= DIRECTORY_SEPARATOR;
        }
        return $path;
    }

    /**
     * Преобразование пути в совместимый с ОС реальный путь.
     * @param string путь
     * @return string реальный путь совместимый с ОС
     */
    public static function pathReal(string $path): string {
        return static::path(realpath($path));
    }
}

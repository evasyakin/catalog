<?php
/**
 * Класс обработки ENV свойств.
 * @package evas-php\evas-base
 * @link https://github.com/evas-php/evas-base/blob/main/src/Env.php
 * @author Egor Vasyakin <egor@evas-php.com>
 */
namespace Core;

use Core\App;

if (!defined('ENV_PATH')) {
    define('ENV_PATH', '.env');
}

class Env
{
    /** @static mixed значение возвращаемое функцией getenv если ENV свойство не найдено */
    const PHP_GETENV_DEFAULT_RETURN = false;
    /** @static bool значение 2 аргумента вызова функции getenv */
    const PHP_GETENV_LOCAL_ONLY = true;

    /** @static array маппинг ENV свойств установленных через Evas\Base\Env */
    private static $setted = [];

    /** @static bool был ли запущен обработчик ENV */
    private static $launched = false;

    /**
     * Инициализация.
     * @param string|null путь к файлу с ENV свойствами
     */
    public static function init(string $filename = null)
    {
        if (!static::$launched) {
            static::$launched = true;
            if (!$filename) $filename = ENV_PATH;
            static::load($filename);
        }
    }

    /**
     * Загрузка ENV свойств из файла.
     * @param string путь к файлу с ENV свойствами
     */
    public static function load(string $filename)
    {
        $filename = App::resolvePath($filename);
        static::init();
        if (is_file($filename) && is_readable($filename)) {
            $parts = explode('.', $filename);
            $ext = array_pop($parts);
            $props = null;
            if ('env' === $ext) {
                $props = static::parseDotEnv($filename);
            } else if ('php' === $ext) {
                $props = include $filename;
            }
            // debug(compact('props', 'filename'), 'Env');
            if (is_array($props)) foreach ($props as $name => $value) {
                static::set($name, $value);
            }
        }
    }

    /**
     * @todo Дополнительно проверить по синтаксису .env в будущем
     * 
     * Парсинг свойств .env файла.
     * @param string имя файла
     * @return array ENV свойства
     */
    protected static function parseDotEnv(string $filename): array
    {
        $props = [];
        $fh = @fopen($filename, 'r');
        if (!$fh) return [];
        while (($line = fgets($fh, 4096)) !== false) {
            $lineParts = explode('#', $line);
            $line = array_shift($lineParts);
            $line = trim($line);
            if (empty($line)) continue;
            @list($name, $value) = explode('=', $line);
            $name = trim($name);
            $value = trim($value);
            if (empty($value)) continue;
            $value = trim($value, "'\"");
            $props[$name] = $value;
        }
        if (!feof($fh)) {
            throw new \RuntimeException(sprintf(
                'fgets() failed: parsing of the .env file "%s" has not been completed.',
                $filename
            ));
        }
        fclose($fh);
        return $props;
    }


    /**
     * Установка ENV свойства.
     * @param string имя
     * @param mixed значение
     */
    public static function set(string $name, $value)
    {
        static::init();
        $setting = "$name=$value";
        putenv($setting);
        $_ENV[$name] = $value;
        static::$setted[$name] = $value;
    }

    /**
     * Удаление ENV свойства/свойств.
     * @param string имя или иеня 
     */
    public static function unset(string ...$names)
    {
        static::init();
        foreach ($names as $name) {
            putenv($name);
            unset($_ENV[$name]);
            unset(static::$setted[$name]);
        }
    }

    /**
     * Удачение всех ENV свойств.
     */
    public static function unsetAll()
    {
        static::init();
        $names = array_keys(static::getAll());
        return static::unset(...$names);
    }

    /**
     * Проверка наличия ENV свойства/свойств.
     * @param string имя или имена свойств
     * @return bool
     */
    public static function has(string ...$names): bool
    {
        static::init();
        foreach ($names as $name) {
            if (static::PHP_GETENV_DEFAULT_RETURN === static::get($name)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Проверка наличия одного из ENV свойств.
     * @param string имя или имена свойств
     * @return bool
     */
    public static function hasOnce(string ...$names): bool
    {
        static::init();
        foreach ($names as $name) {
            if (static::PHP_GETENV_DEFAULT_RETURN !== static::get($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Получение значения ENV свойства.
     * @param string имя
     * @param mixed string альтернативное значение
     * @return mixed значение или null
     */
    public static function get(string $name, $default = null)
    {
        static::init();
        $value = getenv($name, static::PHP_GETENV_LOCAL_ONLY);
        if ($value === static::PHP_GETENV_DEFAULT_RETURN) {
            return $default;
        }
        return $value;
    }

    /**
     * Получение значения первого найденного ENV свойства.
     * @param string имя или имена свойств
     */
    public static function getOnce(string ...$names)
    {
        static::init();
        foreach ($names as $name) {
            $value = $this->get($name);
            if ($value !== static::PHP_GETENV_DEFAULT_RETURN) return $value;
        }
        return null;
    }

    /**
     * Получение значений нескольких ENV свойств.
     * @param string имя или имена свойств
     * @return array массив [свойство => значение]
     */
    public static function getSome(string ...$names)
    {
        static::init();
        $result = [];
        foreach ($names as $name) {
            $result[$name] = $this->get($name);
        }
        return $result;
    }

    /**
     * Получение всех ENV-свойств.
     * @return array
     */
    public static function getAll(): array
    {
        static::init();
        return getenv(null, static::PHP_GETENV_LOCAL_ONLY);
    }

    /**
     * Получение ENV свойств установленных через Evas\Base\Env.
     * @return array
     */
    public static function getSetted(): array
    {
        static::init();
        return static::$setted;
    }
}

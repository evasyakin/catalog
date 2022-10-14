<?php
namespace Core\Orm;

use Core\App;
use Core\Db\QueryBuilder;

class Model implements \JsonSerializable
{
    /** @var string первичный ключ */
    public static $primaryKey = 'id';

    /**
     * Получение имени таблицы.
     * @return string
     * @throws \RuntimeException
     */
    public static function tableName(): string
    {
        if (!isset(static::$tableName)) {
            throw new \RuntimeException('Model "' . __CLASS__ . '" not has static::$tableName');
        }
        return static::$tableName;
    }

    /**
     * Поиск одной или нескольких записей по первичному ключу.
     * @param mixed значение/значения первичного ключа
     * @return static|static[]
     */
    public static function find($id)
    {
        $sql = 'SELECT * FROM `'. static::tableName() .'` WHERE `'. static::$primaryKey .'` ';
        if (is_array($id)) {
            if (count($id) > 1) {
                $count = count($id);
                $sql .= "IN (" . implode(', ', array_fill(0, $count, '?')) .") LIMIT {$count}";
                return static::many($sql, $id);
            }
            @[$id] = array_values($id);
        }
        $sql .= "= ? LIMIT 1";
        return static::one($sql, [$id]);
    }


    /**
     * Поиск одной записи в результате запроса.
     * @param string sql-запрос
     * @param array|null ппараметры для экранирования
     * @return static|null
     */
    public static function one(string $sql, array $props = null): ?Model
    {
        return App::db()->query($sql, $props)->object(static::class);
    }


    /**
     * Получение массива записей в результате запроса.
     * @param string sql-запрос
     * @param array|null ппараметры для экранирования
     * @return static[]
     */
    public static function many(string $sql, array $props = null): array
    {
        return App::db()->query($sql, $props)->objectAll(static::class);
    }


    public static function build(\Closure $callback)
    {
        $callback($builder = static::buildQuery());
        $isOne = $builder->getLimit() === 1;
        @[$sql, $props] = $builder->getSqlAndBindings();
        return $isOne ? static::one($sql, $props) : static::many($sql, $props);
    }

    protected static function buildQuery()
    {
        return (new QueryBuilder(App::db()))->model(static::class)->from('`'. static::tableName() .'`');
    }

    public static function __callStatic(string $name, array $args = null)
    {
        return static::buildQuery()->$name(...$args);
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function __toString()
    {
        return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE);
    }
}

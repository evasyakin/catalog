<?php
/**
 * Рантайм исключение базы данных.
 */
namespace core\Db\Exceptions;

class DatabaseQueryException extends \RuntimeException
{
    /**
     * Выбрасывание исключения sql-запроса на основе errorInfo Бд.
     * @param array информация о падении запроса
     * @param string sql-запрос
     * @param array|null параметры sql-запроса для экранирования
     */
    public static function fromErrorInfo(array $errInfo, string $sql, array $props = null)
    {
        @list($sqlState, $code, $message) = $errInfo;
        $data = [
            'error' => compact('code', 'message', 'sqlState'),
            'query' => $sql,
            'props' => $props ?? [],
        ];
        return new static(json_encode($data, JSON_UNESCAPED_UNICODE), $code);
    }

    /**
     * Выбрасывание исключения sql-запроса на основе \PDOStatement.
     * @param \PDOStatement
     * @param string sql-запрос
     * @param array|null параметры sql-запроса для экранирования
     */
    public static function fromStmt(\PDOStatement $stmt, array $props = null)
    {
        return static::fromErrorInfo($stmt->errorInfo(), $stmt->queryString, $props);
    }

}

<?php
namespace core\Db;

class QueryResult
{
    /** @var \PDOStatement */
    protected $stmt;

    public function __construct(\PDOStatement &$stmt)
    {
        $this->stmt = &$stmt;
    }

    public function __destruct()
    {
        $this->stmt()->closeCursor();
    }

    // Мета-информация

    /**
     * Получение statement ответа базы.
     * @return \PDOStatement
     */
    public function stmt(): \PDOStatement
    {
        return $this->stmt;
    }

    /**
     * Получение количества возвращённых строк.
     * @return int
     */
    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }


    // Получение записи/записей в разном виде

    /**
     * Получение записи в виде ассоциативного массива.
     * @return assocArray|null
     */
    public function assocArray(): ?array
    {
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Получение всех записей в виде массива ассоциативных массивов.
     * @return array
     */
    public function assocArrayAll(): array
    {
        return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Получение записи в виде объекта.
     * @param string|null имя класса, если он должен отличаться от stdClass
     * @return stdClass|null
     */
    public function object(string $className = null): ?object
    {
        if (1 > $this->rowCount()) return null;
        if (empty($className)) {
            return $this->stmt->fetch(\PDO::FETCH_OBJ);
        }
        $this->stmt->setFetchMode(\PDO::FETCH_CLASS, $className);
        return $this->stmt->fetch();
    }

    /**
     * Получение всех записей в виде массива объектов.
     * @param string|null имя класса, если он должен отличаться от stdClass
     * @return array
     */
    public function objectAll(string $className = null): array
    {
        if (1 > $this->rowCount()) return [];
        if (empty($className)) {
            return $this->stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        $this->stmt->setFetchMode(\PDO::FETCH_CLASS, $className);
        return $this->stmt->fetchAll();
    }
}

<?php
namespace core\Db;

use core\Db\Exceptions\DatabaseConnectionException;
use core\Db\Exceptions\DatabaseQueryException;
use core\Db\QueryResult;

class Database
{
    // public $driver = 'mysql';
    public $host = 'localhost';
    public $username = 'root';
    public $password;
    public $dbname;

    public $charset = 'utf8';
    public $options = [];

     /** @var \PDO */
    protected $pdo;
    /** @var \PDOStatement последний запрос */
    protected $lastStmt;

    public function __construct(array $props)
    {
        foreach ($props as $name => $value) {
            $this->$name = $value;
        }
    }


    // Работа с соединением.

    /**
     * Открытие соединения.
     * @return self
     */
    public function open()
    {
        $dsn = "$this->driver:host=$this->host";
        if (!empty($this->dbname)) $dsn .= ";dbname=$this->dbname";
        if (!empty($this->charset)) $dsn .= ";charset=$this->charset";
        try {
            $this->pdo = new \PDO($dsn, $this->username, $this->password, $this->options);
        } catch (\PDOException $e) {
            throw new DatabaseConnectionException($e->getMessage());
        }
        return $this;
    }

    /**
     * Закрытие соединения.
     * @return self
     */
    public function close()
    {
        $this->pdo = null;
        return $this;
    }

    /**
     * Проверка открытости соединения.
     * @return bool
     */
    public function isOpen(): bool
    {
        return null !== $this->pdo ? true : false;
    }

    /**
     * Получение \PDO соедниения с БД.
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        if (!$this->isOpen()) $this->open();
        return $this->pdo;
    }


    // Работа с транзакциями


    // Результат запроса.

    /**
     * Получение объекта результата запроса.
     * @param \PDOStatement statement запроса
     * @return QueryResult
     */
    protected function getQueryResult(\PDOStatement &$stmt): QueryResult
    {
        $this->lastStmt = &$stmt;
        return new QueryResult($stmt, $this);
    }


    // Работа с запросами.

    /**
     * Закрытие курсора statement последнего запроса.
     * @return self
     */
    protected function closeCursor()
    {
        if (!empty($this->lastStmt)) $this->lastStmt->closeCursor();
        return $this;
    }

    /**
     * Получение подготовленного запроса.
     * @param string sql-запрос
     * @return \PDOStatement подготовленный запрос
     * @throws DatabaseQueryException
     */
    public function prepare(string $sql): \PDOStatement
    {
        try {
            $this->closeCursor();
            return $this->getPdo()->prepare($sql);
        } catch (\PDOException $e) {
            throw DatabaseQueryException::fromErrorInfo($this->errorInfo(), $sql);
        }
    }

    /**
     * Выполнение подготовленного запроса.
     * @param \PDOStatement подготовленный запрос
     * @param array|null экранируемые параметры запроса
     * @return QueryResult
     * @throws DatabaseQueryException
     */
    public function execute(\PDOStatement $stmt, array $props = null): QueryResult
    {
        try {
            $this->debugSql($stmt->queryString, $props);
            if (false === $stmt->execute($props)) {
                throw DatabaseQueryException::fromStmt($stmt, $props);
            }
        } catch (\PDOException $e) {
            throw DatabaseQueryException::fromStmt($stmt, $props);
        }
        return $this->getQueryResult($stmt);
    }

    /**
     * Выполнение запроса без подготовки.
     * @param string sql-запрос
     * @return QueryResult
     * @throws DatabaseQueryException
     */
    public function pdoQuery(string $sql): QueryResult
    {
        $this->debugSql($sql);
        try {
            $this->closeCursor();
            $stmt = $this->getPdo()->query($sql);
        } catch (\PDOException $e) {
            throw DatabaseQueryException::fromErrorInfo($this->errorInfo(), $sql);
        }
        return $this->getQueryResult($stmt);
    }

    /**
     * Выполнение запроса с автоподготовкой.
     * @param string sql-запрос
     * @param array|null экранируемые параметры запроса
     * @return QueryResult
     * @throws DatabaseQueryException
     */
    public function query(string $sql, array $props = null): QueryResult
    {
        return empty($props) 
        ? $this->pdoQuery($sql)
        : $this->execute($this->prepare($sql), $props);
    }

    public function arrayQuery(string $sql, array $props = null)
    {
        $result = $this->query($sql, $props);
        return [
            $result->assocArrayAll(),
            $result->rowCount(),
        ];
    }


    // Работа с ошибками

    /**
     * Получить расширенную информацию об ошибке последнего запроса.
     * @return array
     */
    public function errorInfo(): array
    {
        return $this->isOpen() ? $this->getPdo()->errorInfo() : [];
    }

    /**
     * Дебаг запроса.
     * @param string sql
     * @param array|null параметры запроса
     */
    public function debugSql(string $sql, array $props = null)
    {
        return debug([
            "query to `$this->host`:`$this->dbname`" => compact('sql', 'props')
        ], 'Database');
    }
}

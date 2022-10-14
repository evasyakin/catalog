<?php
namespace Core\Db;

class QueryBuilder
{
    protected $columns = '';
    protected $from = '';
    protected $join = '';
    protected $where = '';
    protected $groupBy = '';
    protected $having = '';
    protected $orderBy = '';
    protected $limit;
    protected $offset;

    public $bindings = [
        'columns' => [],
        'from' => [],
        'join' => [],
        'where' => [],
        'having' => [],
    ];

    protected $db;
    protected $model;


    public function __construct(Database $db)
    {
        $this->db = &$db;
    }


    // Вспомогательные методы для сборки запроса

    protected function addBindings(string $type, array $bindings = null)
    {
        if (!empty($bindings)) {
            $this->bindings[$type] = array_merge($this->bindings[$type], $bindings);
        }
        return $this;
    }

    protected function addPart(string $type, string $sql, array $props = null)
    {
        $this->$type .= $sql;
        return $this->addBindings($type, $props);
    }

    // Сборка запроса

    public function select(string $sql, array $props = null)
    {
        $this->columns = '';
        return $this->addPart('columns', $sql, $props);
    }

    public function addSelect(string $sql, array $props = null)
    {
        if (!empty($this->columns)) $this->columns .= ', ';
        return $this->addPart('columns', $sql, $props);
    }

    public function from(string $sql, array $props = null)
    {
        $this->from = '';
        return $this->addPart('from', $sql, $props);
    }

    public function join(string $sql, array $props = null)
    {
        if (!empty($this->join)) $this->join .= ' ';
        return $this->addPart('join', $sql, $props);
    }

    public function where(string $sql, array $props = null)
    {
        if (!empty($this->where)) $this->where .= ' AND ';
        return $this->addPart('where', $sql, $props);
    }

    public function orWhere(string $sql, array $props = null)
    {
        if (!empty($this->where)) $this->where .= ' OR ';
        return $this->addPart('where', $sql, $props);
    }

    public function whereIn(string $column, array $props, bool $isOr = false)
    {
        $quotes = implode(', ', array_fill(0, count($props), '?'));
        // $column = str_replace('`', '', $column);
        // $sql = "`{$column}` IN ({$quotes})";
        $sql = "{$column} IN ({$quotes})";
        return $isOr ? $this->orWhere($sql, $props) : $this->where($sql, $props);
    }

    public function orWhereIn(string $column, array $props)
    {
        return $this->whereIn($sql, $props, true);
    }

    public function groupBy(string $sql)
    {
        $this->groupBy = $sql;
        return $this;
    }

    public function having(string $sql, array $props = null)
    {
        if (!empty($this->having)) $this->having .= ' AND ';
        return $this->addPart('having', $sql, $props);
    }

    public function orHaving(string $sql, array $props = null)
    {
        if (!empty($this->having)) $this->having .= ' OR ';
        return $this->addPart('having', $sql, $props);
    }

    public function orderBy(string $sql)
    {
        $this->orderBy = $sql;
        return $this;
    }

    public function limit(int $limit, int $offset = 0)
    {
        if (!empty($limit)) $this->limit = $limit;
        if (!empty($offset)) $this->offset = $offset;
        return $this;
    }

    public function model(string $model)
    {
        $this->model = $model;
        return $this;
    }


    // Сборка запроса

    public function getSqlAndBindings(): array
    {
        if (mb_strlen($this->columns) < 1) $this->columns = '*';
        $sql = "SELECT {$this->columns} FROM {$this->from}";
        $bindings = array_merge($this->bindings['columns'], $this->bindings['from']);
        if (mb_strlen($this->join) > 0) {
            $sql .= ' ' . $this->join;
            $bindings = array_merge($bindings, $this->bindings['join']);
        }
        if (mb_strlen($this->where) > 0) {
            $sql .= ' WHERE ' . $this->where;
            $bindings = array_merge($bindings, $this->bindings['where']);
        }
        if (mb_strlen($this->groupBy) > 0) {
            $sql .= ' GROUP BY ' . $this->groupBy;
            if (mb_strlen($this->having) > 0) {
                $sql .= ' HAVING ' . $this->having;
                $bindings = array_merge($bindings, $this->bindings['having']);
            }
        }
        if (mb_strlen($this->orderBy)) $sql .= ' ORDER BY ' . $this->orderBy;
        if (!empty($this->limit)) $sql .= ' LIMIT ' . $this->limit;
        if (!empty($this->offset)) $sql .= ' OFFSET ' . $this->offset;
        // debug([$sql, $bindings], 'QueryBuilder');
        return [$sql, $bindings];
    }

    // Получние limit для one/many

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    // Получение рещультата.

    public function one()
    {
        @[$sql, $bindings] = $this->getSqlAndBindings();
        return $this->db->query($sql, $bindings)->object($this->model);
        // $this->model::one($sql, $bindings);
    }

    public function many()
    {
        @[$sql, $bindings] = $this->getSqlAndBindings();
        return $this->db->query($sql, $bindings)->objectAll($this->model);
        // $this->model::many($sql, $bindings);
    }

}

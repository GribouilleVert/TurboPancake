<?php
namespace TurboPancake\Database;

use TurboPancake\Database\Exception\QueryBuilderException;

class Query {

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string Mot clé SQL (ex: SELECT)
     */
    private $action = 'SELECT';

    /**
     * @var array Nom des tables d'ou proviennent les données
     */
    private $table;

    /**
     * @var array Colonne a selectionner lors de la requette
     */
    private $columns = [];

    /**
     * @var string
     */
    private $entity;

    /**
     * @var array Liste des conditions
     */
    private $where = [];

    /**
     * @var array Colonne sur laquelle GROUP BY doit etre apliqué
     */
    private $group = [];

    /**
     * @var string Directive ORDER BY
     */
    private $order;

    /**
     * @var string Limitation du nombre d'elements a retourner
     */
    private $limit;

    /**
     * @var array Paranmetre de requette préparée PDO
     */
    private $parameters;

    /**
     * Query constructor.
     * @param \PDO|null $pdo
     */
    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param string[] $columns
     * @return self
     */
    public function select(string ...$columns): self
    {
        $this->action = 'SELECT';
        $this->columns = array_merge($this->columns, $columns);
        return $this;
    }

    /**
     * Définit la table a utiliser
     * @param string $table
     * @param string|null $alias
     * @return self
     */
    public function table(string $table, ?string $alias = null): self
    {
        if ($alias) {
            $this->table[$alias] = $table;
        } else {
            $this->table = [$table];
        }
        return $this;
    }

    /**
     * @param string $class
     * @return Query
     */
    public function using(string $class): self
    {
        $this->entity = $class;
        return $this;
    }

    /**
     * @param string[] $condition
     * @return self
     */
    public function where(string ...$conditions): self
    {
        $this->where = array_merge($this->where, $conditions);
        return $this;
    }

    /**
     * @param string[] $cloumns
     * @return self
     */
    public function groupBy(string ...$cloumns): self
    {
        $this->group = array_merge($this->group, $cloumns);
        return $this;
    }

    /**
     * @param string[] $cloumns
     * @return self
     */
    public function orderBy(string ...$cloumns): self
    {
        $this->order = array_merge($this->group, $cloumns);
        return $this;
    }

    /**
     * @param int $start
     * @param int $size
     * @return self
     */
    public function limit(int $size, ?int $start = null): self
    {
        $start = is_null($start) ? '' : (string) $start . ', ';
        $this->limit =  $start . (string) $size;
        return $this;
    }

    /**
     * @param array $paramters
     * @return Query
     */
    public function parameters(array $paramters): self
    {
        $this->parameters = $paramters;
        return $this;
    }

    /**
     * @param string $column
     * @return int
     * @throws QueryBuilderException
     */
    public function count(string $column = 'id'): int
    {
        if (is_null($this->pdo)) {
            throw new QueryBuilderException(self::class . "::count() Can't be called when pdo hasn't been defined in the constructor");
        }
        $this->columns = ["count($column)"];
        return (int)$this->execute($this->parameters)->fetchColumn();
    }

    /**
     * @return \stdClass
     * @throws QueryBuilderException
     */
    public function fetch(): \stdClass
    {
        if (is_null($this->pdo)) {
            throw new QueryBuilderException(self::class . "::fetch() Can't be called when pdo hasn't been defined in the constructor");
        }

        if ($this->entity) {
            $datas = $this->execute($this->parameters)->fetch(\PDO::FETCH_ASSOC);
            return Sprinkler::hydrate($datas, $this->entity);
        }
        return $this->execute($this->parameters)->fetch();
    }

    /**
     * @return QueryResult
     * @throws QueryBuilderException
     */
    public function fetchAll(): QueryResult
    {
        if (is_null($this->pdo)) {
            throw new QueryBuilderException(self::class . "::fetch() Can't be called when pdo hasn't been defined in the constructor");
        }
        $mode = is_null($this->entity) ? \PDO::FETCH_OBJ : \PDO::FETCH_ASSOC;
        $records = $this->execute($this->parameters)->fetchAll($mode);
        return new QueryResult($records, $this->entity);
    }

    /**
     * @return void
     * @throws QueryBuilderException
     */
    public function run(): void
    {
        if (is_null($this->pdo)) {
            throw new QueryBuilderException(self::class . "::run() Can't be called when pdo hasn't been defined in the constructor");
        }
        $this->execute($this->parameters);
    }

    /**
     * Permet d'obtenir la requette SQL complete
     * @return string la requette SQL
     * @throws QueryBuilderException
     */
    public function __toString(): string
    {
        $parts = [$this->action];
        $parts[] = $this->buildSelect();

        $parts[] = 'FROM';
        $parts[] = $this->builFrom();

        if (!empty($this->where)) {
            $parts[] = 'WHERE';
            $parts[] = '(' . implode(') AND (', $this->where) . ')';
        }

        if (!empty($this->group)) {
            $parts[] = 'GROUP BY';
            $parts[] = implode(', ', $this->group);
        }

        if (!empty($this->order)) {
            $parts[] = 'ORDER BY';
            $parts[] = implode(', ', $this->order);
        }

        if (!empty($this->limit)) {
            $parts[] = 'LIMIT';
            $parts[] = $this->limit;
        }

        return implode(' ', $parts);
    }

    /**
     * @return string
     */
    private function buildSelect(): string
    {
        if (!empty($this->columns)) {
            return implode(', ', $this->columns);
        }
        return '*';
    }

    /**
     * @return string
     * @throws QueryBuilderException
     */
    private function builFrom(): string
    {
        if (is_null($this->table)) {
            throw new QueryBuilderException('The table has not been defined');
        }
        $tables = [];
        foreach ($this->table as $alias => $table) {
            if (is_string($alias)) {
                $tables[] = $table . ' as ' . $alias;
            } else {
                $tables[] = $table;
            }
        }
        return implode(', ', $tables);
    }

    /**
     * @param array|null $parameters
     * @return \PDOStatement
     */
    private function execute(?array $parameters = null): \PDOStatement
    {
        $query = (string)$this;
        if (!is_null($parameters)) {
            $statement = $this->pdo->prepare(($query));
            $statement->execute($parameters);
            return $statement;
        }
        return $this->pdo->query($query);
    }
}
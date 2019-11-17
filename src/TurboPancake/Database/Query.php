<?php
namespace TurboPancake\Database;

use Pagerfanta\Pagerfanta;
use TurboPancake\Database\Exceptions\NoRecordException;
use TurboPancake\Database\Exceptions\QueryBuilderException;

class Query implements \IteratorAggregate {

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
     * @var array Liste des jointures
     */
    private $joins = [];

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
    private $parameters = [];

    /**
     * Lancer une erreur en l'absence d'enregistrements
     * @var bool
     */
    protected $throwOnNotFound = false;

    public const INNER_JOIN = 0;
    public const CROSS_JOIN = 1;
    public const LEFT_JOIN = 10;
    public const RIGHT_JOIN = 11;
    public const FULL_JOIN = 20;
    public const SELF_JOIN = 21;
    public const NATURAL_JOIN = 22;

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
     * @param string $table
     * @param string $condition
     * @param int $mode
     * @return Query
     * @throws QueryBuilderException
     */
    public function join(string $table, string $condition, int $mode = self::LEFT_JOIN): self
    {
        switch ($mode) {
            case self::INNER_JOIN:
                $joinPrefix = 'INNER';
                break;
            case self::CROSS_JOIN:
                $joinPrefix = 'CROSS';
                break;
            case self::LEFT_JOIN:
                $joinPrefix = 'LEFT';
                break;
            case self::RIGHT_JOIN:
                $joinPrefix = 'RIGHT';
                break;
            case self::FULL_JOIN:
                $joinPrefix = 'FULL';
                break;
            case self::SELF_JOIN:
                $joinPrefix = 'SELF';
                break;
            case self::NATURAL_JOIN:
                $joinPrefix = 'NATURAL';
                break;
            default:
                throw new QueryBuilderException('Invalide join mode: ' . $mode);
        }
        $this->joins[] = "$joinPrefix JOIN $table ON $condition";

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
    public function order(string ...$cloumns): self
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
        $start = is_null($start) ? '' : (string)$start . ', ';
        $this->limit =  $start . (string)$size;
        return $this;
    }

    /**
     * @param array $paramters
     * @return Query
     */
    public function parameters(array $paramters): self
    {
        $this->parameters = array_merge($this->parameters, $paramters);
        return $this;
    }

    /**
     * @return void
     * @throws QueryBuilderException
     */
    public function run(): void
    {
        if (is_null($this->pdo)) {
            throw new QueryBuilderException(
                self::class . "::run() Can't be called when pdo hasn't been defined in the constructor"
            );
        }
        $this->execute($this->parameters);
    }

    /**
     * @param string $column
     * @return int
     * @throws QueryBuilderException
     */
    public function count(string $column = 'id'): int
    {
        if (is_null($this->pdo)) {
            throw new QueryBuilderException(
                self::class . "::count() Can't be called when pdo hasn't been defined in the constructor"
            );
        }

        $clone = clone $this;
        $table = is_int(key($this->table)) ? current($this->table) : key($this->table);
        $clone->columns = ["count($table.$column)"];
        $clone->joins = [];
        $clone->group = [];
        $clone->order = null;
        return (int)$clone->execute($this->parameters)->fetchColumn();
    }

    /**
     * @return \stdClass|object|mixed|null
     * @throws QueryBuilderException
     * @throws NoRecordException
     */
    public function fetch()
    {
        if (is_null($this->pdo)) {
            throw new QueryBuilderException(
                self::class . "::fetch() Can't be called when pdo hasn't been defined in the constructor"
            );
        }

        $datas = $this->execute($this->parameters)->fetch(\PDO::FETCH_ASSOC);
        if ($datas === false) {
            if ($this->throwOnNotFound) {
                throw new NoRecordException('No record was found');
            } else {
                return null;
            }
        }

        if ($this->entity) {
            return Sprinkler::hydrate($datas, $this->entity);
        }
        return Sprinkler::hydrate($datas, \stdClass::class);
    }

    /**
     * @return QueryResult
     * @throws QueryBuilderException
     * @throws NoRecordException
     */
    public function fetchAll(): QueryResult
    {
        if (is_null($this->pdo)) {
            throw new QueryBuilderException(
                self::class . "::fetch() Can't be called when pdo hasn't been defined in the constructor"
            );
        }
        $mode = is_null($this->entity) ? \PDO::FETCH_OBJ : \PDO::FETCH_ASSOC;
        $records = $this->execute($this->parameters)->fetchAll($mode);

        if (count($records) <= 0) {
            if ($this->throwOnNotFound) {
                throw new NoRecordException('No record was found');
            }
        }

        return new QueryResult($records, $this->entity);
    }

    /**
     * @param int $itemsPerPages
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function paginate(int $itemsPerPages, int $currentPage = 1): Pagerfanta
    {
        $pager = new PaginatedQuery($this);
        return (new Pagerfanta($pager))
            ->setMaxPerPage($itemsPerPages)
            ->setCurrentPage($currentPage);
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

        if (!empty($this->joins)) {
            $parts[] = implode(' ', $this->joins);
        }

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
     * @param bool $throwOnNotFound
     * @return self
     */
    public function setThrowOnNotFound(bool $throwOnNotFound): self
    {
        $this->throwOnNotFound = $throwOnNotFound;
        return $this;
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
        if (!is_null($parameters) and !empty($parameters)) {
            $statement = $this->pdo->prepare(($query));
            $statement->execute($parameters);
            return $statement;
        }
        return $this->pdo->query($query);
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @throws NoRecordException
     * @throws QueryBuilderException
     * @since 5.0.0
     */
    public function getIterator(): \Traversable
    {
        return $this->fetchAll();
    }
}

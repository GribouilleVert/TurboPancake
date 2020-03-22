<?php
namespace TurboPancake\Database;

use TurboPancake\Database\Exceptions\NoRecordException;
use TurboPancake\Database\Exceptions\QueryBuilderException;

abstract class Table {

    /**
     * @var null|\PDO
     */
    protected $pdo;

    /**
     * Nom de la table en bdd
     * @var string
     */
    protected $table;

    /**
     * Entitée à hydrater
     * @var string
     */
    protected $entity;

    /**
     * Lancer une erreur en l'absence d'enregistrements
     * @var bool
     */
    protected $throwOnNotFound = true;

    /**
     * Nom de la collonne primaire de la table
     * @var string
     */
    protected $customIdColumn = 'id';

    /**
     * Table constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Verifie l'existence d'un élément
     *
     * @param mixed $id
     * @return bool
     */
    public function exists($id): bool
    {
        $statement = $this->pdo->prepare(
            "SELECT `{$this->customIdColumn}` FROM {$this->table} WHERE `{$this->customIdColumn}` = ?"
        );
        $statement->execute([$id]);
        return $statement->fetchColumn() !== false;
    }

    /**
     * Permet de trouver un élément
     *
     * @param mixed $id ID de l'article
     * @return \stdClass|mixed|null Données de l'article, null si aucune article n'a été trouvé
     * @throws QueryBuilderException
     * @throws NoRecordException
     */
    public function find($id)
    {
        return ($this->makeQuery())
            ->where("`{$this->customIdColumn}` = :id")
            ->parameters(['id' => $id])
            ->fetch();
    }

    /**
     * Renvoie un tableau des elements sous la forme `id => name`
     * Info: sert surtout pour faire des select
     *
     * @param string $column Nom de la colonne a utiliser en tant que valeur
     * @return array
     */
    public function findList(string $column = 'name') :array
    {
        $results = $this->pdo
            ->query("SELECT `{$this->customIdColumn}`, {$column} FROM {$this->table} ORDER BY {$column} ASC")
            ->fetchAll(\PDO::FETCH_NUM);

        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }

        return $list;
    }

    /**
     * Renvoie un tableau d'objet contenant toutes les entrée de la table
     *
     * @return Query
     */
    public function findAll(): Query
    {
        $query = $this->makeQuery();
        $query->setThrowOnNotFound(false);
        return $query;
    }

    /**
     * Renvoie un tableau d'objet contenant toutes les entrée de la table
     *
     * @param string $column Colonne a comparer. WARNING: This argument is not securised,
     * remeber: NEVER trust user input.
     * @param mixed $toCompareValue Valeur a compoarer. INFO: This argument is passed to MySQL trough
     * prepared request argument, in consequence, it can be considered as secured.
     * @param string $operator Operateur de comparaison, peut être: =, !=, >, >=, < ou <=
     * @return QueryResult
     * @throws \Exception
     */
    public function findBy(string $column, $toCompareValue, string $operator = '='): QueryResult
    {
        if (!in_array($operator, ['=', '!=', '<', '<=', '>', '>='])) {
            throw new \Exception(
                "The comparison opartor $operator is not accepted by the function " . Table::class .
                "::findBy, please check the function's PHPDoc for a list of accepted comparaison oparators."
            );
        }
        return ($this->makeQuery())
            ->where("`$column` $operator ?")
            ->parameters([$toCompareValue])
            ->fetchAll();
    }

    /**
     * Crée un élément
     *
     * @param array $fields
     * @return bool
     */
    public function insert(array $fields): bool
    {
        $columns = array_keys($fields);
        $values = array_map(function ($field) {
            return ':' . $field;
        }, $columns);

        $columnsQuery = join(', ', $columns);
        $valuesQuery = join(', ', $values);

        return $this->execute("INSERT INTO {$this->table} ($columnsQuery) VALUES ($valuesQuery)", $fields);
    }

    /**
     * Met a jour un élément
     *
     * @param int $id
     * @param array $fields
     * @return bool
     */
    public function update(int $id, array $fields): bool
    {
        $fieldQuery = $this->createFieldQuery($fields);
        $fields['id'] = $id;

        return $this->execute("UPDATE {$this->table} SET $fieldQuery WHERE `{$this->customIdColumn}` = :id", $fields);
    }

    /**
     * Supprime un élément
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->execute("DELETE FROM {$this->table} WHERE `{$this->customIdColumn}` = ?", [$id]);
    }

    /**
     * Obtient le nombre d'élément dans la table
     *
     * @return int
     * @throws QueryBuilderException
     */
    public function count(): int
    {
        return $this->makeQuery()->count();
    }

    /**
     * @return \PDO
     */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param bool $throwOnNotFound
     */
    public function setThrowOnNotFound(bool $throwOnNotFound): void
    {
        $this->throwOnNotFound = $throwOnNotFound;
    }

    /**
     * @return Query
     */
    public function makeQuery(): Query
    {
        $query = (new Query($this->pdo))
            ->table($this->table, strtolower($this->table[0]))
            ->setThrowOnNotFound($this->throwOnNotFound);
        if ($this->entity) {
            $query->using($this->entity);
        }
        return $query;
    }

    /**
     * Permet de faire une requette d'execution
     * @param string $query
     * @param array $parameters
     * @return bool
     */
    protected function execute(string $query, array $parameters = []): bool
    {
        foreach ($parameters as $key => $parameter) {
            if (is_bool($parameter)) {
                $parameters[$key] = (int)$parameter;
            }
        }
        $statement = $this->pdo->prepare($query);
        return $statement->execute($parameters);
    }

    /**
     * Permet de faire une requette de donnée
     * @param string $query
     * @param array $parameters
     * @param bool $fetchAll
     * @return null|array|mixed
     * @throws NoRecordException
     */
    protected function fetch(string $query, array $parameters = [], bool $fetchAll = false)
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($parameters);

        if ($this->entity) {
            $statement->setFetchMode(\PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(\PDO::FETCH_OBJ);
        }

        if ($fetchAll) {
            //Return null only on failure
            $result = $statement->fetchAll() ?: null;
        } else {
            //Return null only on failure
            $result = $statement->fetch() ?: null;
        }
        if (is_null($result)) {
            if ($this->throwOnNotFound) {
                throw new NoRecordException('No record was found');
            }
            return $fetchAll?[]:null;
        }
        return $result;
    }

    /**
     * Permet de construire une chaine de requette préparé en fonction du tableau d'argment
     * Ex: ['a' => 'b'] donne "a = :a"
     * On fait ensuite un \PDOStatement::execute($field) sur la requette construite via cette fonction
     *
     * @param array $fields
     * @return string
     */
    private function createFieldQuery(array $fields): string
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($fields)));
    }
}
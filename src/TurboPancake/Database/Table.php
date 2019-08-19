<?php
namespace TurboPancake\Database;

use Pagerfanta\Pagerfanta;
use PDO;
use TurboPancake\Database\Exceptions\NoRecordException;

class Table {

    /**
     * @var PDO
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
    protected $throwOnNoteFound = true;

    /**
     * Table constructor.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
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
        $statemenet = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE id = ?");
        $statemenet->execute([$id]);
        return $statemenet->fetchColumn() !== false;
    }

    /**
     * Permet de trouver un élément
     *
     * @param int $id ID de l'article
     * @return \stdClass|mixed|null Données de l'article, null si aucune article n'a été trouvé
     * @throws NoRecordException
     */
    public function find(int $id)
    {
        return $this->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
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
            ->query("SELECT id, {$column} FROM {$this->table} ORDER BY {$column} ASC")
            ->fetchAll(PDO::FETCH_NUM);

        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }

        return $list;
    }

    /**
     * Renvoie un tableau d'objet contenant toutes les entrée de la table
     *
     * @return array
     * @throws NoRecordException
     */
    public function findAll() :array
    {
        return $this->fetch("SELECT * FROM {$this->table}", [], true);
    }

    /**
     * Renvoie un tableau d'objet contenant toutes les entrée de la table
     *
     * @param string $column Colonne a comparer. WARNING: This argument is not securised,
     * remeber: NEVER trust user input.
     * @param mixed $toCompareValue Valeur a compoarer. INFO: This argument is passed to MySQL trough
     * prepared request argument, in consequence, it can be considered as secured.
     * @param string $operator Operateur de comparaison, peut être: =, !=, >, >=, < ou <=
     * @return array|null
     * @throws \Exception
     */
    public function findBy(string $column, $toCompareValue, string $operator = '='): ?array
    {
        if (!in_array($operator, ['=', '!=', '<', '<=', '>', '>='])) {
            throw new \Exception(
                "The comparison opartor $operator is not accepted by the function " . Table::class .
                "::findBy, please check the function's PHPDoc for a list of accepted comparaison oparators."
            );
        }
        return $this->fetch("SELECT * FROM {$this->table} WHERE $column $operator ?", [$toCompareValue], true);
    }

    /**
     * Permet d'obtenir une pagination des éléments
     * @param int $maxPerPage
     * @param int $currentPage
     * @return Pagerfanta
     */
    public function findPaginated(int $maxPerPage, int $currentPage): ?Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            $this->getPaginationQuery(),
            $this->countQuery(),
            $this->entity
        );
        $pagerFanta =  (new Pagerfanta($query))
            ->setMaxPerPage($maxPerPage);

        if ($pagerFanta->getNbPages() < $currentPage) {
            return null;
        }

        $pagerFanta->setCurrentPage($currentPage);
        return $pagerFanta;
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

        return $this->execute("UPDATE {$this->table} SET $fieldQuery WHERE id = :id", $fields);
    }

    /**
     * Supprime un élément
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->execute("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }

    /**
     * Obtient le nombre d'élément dans la table
     *
     * @return int
     */
    public function count(): int
    {
        $statement = $this->pdo->query("SELECT count(id) FROM {$this->table}");
        return $statement->fetchColumn();
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

    public function setThrowOnNoteFound(bool $throwOnNoteFound)
    {
        $this->throwOnNoteFound = $throwOnNoteFound;
    }

    /**
     * Requète a executer pour obtenir tous les éléments de la table
     *
     * @return string
     */
    protected function getPaginationQuery()
    {
        return "SELECT * FROM {$this->table}";
    }

    /**
     * Requète a executer pour obtenir le nombre d'éléments dans la table
     *
     * @return string
     */
    protected function countQuery()
    {
        return "SELECT count(id) FROM {$this->table}";
    }

    /**
     * Permet de faire une requette d'execution
     * @param string $query
     * @param array $parameters
     * @return bool
     */
    protected function execute(string $query, array $parameters = [])
    {
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
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        } else {
            $statement->setFetchMode(PDO::FETCH_OBJ);
        }

        if ($fetchAll) {
            //Return null only on failure
            $result = $statement->fetchAll() ?: null;
        } else {
            //Return null only on failure
            $result = $statement->fetch() ?: null;
        }
        if (is_null($result)) {
            if ($this->throwOnNoteFound) {
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

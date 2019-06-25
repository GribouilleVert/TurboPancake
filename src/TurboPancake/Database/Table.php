<?php
namespace TurboPancake\Database;

use Pagerfanta\Pagerfanta;
use PDO;

class Table {

    /**
     * @var PDO
     */
    private $pdo;

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
     */
    public function find(int $id)
    {
        $statement = $this->pdo
            ->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $statement->execute([$id]);

        if ($this->entity) {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        }
        return $statement->fetch() ?: null;
    }

    /**
     * Renvoie un tableau des elements sous la forme `id => name`
     * Info: sert surtout pour faire des select
     *
     * @return array
     */
    public function findList() :array
    {
        $results = $this->pdo
            ->query("SELECT id, name FROM {$this->table} ORDER BY name ASC")
            ->fetchAll(PDO::FETCH_NUM);

        $list = [];
        foreach ($results as $result) {
            $list[$result[0]] = $result[1];
        }

        return $list;
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

        $statement = $this->pdo->prepare("INSERT INTO {$this->table} ($columnsQuery) VALUES ($valuesQuery)");
        return $statement->execute($fields);
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

        $statement = $this->pdo->prepare("UPDATE {$this->table} SET $fieldQuery WHERE id = :id");
        return $statement->execute($fields);
    }

    /**
     * Supprime un élément
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $statement->execute([$id]);
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

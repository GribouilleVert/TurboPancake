<?php
namespace Haifunime\Blog\Managers;

use Framework\Database\PaginatedQuery;
use Haifunime\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;

final class PostTable {

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * PostTable constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Permet d'obtenir tous les articles dans un encadrement
     * Trie par date de publication (DESC)
     * @param int $maxPerPage
     * @param int $currentPage
     * @return Pagerfanta Articles correspondants a l'interval
     */
    public function findPaginated(int $maxPerPage, int $currentPage): Pagerfanta
    {
        $query = new PaginatedQuery(
            $this->pdo,
            "SELECT * FROM posts ORDER BY created_at DESC ",
            "SELECT count(id) FROM posts",
            Post::class
        );
        return (new Pagerfanta($query))
            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($currentPage);
    }

    /**
     * Permet de trouver un article à partir de son ID
     * @param int $id ID de l'article
     * @return \stdClass|null Données de l'article, null si aucune article n'a été trouvé
     */
    public function find(int $id): ?Post
    {
        $statement = $this->pdo
            ->prepare('SELECT * FROM posts WHERE id = ?');
        $statement->execute([$id]);

        $statement->setFetchMode(\PDO::FETCH_CLASS, Post::class);
        return $statement->fetch() ?: null;
    }

    /**
     * Crée un article
     * @param array $fields
     * @return bool
     */
    public function insert(array $fields): bool
    {
        $columns = array_keys($fields);
        $values = array_map(function ($field) {
            return ':' . $field;
        }, $columns);

        $statement = $this->pdo->prepare(
            "INSERT INTO posts (" . join(', ', $columns) . ") VALUES (" . join(', ', $values) . ")"
        );
        return $statement->execute($fields);
    }

    /**
     * Met a jour un article
     * @param int $id
     * @param array $fields
     * @return bool
     */
    public function update(int $id, array $fields): bool
    {
        $fieldQuery = $this->createFieldQuery($fields);
        $fields['id'] = $id;

        $statement = $this->pdo->prepare("UPDATE posts SET $fieldQuery WHERE id = :id");
        return $statement->execute($fields);
    }

    /**
     * Supprime un article
     * @param int $id
     * @param array $fields
     * @return bool
     */
    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        return $statement->execute([$id]);
    }

    private function createFieldQuery(array $fields): string
    {
        return join(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($fields)));
    }

}

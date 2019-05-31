<?php
namespace Haifunime\Blog\Fetchers;

use Framework\Database\PaginatedQuery;
use Haifunime\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;

class PostTable {

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
        $query = $this->pdo
            ->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$id]);

        if ($query->rowCount() ===0) {
            return null;
        }
        $query->setFetchMode(\PDO::FETCH_CLASS, Post::class);
        return $query->fetch();
    }

}
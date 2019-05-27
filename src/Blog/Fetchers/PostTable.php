<?php
namespace Haifunime\Blog\Fetchers;

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
     * @return \stdClass[] Articles correspondants a l'interval
     */
    public function findPaginated(): array
    {
        return $this->pdo
            ->query('SELECT * FROM posts ORDER BY created_at DESC LIMIT 10')
            ->fetchAll();
    }

    /**
     * Permet de trouver un article à partir de son ID
     * @param int $id ID de l'article
     * @return \stdClass|null Données de l'article, null si aucune article n'a été trouvé
     */
    public function find(int $id): ?\stdClass
    {
        $query = $this->pdo
            ->prepare('SELECT * FROM posts WHERE id = ?');
        $query->execute([$id]);

        if ($query->rowCount() ===0) {
            return null;
        }
        return $query->fetch();
    }

}
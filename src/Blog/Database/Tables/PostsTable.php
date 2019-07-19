<?php
namespace TurboModule\Blog\Database\Tables;

use Pagerfanta\Pagerfanta;
use TurboModule\Blog\Database\Entities\Post;
use TurboPancake\Database\PaginatedQuery;
use TurboPancake\Database\Table;

final class PostsTable extends Table {

    public $privateMode = false;

    protected $table = 'posts';

    protected $entity = Post::class;

    public function findPaginatedByCategory(int $maxPerPage, int $currentPage, int $categoryId): ?Pagerfanta
    {

        if ($this->privateMode) {
            $query = "
                SELECT p.*,  
                       c.name as category_name
                FROM {$this->table} as p
                LEFT JOIN categories as c ON p.category_id = c.id
                ORDER BY p.created_at DESC
            ";
        } else {
            $query = "
            SELECT p.*, 
                   c.name as category_name,
                   c.slug as category_slug
            FROM {$this->table} as p
            LEFT JOIN categories as c ON p.category_id = c.id
            WHERE p.category_id = :catId
            ORDER BY p.created_at DESC
        ";
        }

        $paginatedQuery = new PaginatedQuery(
            $this->pdo,
            $query,
            "SELECT count(id) FROM {$this->table} WHERE category_id = :catId",
            $this->entity,
            [
                'catId' => $categoryId
            ]
        );

        return (new Pagerfanta($paginatedQuery))
            ->setMaxPerPage($maxPerPage)
            ->setCurrentPage($currentPage);
    }

    public function findWithCategory(int $id): Post
    {
        return $this->fetch("
            SELECT p.*, 
                   c.name as category_name,
                   c.slug as category_slug
            FROM {$this->table} as p
            LEFT JOIN categories as c ON p.category_id = c.id
            WHERE p.id = ?
            ", [$id]);
    }

    protected function getPaginationQuery()
    {
        if ($this->privateMode) {
            return "
                SELECT p.id, 
                       p.name, 
                       c.name as category_name
                FROM {$this->table} as p
                LEFT JOIN categories as c ON p.category_id = c.id
                ORDER BY p.created_at DESC
            ";
        }

        return "
            SELECT p.*, 
                   c.name as category_name,
                   c.slug as category_slug
            FROM {$this->table} as p
            LEFT JOIN categories as c ON p.category_id = c.id
            ORDER BY p.created_at DESC
        ";
    }

}

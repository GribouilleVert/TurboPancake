<?php
namespace TurboModule\Blog\Database\Tables;

use Pagerfanta\Pagerfanta;
use TurboModule\Blog\Database\Entities\Post;
use TurboPancake\Database\PaginatedQuery;
use TurboPancake\Database\Query;
use TurboPancake\Database\Table;

final class PostsTable extends Table {

    protected $table = 'posts';

    protected $entity = Post::class;

    public function findPublic(): Query
    {
        $categoryTable = (new CategoriesTable($this->pdo))->getTable();
        return $this->makeQuery()
            ->select('p.*', 'c.name as category_name', 'c.slug as category_slug')
            ->join($categoryTable . ' as c', 'p.category_id = c.id')
            ->order('p.created_at DESC')
            ->where('p.created_at <= NOW()', 'p.private = 0');
    }

    public function findPublicByCategory(int $categoryId): Query
    {
        return $this->findPublic()
            ->where('p.category_id = ?')
            ->parameters([$categoryId]);
    }

    public function findPublicWithCategory(int $id): Post
    {
        return $this->findPublic()
            ->where('p.id = ?')
            ->parameters([$id])
            ->fetch();
    }

    public function findAll(): Query
    {
        $categoryTable = (new CategoriesTable($this->pdo))->getTable();
        return $this->makeQuery()
            ->select('p.*', 'c.name as category_name', 'c.id as category_id')
            ->join($categoryTable . ' as c', 'p.category_id = c.id')
            ->order('p.created_at DESC');
    }

}

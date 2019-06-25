<?php
namespace TurboModule\Blog\Database\Tables;

use TurboModule\Blog\Database\Entities\Post;
use TurboPancake\Database\Table;

final class PostsTable extends Table {

    protected $table = 'posts';

    protected $entity = Post::class;

    protected function getPaginationQuery()
    {
        return "
            SELECT p.id, 
                   p.name, 
                   c.name as category_name
            FROM {$this->table} as p
            LEFT JOIN categories as c ON p.category_id = c.id
            ORDER BY p.created_at DESC
        ";
    }

}

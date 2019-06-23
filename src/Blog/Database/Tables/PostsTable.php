<?php
namespace TurboModule\Blog\Database\Tables;

use TurboModule\Blog\Database\Entities\Post;
use TurboPancake\Database\Table;

final class PostsTable extends Table {

    protected $table = 'posts';

    protected $entity = Post::class;

    protected function getAllQuery()
    {
        return parent::getAllQuery() . " ORDER BY created_at DESC";
    }

}

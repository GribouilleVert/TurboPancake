<?php

use Phinx\Migration\AbstractMigration;

class LinkCategoriesWithPosts extends AbstractMigration {

    public function change()
    {
        $this->table('posts')
            ->addColumn('category_id', 'integer', [
                'null' => true,
                'after' => 'slug'
            ])
            ->addForeignKey('category_id', 'categories', 'id', [
                'delete' => 'SET_NULL'
            ])
            ->update();
    }



}

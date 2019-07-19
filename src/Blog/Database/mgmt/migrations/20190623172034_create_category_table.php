<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoryTable extends AbstractMigration
{

    public function change()
    {
        $this->table('categories')
            ->addColumn('name', 'string')
            ->addColumn('slug', 'string')
            ->addIndex('slug', [])
            ->save();
    }

}

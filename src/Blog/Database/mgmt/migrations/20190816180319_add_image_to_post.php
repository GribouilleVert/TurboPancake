<?php

use Phinx\Migration\AbstractMigration;

class AddImageToPost extends AbstractMigration
{
    public function change()
    {
        $this->table('posts')
            ->addColumn('image', 'string', ['null' => true])
            ->update();
    }
}

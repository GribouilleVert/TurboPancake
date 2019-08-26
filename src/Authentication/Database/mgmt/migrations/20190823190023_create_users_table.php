<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{

    public function up()
    {
        $this->table('users', ['id' => false])
            ->addColumn('id', 'char', ['limit' => 8])
            ->changePrimaryKey('id')
            ->addColumn('username', 'string', ['limit' => 40])
            ->addColumn('email', 'string')
            ->addColumn('password', 'char', ['length' => 60])
            ->addIndex(['username', 'email'], ['unique' => true])
            ->create();
    }

    public function down()
    {
        $this->table('users')->drop()->save();
    }
    
}

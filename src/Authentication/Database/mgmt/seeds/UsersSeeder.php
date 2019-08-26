<?php


use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    public function run()
    {

        $this->table('users')
            ->insert([
                'id' => '00000000',
                'username' => 'admin',
                'email' => 'admin@turbopancake.dev',
                'password' => password_hash('admin', PASSWORD_BCRYPT)
            ])
            ->save();

    }
}

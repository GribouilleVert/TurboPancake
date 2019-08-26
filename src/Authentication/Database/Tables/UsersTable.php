<?php
namespace TurboModule\Authentication\Database\Tables;

use TurboModule\Authentication\User;
use TurboPancake\Database\Table;

class UsersTable extends Table {

    protected $table = 'users';

    protected $entity = User::class;

    public $throwOnNotFound = false;

}

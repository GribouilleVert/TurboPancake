<?php
namespace TurboModule\Blog\Database\Tables;

use TurboPancake\Database\Table;

final class CategoriesTable extends Table {

    protected $table = 'categories';

    protected function getAllQuery()
    {
        return parent::getAllQuery() . " ORDER BY name ASC";
    }

}

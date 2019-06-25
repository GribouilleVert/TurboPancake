<?php
namespace TurboModule\Blog\Database\Tables;

use TurboPancake\Database\Table;

final class CategoriesTable extends Table {

    protected $table = 'categories';

    protected function getPaginationQuery()
    {
        return parent::getPaginationQuery() . " ORDER BY name ASC";
    }

}

<?php
require 'public/index.php';

$migrations = [];
$seeds = [];
foreach ($modules as $module) {
    if (!is_null($module::MIGRATIONS)) {
        $migrations[] = $module::MIGRATIONS;
    }
    if (!is_null($module::SEEDS)) {
        $seeds[] = $module::SEEDS;
    }
}

return [
    'paths' => [
        'migrations' => $migrations,
        'seeds' => $seeds,
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $app->getContainer()->get('database.host'),
            'name' => $app->getContainer()->get('database.name'),
            'user' => $app->getContainer()->get('database.username'),
            'pass' => $app->getContainer()->get('database.password'),
            'port' => 3306,
            'charset' => 'utf8'
        ]
    ],
    'version_order' => 'creation',
];
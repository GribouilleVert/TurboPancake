<?php

require_once '../vendor/autoload.php';

$modules = [
    \Haifunime\Blog\BlogModule::class,
];

$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');
foreach ($modules as $module) {
    if (!is_null($module::DEFINITIONS)) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$builder->addDefinitions(dirname(__DIR__) . '/config.php');
$container = $builder->build();

$app = new Framework\App($container, $modules);

$response = $app->run(GuzzleHttp\Psr7\ServerRequest::fromGlobals());

\Http\Response\send($response);

<?php
define('ROOT', dirname(__DIR__));
require '../vendor/autoload.php';


$modules = [
    \TurboModule\Administration\AdministrationModule::class,
    \TurboModule\Blog\BlogModule::class,
];

$container = (new \TurboPancake\Container\ContainerFactory)($modules);

$app = new TurboPancake\App($container, $modules);

if (php_sapi_name() !== 'cli') {
    $response = $app->run(GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    \Http\Response\send($response);
}

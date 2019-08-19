<?php

use TurboModule\Blog\BlogHelium;

chdir(dirname(__DIR__));


require 'vendor/autoload.php';

$modules = [
    \TurboModule\Administration\AdministrationModule::class,
    \TurboModule\Blog\BlogModule::class,
];

$app = (new TurboPancake\App('config/config.php', $modules))
    ->pipe(\Middlewares\Whoops::class)
    ->pipe(\TurboPancake\Middlewares\TralingSlashMiddleware::class)
    ->pipe(\TurboPancake\Middlewares\MethodDetectorMiddleware::class)
    ->pipe(\TurboPancake\Middlewares\CsrfMiddleware::class)
    ->pipe(\TurboPancake\Middlewares\RouterMiddleware::class)
    ->pipe(\TurboPancake\Middlewares\DispatcherMiddleware::class)
    ->pipe(\TurboPancake\Middlewares\NotFoundMiddleware::class)
;


if (php_sapi_name() !== 'cli') {
    $response = $app->run(GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    \Http\Response\send($response);
}

<?php
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}
require ROOT . '/vendor/autoload.php';

$modules = [
    \TurboModule\Administration\AdministrationModule::class,
    \TurboModule\Blog\BlogModule::class,
];

$app = (new TurboPancake\App(ROOT . '/config/config.php', $modules))
    ->pipe(\Middlewares\Whoops::class)
    ->pipe(\TurboPancake\Middleware\TralingSlashMiddleware::class)
    ->pipe(\TurboPancake\Middleware\MethodDetectorMiddleware::class)
    ->pipe(\TurboPancake\Middleware\CsrfMiddleware::class)
    ->pipe(\TurboPancake\Middleware\RouterMiddleware::class)
    ->pipe(\TurboPancake\Middleware\DispatcherMiddleware::class)
    ->pipe(\TurboPancake\Middleware\NotFoundMiddleware::class)
;


if (php_sapi_name() !== 'cli') {
    $response = $app->run(GuzzleHttp\Psr7\ServerRequest::fromGlobals());
    \Http\Response\send($response);
}

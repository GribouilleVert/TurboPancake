<?php
chdir(dirname(__DIR__));

require 'vendor/autoload.php';

$modules = [
    TurboModule\Administration\AdministrationModule::class,
    TurboModule\Authentication\AuthenticationModule::class,
    TurboModule\Blog\BlogModule::class,
];

$app = new TurboPancake\App('config/config.php', $modules);
$container = $app->getContainer();

$app
    ->trough(Middlewares\Whoops::class)
    ->trough(TurboPancake\Middlewares\TralingSlashMiddleware::class)
    ->trough(TurboModule\Authentication\Middlewares\ForbiddenHandlerMiddleware::class)
    ->trough(TurboModule\Authentication\Middlewares\AuthCheckerMiddleware::class)
    ->trough(TurboPancake\Middlewares\MethodDetectorMiddleware::class)
    ->trough(TurboPancake\Middlewares\CsrfMiddleware::class)
    ->trough(TurboPancake\Middlewares\RouterMiddleware::class)
    ->trough(TurboPancake\Middlewares\DispatcherMiddleware::class)
    ->trough(TurboPancake\Middlewares\NotFoundMiddleware::class)
;

if (php_sapi_name() !== 'cli') {
    $response = $app->run(TurboPancake\App::fromGlobals());
    \Http\Response\send($response);
}

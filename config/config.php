<?php

use Psr\Container\ContainerInterface;
use TurboPancake\Middleware\CsrfMiddleware;
use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Renderer\TwigRendererFactory;
use TurboPancake\Router;
use TurboPancake\Router\RouterFactory;
use TurboPancake\Router\RouterTwigExtension;
use TurboPancake\Services\Session\PHPSession;
use TurboPancake\Services\Session\SessionInterface;
use TurboPancake\Twig\CsrfExtension;
use TurboPancake\Twig\FlashExtension;
use TurboPancake\Twig\FormExtension;
use TurboPancake\Twig\PagerFantaExtension;
use TurboPancake\Twig\TextExtension;
use TurboPancake\Twig\TimeExtension;

return [
    'env'    => \DI\env('ENV', 'production'),

    //Base de donnée
    'database.host' => 'localhost',
    'database.name' => 'TurboPancake',
    'database.username' => 'TurboPancake',
    'database.password' => 'La8zS1tLYuN9PPRz',

    //Twig
    'views.path' => 'views',
    'twig.configuration' => [
        'charset' => 'utf-8'
    ],
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class),
        \DI\get(PagerFantaExtension::class),
        \DI\get(TextExtension::class),
        \DI\get(TimeExtension::class),
        \DI\get(FlashExtension::class),
        \DI\get(FormExtension::class),
        \DI\get(CsrfExtension::class),
    ],

    //Objets globaux
    SessionInterface::class => \DI\autowire(PHPSession::class),
    Router::class => \DI\factory(RouterFactory::class),
    RendererInterface::class => \DI\Factory(TwigRendererFactory::class),
    PDO::class => function (ContainerInterface $c) {
        $pdo = new PDO(
            "mysql:host={$c->get('database.host')};port=3306;dbname={$c->get('database.name')};charset=UTF8",
            $c->get('database.username'),
            $c->get('database.password'),
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        return $pdo;
    },

    //Middlwares
    CsrfMiddleware::class => \DI\autowire()->constructor(\DI\get(SessionInterface::class)),
];
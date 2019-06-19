<?php

use TurboPancake\Renderer\RendererInterface;
use TurboPancake\Renderer\TwigRendererFactory;
use TurboPancake\Router;
use TurboPancake\Router\RouterTwigExtension;
use TurboPancake\Services\Session\PHPSession;
use TurboPancake\Services\Session\SessionInterface;
use TurboPancake\Twig\FlashExtension;
use TurboPancake\Twig\FormExtension;
use TurboPancake\Twig\PagerFantaExtension;
use TurboPancake\Twig\TextExtension;
use TurboPancake\Twig\TimeExtension;

return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => 'root',
    'database.name' => 'TurboPancake',

    'views.path' => dirname(__DIR__) . '/views',
    'twig.configuration' => [
        'debug' => true,
        'charset' => 'utf-8',
        'auto_reload' => true,
    ],
    'twig.extensions' => [
        \DI\get(RouterTwigExtension::class),
        \DI\get(PagerFantaExtension::class),
        \DI\get(TextExtension::class),
        \DI\get(TimeExtension::class),
        \DI\get(FlashExtension::class),
        \DI\get(FormExtension::class),
    ],
    SessionInterface::class => \DI\autowire(PHPSession::class),
    Router::class => \DI\autowire(Router::class),
    RendererInterface::class => \DI\Factory(TwigRendererFactory::class),
    PDO::class => function (\Psr\Container\ContainerInterface $c) {
        $pdo = new PDO(
            "mysql:host={$c->get('database.host')};port=3306;dbname={$c->get('database.name')};charset=UTF8",
            $c->get('database.username'),
            $c->get('database.password'),
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        return $pdo;
    }
];
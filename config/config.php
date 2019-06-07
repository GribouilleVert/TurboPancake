<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;
use Framework\Router\RouterTwigExtension;
use Framework\Services\Session\PHPSession;
use Framework\Services\Session\SessionInterface;
use Framework\Twig\FlashExtension;
use Framework\Twig\PagerFantaExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;

return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => 'root',
    'database.name' => 'haifunime',

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
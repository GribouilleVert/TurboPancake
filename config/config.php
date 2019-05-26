<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;

return [
    'views.path' => dirname(__DIR__) . '/views',
    'twig.configuration' => [
        'debug' => true,
        'charset' => 'utf-8',
        'auto_reload' => true,
    ],
    'twig.extensions' => [
        \DI\get(Router\RouterTwigExtension::class)
    ],
    Router::class => \DI\autowire(Router::class),
    RendererInterface::class => \DI\Factory(TwigRendererFactory::class),
];
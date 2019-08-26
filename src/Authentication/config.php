<?php

use TurboModule\Authentication\Actions\AttemptLoginAction;
use TurboModule\Authentication\Actions\LogoutAction;
use TurboModule\Authentication\AuthTwigExtension;
use TurboModule\Authentication\DatabaseAuther;
use TurboModule\Authentication\Middlewares\ForbiddenHandlerMiddleware;
use TurboPancake\AuthentificationInterface;

return [
    'auth.login' => '/login',
    'auth.logout' => '/logout',

    'auth.afterLogin' => 'admin.dashboard',
    'auth.afterLogout' => 'auth.login',

    'twig.extensions' => \DI\add([
        \DI\get(AuthTwigExtension::class),
    ]),

    AuthentificationInterface::class => \DI\get(DatabaseAuther::class),
    AttemptLoginAction::class => \DI\autowire()->constructorParameter('afterLoginRoute', \DI\get('auth.afterLogin')),
    LogoutAction::class => \DI\autowire()->constructorParameter('afterLogoutRoute', \DI\get('auth.afterLogout')),
    ForbiddenHandlerMiddleware::class => \DI\autowire()->constructorParameter('destinationRoute', \DI\get('auth.login')),
];
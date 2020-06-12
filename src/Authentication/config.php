<?php

use TurboModule\Authentication\Actions\AttemptLoginAction;
use TurboModule\Authentication\Actions\LogoutAction;
use TurboModule\Authentication\AuthTwigExtension;
use TurboModule\Authentication\DatabaseAuther;
use TurboModule\Authentication\DatabaseIdentityChecker;
use TurboModule\Authentication\Middlewares\ForbiddenHandlerMiddleware;
use TurboPancake\Auth\Identity\IdentityCheckerInterface;
use TurboPancake\Auth\UserInterface;
use TurboPancake\Auth\AuthenticationInterface;

return [
    'auth.login' => '/login',
    'auth.logout' => '/logout',

    'auth.afterLogin' => 'admin.dashboard',
    'auth.afterLogout' => 'auth.login',

    'twig.extensions' => \DI\add([
        \DI\get(AuthTwigExtension::class),
    ]),

    AuthenticationInterface::class => \DI\get(DatabaseAuther::class),
    IdentityCheckerInterface::class => \DI\get(DatabaseIdentityChecker::class),
    UserInterface::class => \DI\factory(function (AuthenticationInterface $auth) {
        return $auth->getUser();
    })->parameter('auth', AuthenticationInterface::class),
    AttemptLoginAction::class => \DI\autowire()->constructorParameter('afterLoginRoute', \DI\get('auth.afterLogin')),
    LogoutAction::class => \DI\autowire()->constructorParameter('afterLogoutRoute', \DI\get('auth.afterLogout')),
    ForbiddenHandlerMiddleware::class => \DI\autowire()->constructorParameter('destinationRoute', \DI\get('auth.login')),
];

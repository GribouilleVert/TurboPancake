<?php

use TurboModule\Administration\Actions\DashboardAction;
use TurboModule\Administration\AdministrationModule;
use TurboModule\Administration\AdminTwigExtension;
use TurboModule\Administration\Widgets\HelloAddon;

return [
    'admin.prefix'  => '/cockpit',
    'admin.addons' => [
        \DI\get(HelloAddon::class)
    ],

    AdministrationModule::class => \DI\autowire()->constructorParameter('prefix', \DI\get('admin.prefix')),
    AdminTwigExtension::class => \DI\autowire()->constructorParameter('addons', \DI\get('admin.addons')),
    DashboardAction::class => \DI\autowire()->constructorParameter('addons', \DI\get('admin.addons')),
];

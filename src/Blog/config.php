<?php

use function DI\autowire;
use function DI\get;
use Haifunime\Blog\BlogModule;

return  [
    'blog.prefix' => '/blog',
    BlogModule::class => autowire()->constructorParameter('prefix', get('blog.prefix'))
];

<?php

use TurboModule\Blog\BlogAddon;

return  [
    'blog.prefix' => '/blog',
    'admin.addons' => \DI\add([
        \DI\get(BlogAddon::class)
    ])
];

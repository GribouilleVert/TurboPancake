<?php

use TurboModule\Blog\BlogAddon;
use TurboModule\Blog\BlogHelium;

return  [
    'blog.prefix' => '/blog',
    'admin.addons' => \DI\add([
        \DI\get(BlogAddon::class)
    ]),
];

<?php
namespace Tests\Framework\Modules;

use Framework\Router;

class StringModule {

    public function __construct(Router $router)
    {
        $router->get('/test', function () {
            return 'Looks like a string !';
        }, 'string.trigger');
    }

}
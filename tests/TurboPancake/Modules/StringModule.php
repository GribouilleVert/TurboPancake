<?php
namespace Tests\TurboPancake\Modules;

use TurboPancake\Module;
use TurboPancake\Router;

class StringModule extends Module {

    public function __construct(Router $router)
    {
        $router->get('/test', function () {
            return 'Looks like a string !';
        }, 'string.trigger');
    }

}
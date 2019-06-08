<?php
namespace Tests\TurboPancake\Modules;

use TurboPancake\Module;
use TurboPancake\Router;

class WrongModule extends Module {

    public function __construct(Router $router)
    {
        $router->get('/trigger-error', function () {
            return new \stdClass();
        }, 'error.trigger');
    }

}
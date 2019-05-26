<?php
namespace Tests\Framework\Modules;

use Framework\Module;
use Framework\Router;

class WrongModule extends Module {

    public function __construct(Router $router)
    {
        $router->get('/trigger-error', function () {
            return new \stdClass();
        }, 'error.trigger');
    }

}